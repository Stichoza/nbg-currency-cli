<?php

namespace Stichoza\NbgCurrencyCli;

use Stichoza\NbgCurrency\NbgCurrency;

class Command {

    public function test(string $currency): float
    {
        return (float) NbgCurrency::get($currency);
    }

}