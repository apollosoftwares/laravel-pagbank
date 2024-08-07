<?php

return [
    'sandbox'         => env('PAGBANK_SANDBOX', true),
    'email'           => env('PAGBANK_EMAIL', ''),
    'token'           => env('PAGBANK_TOKEN', ''),
    'notificationURL' => env('PAGBANK_NOTIFICATION', ''),
    'publicKey'       => env('PAGBANK_PUBLIC_KEY', ''),
];
