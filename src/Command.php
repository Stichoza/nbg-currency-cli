<?php

namespace Stichoza\NbgCurrencyCli;

use Stichoza\NbgCurrency\NbgCurrency;
use Codedungeon\PHPCliColors\Color;

class Command
{
    protected const PRECISION = 4;

    protected const FALLBACK = 'usd';

    /**
     * @var array Arguments from $argv array
     */
    protected $arguments;

    /**
     * Command constructor.
     *
     * @param array $arguments Arguments from $argv array
     */
    public function __construct(array $arguments = [])
    {
        $this->arguments = $arguments;
    }

    /**
     * Entry point of command
     */
    public function run(): void
    {
        if ($this->hasOption('help')) {
            echo $this->help();
        } elseif (is_numeric($this->arguments[0] ?? null)) {
            echo $this->converted();
        } else {
            echo $this->list();
        }

        echo PHP_EOL;
    }

    /**
     * Get currency raw object.
     *
     * @param string $currency Currency to get
     * @param bool $normalize Normalize amounts from rate
     *
     * @return object
     */
    protected function get(string $currency, bool $normalize = false): object
    {
        $data = (object) NbgCurrency::get($currency);

        if ($normalize || $this->hasOption('normalize') || $this->hasOption('normalized')) {
            $multiplier = ((int) $data->description) ?: 1; // Parse multiplier from description

            $data->rate /= $multiplier;
            $data->diff /= $multiplier;
            $data->description = preg_replace('/^\d+\s/', '1 ', $data->description);
        }

        return $data;
    }

    /**
     * Get calculated currency rate
     *
     * @param string $currency Currency to convert
     * @param float $amount Amount to convert
     * @param bool $inverse Amount given in local currency, return converted to $currency
     * @param bool $normalize Normalize amounts from rate
     *
     * @return float Converted amount
     */
    protected function rate(string $currency, float $amount = 1, bool $inverse = false, bool $normalize = false): float
    {
        $rate = $this->get($currency, $normalize)->rate ?? 0;

        if ($inverse) {
            return round($amount / ($rate ?: 1), self::PRECISION);
        }

        return round($rate * $amount, self::PRECISION);
    }

    /**
     * Whether the command has option passed or not.
     *
     * @param string $option Option name
     *
     * @return bool
     */
    protected function hasOption(string $option): bool
    {
        return in_array('--' . $option, $this->arguments, true);
    }

    /**
     * Get converted amount
     *
     * @return string Results
     */
    protected function converted(): string
    {
        [$first, $second, $third] = $this->arguments;

        if ($second === 'gel' || $second === 'to') {
            return $this->rate($third ?? self::FALLBACK, $first, true, true);
        }

        return $this->rate($second ?? self::FALLBACK, $first, false, true);
    }

    /**
     * Get list of currencies
     *
     * @return string Results
     */
    protected function list(): string
    {
        $results = [];

        $arguments = array_filter($this->arguments, function ($argument) {
            return strpos($argument, '--') !== 0;
        }) ?: [self::FALLBACK];

        foreach ($arguments as $c) {
            if (!$this->hasOption('plain')) {
                $currency = $this->get($c);

                $results[] = strtoupper($c) . ': '
                    . Color::bold() . round($currency->rate, self::PRECISION) . Color::reset() . ' '
                    . [Color::light_green(), '', Color::red()][$currency->change + 1]
                    . ['▼', '', '▲'][$currency->change + 1] . ' '
                    . round(abs($currency->diff), self::PRECISION)
                    . Color::reset() . Color::gray() . ' (' . $currency->description . ')' . Color::reset();
            } else {
                $results[] = round($this->get($c, true)->rate, self::PRECISION);
            }
        }

        return implode(PHP_EOL, $results);
    }

    /**
     * Get help page
     *
     * @return string Help page
     */
    protected function help(): string
    {
        $lines = require __DIR__ . '/../resources/help.php';

        return implode(PHP_EOL, $lines);
    }
}