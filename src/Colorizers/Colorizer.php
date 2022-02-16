<?php

namespace Spatie\Visit\Colorizers;

use Symfony\Component\Process\ExecutableFinder;

abstract class Colorizer
{
    public function canColorize(string $contentType): bool
    {
        if ($this->getColorizerToolName() === '') {
            return true;
        }

        return ! empty($this->getColorizerToolPath());
    }

    abstract public function colorize(string $content): string;

    protected function getColorizerToolName(): string
    {
        return '';
    }

    protected function getColorizerToolPath(): string
    {
        $toolName = $this->getColorizerToolName();

        if ($toolName === '') {
            return '';
        }

        return (new ExecutableFinder())->find($toolName, $toolName, [
            '/usr/local/bin',
            '/opt/homebrew/bin',
        ]);
    }
}
