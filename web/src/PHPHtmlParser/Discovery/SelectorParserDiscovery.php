<?php

declare(strict_types=1);

namespace App\PHPHtmlParser\Discovery;

use App\PHPHtmlParser\Contracts\Selector\ParserInterface;
use App\PHPHtmlParser\Selector\Parser;

class SelectorParserDiscovery
{
    /**
     * @var ParserInterface|null
     */
    private static $parser = null;

    public static function find(): ParserInterface
    {
        if (self::$parser == null) {
            self::$parser = new Parser();
        }

        return self::$parser;
    }
}
