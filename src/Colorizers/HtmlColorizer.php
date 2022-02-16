<?php

namespace Spatie\Visit\Colorizers;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class HtmlColorizer
{
    public function canRun(): bool
    {
        return ! empty($this->getBatPath());
    }

    public function colorize(string $html): string
    {
        $file = tmpfile();
        $path = stream_get_meta_data($file)['uri'];
        file_put_contents($path, $html);

        $process = Process::fromShellCommandline("cat {$path} | {$this->getBatPath()} --style=numbers --force-colorization");

        $process->run();

        $process->setTty(true);

        return  $process->getOutput();
    }

    protected function getBatPath(): string
    {
        return (new ExecutableFinder())->find('bat', 'bat', [
            '/usr/local/bin',
            '/opt/homebrew/bin',
        ]);
    }
}
