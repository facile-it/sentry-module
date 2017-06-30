<?php
declare(strict_types=1);

namespace Facile\SentryModule\Options;

interface ConfigurationInterface
{
    /**
     * @return string
     */
    public function getDsn(): string;

    /**
     * @return array
     */
    public function getRavenOptions(): array;

    /**
     * @return string
     */
    public function getRavenJavascriptDsn(): string;

    /**
     * @return string
     */
    public function getRavenJavascriptUri(): string;

    /**
     * @return array
     */
    public function getRavenJavascriptOptions(): array;

    /**
     * @return bool
     */
    public function shouldInjectRavenJavascript(): bool;

    /**
     * @return ErrorHandlerOptionsInterface
     */
    public function getErrorHandlerOptions(): ErrorHandlerOptionsInterface;

    /**
     * @return StackTraceOptionsInterface
     */
    public function getStackTraceOptions(): StackTraceOptionsInterface;
}
