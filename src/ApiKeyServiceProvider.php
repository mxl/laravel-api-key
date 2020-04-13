<?php


namespace MichaelLedin\LaravelApiKey;


use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class ApiKeyServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Router $router)
    {
        $this->publishes([
            $this->getConfigFile() => config_path('apiKey.php'),
        ], 'config');

        $this->registerMiddleware($router);
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->getConfigFile(),
            'excel'
        );
    }

    protected function registerMiddleware(Router $router)
    {
        $router->aliasMiddleware('apiKey', AuthorizeApiKey::class);
    }

    /**
     * @return string
     */
    protected function getConfigFile(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'apiKey.php';
    }
}
