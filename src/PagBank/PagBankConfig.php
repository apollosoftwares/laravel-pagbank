<?php

namespace Apollosoftwares\Pagbank;

use Illuminate\Validation\Factory as Validator;
use Psr\Log\LoggerInterface as Log;

class PagBankConfig
{
    protected $log;
    protected $validator;
    protected $sandbox;
    protected $token;
    protected $email;
    protected $notificationURL;
    protected $url = [];

    public function __construct(
        Log $log,
        Validator $validator
    ) {
        $this->log       = $log;
        $this->validator = $validator;

        $this->setEnvironment();
        $this->setUrl();
    }

    private function setEnvironment()
    {
        $this->sandbox         = config('pagbank.sandbox', env('PAGBANK_SANDBOX', true));
        $this->email           = config('pagbank.email', env('PAGBANK_EMAIL', ''));
        $this->token           = config('pagbank.token', env('PAGBANK_TOKEN', ''));
        $this->notificationURL = config('pagbank.notificationURL', env('PAGBANK_NOTIFICATION', ''));
    }

    private function setUrl()
    {
        $sandbox = $this->sandbox ? 'sandbox.' : '';

        $url = [
            'javascript' => 'https://assets.pagseguro.com.br/checkout-sdk-js/rc/dist/browser/pagseguro.min.js',
            'orders'     => "https://{$sandbox}api.pagseguro.com/orders",
        ];

        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
