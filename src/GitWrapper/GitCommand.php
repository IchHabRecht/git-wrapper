<?php
namespace IchHabRecht\GitWrapper;

use Symfony\Component\Process\ProcessUtils;

class GitCommand
{
    /**
     * @var string
     */
    protected $command;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @param string $command
     * @param array $options
     * @param array $arguments
     */
    public function __construct($command, array $options = [], array $arguments = [])
    {
        $this->command = $command;
        $this->options = $options;
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return string
     */
    public function getCommandLine()
    {
        $commandLine = [
            $this->command,
            $this->buildOptions(),
            implode(' ', array_map([ProcessUtils::class, 'escapeArgument'], $this->arguments)),
        ];

        return implode(' ', array_filter($commandLine));
    }

    /**
     * @return string
     */
    public function buildOptions()
    {
        $options = [];
        foreach ($this->options as $option) {
            if (is_array($option)) {
                $name = key($option);
                $value = current($option);
                $prefix = strlen($name) === 1 ? '-' : '--';
                $options[] = $prefix . $name .
                    (!empty($value) ? ($prefix === '-' ? ' ' : '=') . ProcessUtils::escapeArgument($value) : '');
            } else {
                $options[] = (strlen($option) === 1 ? '-' : '--') . $option;
            }
        }

        return implode(' ', $options);
    }
}
