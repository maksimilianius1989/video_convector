<?php

return [
    'settings' => [
        'addContentLengthHeader' => false,
        'displayErrorDetails' => (bool)getenv('API_DEBUG'),
    ],
];