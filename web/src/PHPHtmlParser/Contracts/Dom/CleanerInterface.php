<?php

namespace App\PHPHtmlParser\Contracts\Dom;

use App\PHPHtmlParser\Exceptions\LogicalException;
use App\PHPHtmlParser\Options;

interface CleanerInterface
{
    /**
     * Cleans the html of any none-html information.
     *
     * @throws LogicalException
     */
    public function clean(string $str, Options $options, string $defaultCharset): string;
}
