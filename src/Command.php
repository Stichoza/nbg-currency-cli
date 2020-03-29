<?php

namespace Stichoza\NbgCurrencyCli;

use Stichoza\NbgCurrency\NbgCurrency;

class Command {

    const PRECISION = 4;

    const FALLBACK = 'usd';

    /**
     * @var array
     */
    protected $arguments;

    public function __construct($arguments = [])
    {
        $this->arguments = $arguments;
    }

    public function run(): void
    {
        [$first, $second, $third] = $this->arguments;

        if (is_numeric($first)) {
            if ($second === 'gel' || $second === 'to') {
                echo $this->rate($third ?? self::FALLBACK, $first, true);
            } else {
                echo $this->rate($second ?? self::FALLBACK, $first);
            }
        } else {
            $currency = $this->get($first ?? self::FALLBACK);

            echo $currency->rate;

            if (!$this->hasOption('plain')) {
                echo " ";
                echo ['-', ':', '+'][$currency->change - 1];
                echo $currency->diff;
            }
        }

        echo PHP_EOL;
    }

    protected function get($currency): object
    {
        return (object) NbgCurrency::get($currency);
    }

    protected function rate($currency, float $amount = 1, bool $inverse = false)
    {
        $rate = $this->get($currency)->rate ?? 0;

        if ($inverse) {
            return round($amount / ($rate ?: 1), self::PRECISION);
        } else {
            return $rate * $amount;
        }
    }

    /**
     * If the command hac option passed
     *
     * @param string $option Option name
     *
     * @return bool
     */
    protected function hasOption($option): bool
    {
        return in_array('--' . $option, $this->arguments);
    }

}