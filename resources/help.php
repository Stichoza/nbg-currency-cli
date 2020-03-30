<?php

use Codedungeon\PHPCliColors\Color;

return Color::bold() . "NBG Currency CLI " . Color::light_green() . "by Stichoza\n" .
    Color::reset() . "  Command-line tool to get currency rates by National Bank of Georgia.\n\n" .
    Color::light_yellow() . "Options:" . Color::reset() . "\n" .
    Color::bold_green() . "  --help         " . Color::reset() . "  Display this help page.\n" .
    Color::bold_green() . "  --plain        " . Color::reset() . "  Display plain results without colors.\n" .
    Color::bold_green() . "  --normalize    " . Color::reset() . "  Convert rates to single entity if rate is given for amount larger than 1.\n\n" .
    Color::light_yellow() . "Example commands:" . Color::reset() . "\n" .
    Color::bold_green() . "  nbg usd        " . Color::reset() . "  Get currency rate and change for USD.\n" .
    Color::bold_green() . "  nbg usd --plain" . Color::reset() . "  Get currency rate for USD.\n" .
    Color::bold_green() . "  nbg usd eur gbp" . Color::reset() . "  Get currency rate for USD . EUR . GBP.\n" .
    Color::bold_green() . "  nbg 150 usd    " . Color::reset() . "  Get equivalent of 150 USD in GEL.\n" .
    Color::bold_green() . "  nbg 150 gel usd" . Color::reset() . "  Get equivalent of 150 GEL in USD.\n";
