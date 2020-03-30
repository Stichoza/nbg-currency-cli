<?php

namespace Stichoza\NbgCurrencyCli;

use Stichoza\NbgCurrency\NbgCurrency;
use Codedungeon\PHPCliColors\Color;

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
                echo $this->rate($third ?? self::FALLBACK, $first, true) . PHP_EOL;
            } else {
                echo $this->rate($second ?? self::FALLBACK, $first) . PHP_EOL;
            }
        } else {
            foreach ($this->arguments ?? [self::FALLBACK] as $c) {
                if (substr($c, 0, 2) === '--') {
                    continue;
                }

                $currency = $this->get($c);

                if (!$this->hasOption('plain')) {
                    echo strtoupper($c) . ': ';
                    echo Color::BOLD . $currency->rate . Color::RESET . ' ';
                    echo [Color::GREEN, '', Color::RED][$currency->change + 1];
                    echo ['▼', '', '▲'][$currency->change + 1] . ' ';
                    echo abs($currency->diff);
                    echo Color::GRAY . ' (' . $currency->description . ')' . Color::RESET;
                } else {
                    echo $currency->rate;
                }

                echo PHP_EOL;
            }
        }

    }

    protected function get($currency): object
    {
        $data = (object) NbgCurrency::get($currency);

        if ($this->hasOption('normalize')) {
            $multiplier = ((int) $data->description) ?: 1; // Parse multiplier from description

            $data->rate /= $multiplier;
            $data->diff /= $multiplier;
            $data->description = preg_replace('/^\d+\s/', '1 ', $data->description);
        }

        return $data;
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