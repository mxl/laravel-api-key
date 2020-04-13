<?php

namespace MichaelLedin\LaravelApiKey;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;

class AuthorizeApiKey
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        $settings = $this->init();

        if ($settings) {
            list($secret, $timestampHeader, $window, $tokenHeader) = $settings;
            $timestamp = $request->header($timestampHeader);
            $token = $request->header($tokenHeader);

            if (!($token && $timestamp && ($timestampInstant = $this->parseTimestamp($timestamp)) &&
                $this->checkTimestamp($timestampInstant, $window) && $this->checkToken($secret, $timestamp, $token))) {
                return $this->makeResponse($timestamp, $token);
            }
        }
        return $next($request);
    }

    protected function parseTimestamp(string $value)
    {
        return Carbon::parse($value);
    }

    protected function checkTimestamp(Carbon $timestamp, int $window)
    {
        return Carbon::now()->diffInSeconds($timestamp) <= $window;
    }

    protected function createToken(string $secret, string $timestamp)
    {
        return hash($this->checkConfigurationPropertyIsNotEmptyString('hash'), $secret . $timestamp);
    }

    protected function checkToken(string $secret, string $timestamp, string $token)
    {
        return $token === $this->createToken($secret, $timestamp);
    }

    /** @noinspection PhpUnusedParameterInspection */
    protected function makeResponse(?string $timestamp, ?string $token)
    {
        return response(null, Response::HTTP_UNAUTHORIZED);
    }

    protected function init()
    {
        $secret = config('apiKey.secret');
        if ($secret) {
            $timestampHeader = $this->checkConfigurationPropertyIsNotEmptyString('timestampHeader');
            $window = $this->checkConfigurationPropertyIsPositiveInteger('window');
            $tokenHeader = $this->checkConfigurationPropertyIsNotEmptyString('tokenHeader');
            return [$secret, $timestampHeader, $window, $tokenHeader];
        }
        return false;
    }

    protected function checkConfigurationProperty(string $property, callable $check)
    {
        $value = config('apiKey.' . $property);
        if (($message = $check($value))) {
            throw new InvalidArgumentException($message);
        }
        return $value;
    }

    protected function checkConfigurationPropertyMustBe(string $property, string $mustBe)
    {
        return $this->checkConfigurationProperty($property, function ($value) use ($property, $mustBe) {
            if (!$value || is_array($value) && empty($value)) {
                return "${property} configuration property must be ${mustBe}.";
            }
            return null;
        });
    }

    protected function checkConfigurationPropertyIsNotEmptyString(string $property)
    {
        return $this->checkConfigurationPropertyMustBe($property, 'not empty string');
    }

    protected function checkConfigurationPropertyIsPositiveInteger(string $property)
    {
        return $this->checkConfigurationPropertyMustBe($property, 'positive integer');
    }
}
