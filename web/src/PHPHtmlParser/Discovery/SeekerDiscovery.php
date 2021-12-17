<?php

declare(strict_types=1);

namespace App\PHPHtmlParser\Discovery;

use App\PHPHtmlParser\Contracts\Selector\SeekerInterface;
use App\PHPHtmlParser\Selector\Seeker;

class SeekerDiscovery
{
    /**
     * @var SeekerInterface|null
     */
    private static $seeker = null;

    public static function find(): SeekerInterface
    {
        if (self::$seeker == null) {
            self::$seeker = new Seeker();
        }

        return self::$seeker;
    }
}
