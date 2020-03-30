<?php

use Codedungeon\PHPCliColors\Color;

return [
    Color::bold() . "NBG Currency CLI " . Color::light_green() . "by Stichoza",
    Color::reset() . "  Command-line tool to get currency rates by National Bank of Georgia.",
    PHP_EOL,
    Color::light_yellow() . "Options:" . Color::reset(),
    Color::bold_green() . "  --help         " . Color::reset() . "  Display this help page.",
    Color::bold_green() . "  --plain        " . Color::reset() . "  Display plain results without colors.",
    Color::bold_green() . "  --normalize    " . Color::reset() . "  Convert rates to single entity if rate is given for amount larger than 1.",
    PHP_EOL,
    Color::light_yellow() . "Example commands:" . Color::reset(),
    Color::bold_green() . "  nbg usd        " . Color::reset() . "  Get currency rate and change for USD.",
    Color::bold_green() . "  nbg usd --plain" . Color::reset() . "  Get currency rate for USD.",
    Color::bold_green() . "  nbg usd eur gbp" . Color::reset() . "  Get currency rate for USD . EUR . GBP.",
    Color::bold_green() . "  nbg 150 usd    " . Color::reset() . "  Get equivalent of 150 USD in GEL.",
    Color::bold_green() . "  nbg 150 gel usd" . Color::reset() . "  Get equivalent of 150 GEL in USD.",
];
