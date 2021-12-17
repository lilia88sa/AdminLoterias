<?php

namespace App\PHPHtmlParser\Contracts\Dom;

use App\PHPHtmlParser\Content;
use App\PHPHtmlParser\Dom\Node\AbstractNode;
use App\PHPHtmlParser\Exceptions\ChildNotFoundException;
use App\PHPHtmlParser\Exceptions\CircularException;
use App\PHPHtmlParser\Exceptions\ContentLengthException;
use App\PHPHtmlParser\Exceptions\LogicalException;
use App\PHPHtmlParser\Exceptions\StrictException;
use App\PHPHtmlParser\Options;

interface ParserInterface
{
    /**
     * Attempts to parse the html in content.
     *
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws StrictException
     */
    public function parse(Options $options, Content $content, int $size): AbstractNode;

    /**
     * Attempts to detect the charset that the html was sent in.
     *
     * @throws ChildNotFoundException
     */
    public function detectCharset(Options $options, string $defaultCharset, AbstractNode $root): bool;
}
