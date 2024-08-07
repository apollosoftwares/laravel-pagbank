<?php

namespace Apollosoftwares\Pagbank;

use Illuminate\Support\Facades\Http;

class PagBankClient extends PagBankConfig
{
    protected function execute(
        $parameters,
        $url
    ) {
        return Http::withToken($this->token)
                    ->withBody(json_encode($parameters), 'application/json')
                    ->post($url)
                    ->json();
    }

    protected function validate($data, $rules)
    {
        $data = array_filter($data);

        $validator = $this->validator->make($data, $rules);

        if ($validator->fails()) {
            throw new PagBankException($validator->messages()->first(), 1003);
        }
    }
}
