<p align="center">
 <img src="https://assets.pagseguro.com.br/ps-bootstrap/v7.3.1/svg/pagbank/logo-pagbank.svg">
</p>

### Atualmente essa é forma de importar

```
"repositories": [{
    "type": "git",
    "url": "https://github.com/apollosoftwares/laravel-pagbank"
}],
"require": {
    "apollosoftwares/pagbank": "dev-master"
}
```

## Exemplo de uso
### BOLETO

```php

use PagBank //Utilize a Facade

PagBank::setReferenceId('Pedido 123')
        ->setCustomerInfo([
            'name'   => 'Nome',
            'email'  => 'Email',
            'tax_id' => '99999999999'
        ])
        ->setPhoneInfo([
            [
                'country' => '55',
                'area'    => '66',
                'number'  => '9999999999',
                'type'    => 'MOBILE'
            ]
        ])
        ->setItems([
            [
                'reference_id' => 'ID',
                'name' => 'Nome do Item',
                'unit_amount' => 12.50,
                'quantity' => 1
            ],
            [
                'reference_id' => 'ID',
                'name' => 'Nome do Item 2',
                'unit_amount' => 13.50,
                'quantity' => 2
            ]
        ])
        ->setShippingAddress(
            [
                'street' => 'Rua/Avenida',
                'number' => 'Número',
                'locality' => 'Bairro',
                'city' => 'Cidade',
                'region_code' => 'UF',
                'country' =>  'BRA',
                'postal_code' => '12345678'
            ]
        )
        ->setPaymentMethod([
            'type' => 'BANK_SLIP',
            'charges' => [
                'reference_id' => 'Pedido 123',
                'description' => 'Pedido 123',
                'due_date' => '2024-08-07',
                'amount' => [
                    'value' => 100.00,
                    'currency' => 'BRL'
                ]
            ]
        ])
        ->send();

```
