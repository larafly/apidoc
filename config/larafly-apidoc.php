<?php

return [
    // the url to access
    'route' => env('API_DOC_ROUTE', 'apidoc'),
    'datetime_format' => 'Y-m-d H:i:s',
    'author' => env('GENERATOR_AUTHOR', 'system'),
    'is_show' => env('API_DOC_SHOW', false),
];
