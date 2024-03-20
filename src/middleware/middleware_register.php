<?php

return [
    'global' => [
        src\middleware\SessionsSetupMiddleware::class,
        src\middleware\CSRFMiddleware::class,
    ],
    'aliases' => [
        'auth' => src\middleware\AuthenticatedMiddleware::class,
        'guest' => src\middleware\GuestMiddleware::class,
        'signature'=> src\middleware\SignatureValidationMiddleware::class,
        'verify'=> src\middleware\EmailVerifiedMiddleware::class,
        'register'=> src\middleware\RegisterMiddleware::class,
    ]
];
