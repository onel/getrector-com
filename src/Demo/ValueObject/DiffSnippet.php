<?php

declare(strict_types=1);

namespace App\Demo\ValueObject;

/**
 * Represents a snippet of code from a diff with its associated line number.
 */
final readonly class DiffSnippet
{
    /**
     * Initializes a new diff snippet with a line number and content.
     *
     * @param int $line The line number where the snippet appears
     * @param string $snippet The content of the code snippet
     */
    public function __construct(
        private int $line,
        private string $snippet
    ) {
    }

    /**
     * Returns the line number where the snippet appears.
     *
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * Returns the content of the code snippet.
     *
     * @return string
     */
    public function getSnippet(): string
    {
        return $this->snippet;
    }
}
