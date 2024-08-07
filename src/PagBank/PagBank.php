<?php

namespace Apollosoftwares\Pagbank;

use Helpers\ConvertToCents;
use Helpers\RemoveAccents;
use Helpers\ContainsKeys;

class PagBank extends PagBankClient
{
    protected $referenceId     = '';
    protected $type            = 'BOLETO';

    protected $customerInfo    = [];
    protected $phoneInfo       = [];
    protected $items           = [];
    protected $shippingAddress = [];
    protected $charges         = [];

    public const PHONES = [
        "country" => "",
        "area" => "",
        "number" => "",
        "type" => ""
    ];

    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
        return $this;
    }

    public function setCustomerInfo(array $customerInfo)
    {
        $customer = [
           "name"   => RemoveAccents::run($customerInfo["name"]),
           "email"  => $customerInfo["email"],
           "tax_id" => $customerInfo["tax_id"],
        ];

        $this->customerInfo = $customer;

        return $this;
    }

    public function setPhoneInfo(array $phoneInfo)
    {
        $key = ContainsKeys::run($phoneInfo, self::PHONES);

        if(!$key) {
            return 1;
        }

        $contacts = [];
        foreach($phoneInfo as $phone) {
            $contacts[] = $phone;
        }

        $this->phoneInfo = $contacts;
        return $this;
    }

    public function setItems(array $items)
    {
        $newItems = [];
        foreach($items as $item) {
            $item['name'] = RemoveAccents::run($item['name']);

            $newItems[] = $item;
        }

        $this->items = $newItems;

        return $this;
    }

    public function setShippingAddress(array $shippings)
    {
        $this->shippingAddress = $shippings;
        return $this;
    }

    public function setPaymentMethod(array $paymentMethods)
    {
        $paymentMethods  = (object) $paymentMethods;

        $this->type = $paymentMethods->type;

        $charges = [];
        switch ($paymentMethods->type) {
            case 'BANK_SLIP':
                $charges = $this->chargeBankSlip(
                    $paymentMethods->charges
                );
                break;
            case 'CREDIT_CARD':
                $charges = $this->chargeCreditCard(
                    $paymentMethods->charges
                );
                break;
            case 'PIX':
                $charges = $this->chargePIX(
                    $paymentMethods->charges
                );
                break;
            default:
                break;
        }

        $this->charges = $charges;

        return $this;
    }

    public function send()
    {
        $data = [
            'reference_id' => $this->referenceId,
            'customer'     => $this->customerInfo,
            'phones'       => $this->phoneInfo,
            'items'        => $this->items,
            'shipping' => [
                'address' => $this->shippingAddress
            ],
            'notification_urls' => [
                $this->notificationURL
            ]
        ];

        switch ($this->type) {
            case 'BANK_SLIP':
            case 'CREDIT_CARD':
                $data['charges'][] = $this->charges;
                break;
            case 'PIX':
                $data['qr_codes'][] = $this->charges;
                break;
        }

        return $this->execute(
            $data,
            $this->url['orders']
        );
    }

    // https://developer.pagbank.com.br/reference/criar-pagar-pedido-com-boleto
    private function chargeBankSlip(array $charges)
    {
        $newCharges = [];

        $newCharges['reference_id'] = $charges['reference_id'];
        $newCharges['description']  = $charges['description'];

        $newCharges['amount']['value'] = ConvertToCents::run($charges['amount']['value']);
        $newCharges['amount']['currency'] = $charges['amount']['currency'] ?? 'BRL';

        $newCharges['payment_method']['type']               = 'BOLETO';
        $newCharges['payment_method']['boleto']['due_date'] = $charges['due_date'];

        if(isset($charges['instruction_lines']) && sizeof($charges['instruction_lines']) > 1) {
            $newCharges['payment_method']['boleto']['instruction_lines']['line_1'] = $charges['instruction_lines']['line_1'] ?? '';
            $newCharges['payment_method']['boleto']['instruction_lines']['line_2'] = $charges['instruction_lines']['line_2'] ?? '';
        }

        $newCharges['payment_method']['boleto']['holder']['name']                   = $charges['holder']['name'] ?? RemoveAccents::run($this->customerInfo['name']);
        $newCharges['payment_method']['boleto']['holder']['tax_id']                 = $charges['holder']['tax_id'] ?? $this->customerInfo['tax_id'];
        $newCharges['payment_method']['boleto']['holder']['email']                  = $charges['holder']['email'] ?? $this->customerInfo['email'];
        $newCharges['payment_method']['boleto']['holder']['address']['country']     = $charges['holder']['address']['country'] ?? $this->shippingAddress['country'];
        $newCharges['payment_method']['boleto']['holder']['address']['region_code'] = $charges['holder']['address']['region'] ?? $this->shippingAddress['region_code'];
        $newCharges['payment_method']['boleto']['holder']['address']['region']      = $charges['holder']['address']['region'] ?? $this->shippingAddress['city'];
        $newCharges['payment_method']['boleto']['holder']['address']['city']        = $charges['holder']['address']['city'] ?? $this->shippingAddress['city'];
        $newCharges['payment_method']['boleto']['holder']['address']['postal_code'] = $charges['holder']['address']['postal_code'] ?? $this->shippingAddress['postal_code'];
        $newCharges['payment_method']['boleto']['holder']['address']['street']      = $charges['holder']['address']['street'] ?? $this->shippingAddress['street'];
        $newCharges['payment_method']['boleto']['holder']['address']['number']      = $charges['holder']['address']['number'] ?? $this->shippingAddress['number'];
        $newCharges['payment_method']['boleto']['holder']['address']['locality']    = $charges['holder']['address']['locality'] ?? $this->shippingAddress['locality'];

        return $newCharges;
    }

    // https://developer.pagbank.com.br/reference/criar-pagar-pedido-com-cartao
    private function chargeCreditCard(array $charges)
    {
        $newCharges = [];

        $newCharges['reference_id'] = $charges['reference_id'];
        $newCharges['description']  = $charges['description'];

        $newCharges['amount']['value'] = ConvertToCents::run($charges['amount']['value']);
        $newCharges['amount']['currency'] = $charges['amount']['currency'] ?? 'BRL';

        $newCharges['payment_method']['type']              = 'CREDIT_CARD';
        $newCharges['payment_method']['installments']      = $charges['installments'] ?? 1;
        $newCharges['payment_method']['capture']           = $charges['capture'] ?? true;
        $newCharges['payment_method']['card']['encrypted'] = $charges['card']['encrypted'];
        $newCharges['payment_method']['card']['store']     = $charges['card']['store'];

        $newCharges['holder']['name']   = $charges['holder']['name'] ?? RemoveAccents::run($this->customerInfo['name']);
        $newCharges['holder']['tax_id'] = $charges['holder']['tax_id'] ?? $this->customerInfo['tax_id'];

        return $newCharges;
    }

    // https://developer.pagbank.com.br/reference/criar-pedido-pedido-com-qr-code
    private function chargePIX(array $qrCode)
    {
        $newCharges = [];

        $newCharges['amount']['value'] = ConvertToCents::run($qrCode['amount']['value']);
        $newCharges['expiration_date'] = $qrCode['expiration_date'];

        return $newCharges;
    }
}
