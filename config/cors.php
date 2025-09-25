<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_origins' => ['http://localhost:5173'],
    'allowed_headers' => ['*'],
    'allowed_methods' => ['*'],
    'supports_credentials' => true,
    'allowed_credentials' => true,
];
