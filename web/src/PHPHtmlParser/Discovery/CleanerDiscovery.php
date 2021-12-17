<?php

declare(strict_types=1);

namespace App\PHPHtmlParser\Discovery;

use App\PHPHtmlParser\Contracts\Dom\CleanerInterface;
use App\PHPHtmlParser\Dom\Cleaner;

class CleanerDiscovery
{
    /**
     * @var Cleaner|null
     */
    private static $parser = null;

    public static function find(): CleanerInterface
    {
        if (self::$parser == null) {
            self::$parser = new Cleaner();
        }

        return self::$parser;
    }
}
