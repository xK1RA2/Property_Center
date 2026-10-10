<?php

/*
| Cross-Origin Resource Sharing for the JSON API.
|
| The mobile app is not a browser and ignores CORS. This only matters for a web front end on another
| origin. By default NO other origin may call the API from a browser; list trusted ones in
| CORS_ALLOWED_ORIGINS, e.g. "https://app.example.com,https://admin.example.com".
*/
return [
    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => array_values(array_filter(array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Accept', 'Authorization', 'Content-Type', 'X-Requested-With'],

    'exposed_headers' => [],

    'max_age' => 600,

    // tokens travel in the Authorization header, never in cookies
    'supports_credentials' => false,
];
