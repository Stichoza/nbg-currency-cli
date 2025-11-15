<?php

declare(strict_types=1);

namespace Stichoza\NbgCurrencyCli;

use Exception;
use Stichoza\NbgCurrency\NbgCurrency;
use Codedungeon\PHPCliColors\Color;

class Command
{
    protected const PRECISION = 4;

    protected const FALLBACK = 'usd';

    /**
     * @var array Arguments from $argv array
     */
    protected array $arguments;

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
    public function run(): int
    {
        try {
            if ($this->hasOption('help')) {
                echo $this->help();
            } elseif (is_numeric($this->arguments[0] ?? null)) {
                echo $this->converted();
            } else {
                echo $this->list();
            }
        } catch (Exception $e) {
            echo Color::red() . Color::bold() . 'Error: ' . Color::reset() . $e->getMessage() . PHP_EOL;
            return 1;
        }

        echo PHP_EOL;
        return 0;
    }

    /**
     * Get currency raw object.
     *
     * @param string $currency Currency to get
     *
     * @return object
     * @throws \Stichoza\NbgCurrency\Exceptions\CurrencyNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\DateNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\InvalidDateException
     * @throws \Stichoza\NbgCurrency\Exceptions\LanguageNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\RequestFailedException
     */
    protected function get(string $currency): object
    {
        return NbgCurrency::get($currency);
    }

    /**
     * Get calculated currency rate
     *
     * @param string $currency Currency to convert
     * @param float $amount Amount to convert
     * @param bool $inverse Amount given in local currency, return converted to $currency
     *
     * @return float Converted amount
     * @throws \Stichoza\NbgCurrency\Exceptions\CurrencyNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\DateNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\InvalidDateException
     * @throws \Stichoza\NbgCurrency\Exceptions\LanguageNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\RequestFailedException
     */
    protected function rate(string $currency, float $amount = 1, bool $inverse = false): float
    {
        $rate = $this->get($currency)->rate ?? 0;

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
     * @return float Results
     * @throws \Stichoza\NbgCurrency\Exceptions\CurrencyNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\DateNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\InvalidDateException
     * @throws \Stichoza\NbgCurrency\Exceptions\LanguageNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\RequestFailedException
     */
    protected function converted(): float
    {
        [$first, $second, $third] = $this->arguments;

        if ($second === 'gel' || $second === 'to') {
            return $this->rate($third ?? self::FALLBACK, $first, true);
        }

        return $this->rate($second ?? self::FALLBACK, $first);
    }

    /**
     * Get a list of currencies
     *
     * @return string Results
     * @throws \Stichoza\NbgCurrency\Exceptions\CurrencyNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\DateNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\InvalidDateException
     * @throws \Stichoza\NbgCurrency\Exceptions\LanguageNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\RequestFailedException
     */
    protected function list(): string
    {
        $results = [];

        $arguments = array_filter($this->arguments, static fn ($argument) => !str_starts_with($argument, '--'))
            ?: [self::FALLBACK];

        foreach ($arguments as $c) {
            if (!$this->hasOption('plain')) {
                $currency = $this->get($c);

                $results[] = strtoupper($c) . ': '
                    . Color::bold() . round($currency->rate, self::PRECISION) . Color::reset() . ' '
                    . [Color::light_green(), '', Color::red()][$currency->change + 1]
                    . ['▼', '', '▲'][$currency->change + 1] . ' '
                    . round(abs($currency->diff), self::PRECISION)
                    . Color::reset() . Color::gray() . ' (' . $currency->name . ')' . Color::reset();
            } else {
                $results[] = round($this->get($c)->rate, self::PRECISION);
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