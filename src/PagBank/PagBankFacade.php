<?php

namespace Apollosoftwares\Pagbank;

use Illuminate\Support\Facades\Facade;

class PagBankFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pagbank';
    }
}
