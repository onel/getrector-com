<?php

declare(strict_types=1);

namespace App\RuleFilter\ValueObject;

/**
 * Represents a configured diff sample with its associated configuration.
 *
 * @api used in blade
 */
final readonly class ConfiguredDiffSample
{
    /**
     * Initializes the configured diff sample with code and configuration.
     */
    public function __construct(
        private string $diffCodeSample,
        private string $configuration
    ) {
    }

    /**
     * Retrieves the diff code sample.
     *
     * @return string
     */
    public function getDiffCodeSample(): string
    {
        return $this->diffCodeSample;
    }

    /**
     * Retrieves the configuration string.
     *
     * @return string
     */
    public function getConfiguration(): string
    {
        return $this->configuration;
    }
}
