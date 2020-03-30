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
        if ($this->hasOption('help')) {
            echo Color::bold(), "NBG Currency CLI ", Color::reset(), Color::green(), "by Stichoza\n", Color::reset(),
                "  Command-line tool to get currency rates by National Bank of Georgia.\n\n",
                Color::yellow(), "Options:", Color::reset(), "\n",
                Color::bold_green(), "  --help         ", Color::reset(), "  Display this help page.\n",
                Color::bold_green(), "  --plain        ", Color::reset(), "  Display plain results without colors.\n",
                Color::bold_green(), "  --normalize    ", Color::reset(), "  Convert rates to single entity if rate is given for amount larger than 1.\n\n",
                Color::yellow(), "Example commands:", Color::reset(), "\n",
                Color::bold_green(), "  nbg usd        ", Color::reset(), "  Get currency rate and change for USD.\n",
                Color::bold_green(), "  nbg usd --plain", Color::reset(), "  Get currency rate for USD.\n",
                Color::bold_green(), "  nbg usd eur gbp", Color::reset(), "  Get currency rate for USD, EUR, GBP.\n",
                Color::bold_green(), "  nbg 150 usd    ", Color::reset(), "  Get equivalent of 150 USD in GEL.\n",
                Color::bold_green(), "  nbg 150 gel usd", Color::reset(), "  Get equivalent of 150 GEL in USD.\n";
            return;
        }

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