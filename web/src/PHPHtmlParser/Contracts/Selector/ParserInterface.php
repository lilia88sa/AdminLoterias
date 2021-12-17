<?php

declare(strict_types=1);

namespace App\PHPHtmlParser\Contracts\Selector;

use App\PHPHtmlParser\DTO\Selector\ParsedSelectorCollectionDTO;

interface ParserInterface
{
    public function parseSelectorString(string $selector): ParsedSelectorCollectionDTO;
}
