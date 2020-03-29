<?php

namespace Stichoza\NbgCurrencyCli;

use Stichoza\NbgCurrency\NbgCurrency;

class Command {

    const PRECISION = 4;

    const FALLBACK = 'usd';

    public function run($first = null, $second = null, $third = null): void
    {
        if (is_numeric($first)) {
            if ($second === 'gel' || $second === 'to') {
                echo round($first / $this->rate($third ?? self::FALLBACK), self::PRECISION);
            } else {
                echo $first * $this->rate($second ?? self::FALLBACK);
            }
        } else {
            echo $this->rate($first ?: self::FALLBACK);
        }

        echo PHP_EOL;
    }

    public function rate($currency): float
    {
        return (float) NbgCurrency::rate($currency);
    }

}