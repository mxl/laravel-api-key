<?php

return [
    'secret' => env('API_KEY_SECRET'),

    'hash' => env('API_KEY_HASH', 'md5'),
    'timestampHeader' => env('API_KEY_TIMESTAMP_HEADER', 'X-Timestamp'),
    'tokenHeader' => env('API_KEY_TOKEN_HEADER', 'X-Authorization'),
    'window' => env('API_KEY_WINDOW', 30),
];
