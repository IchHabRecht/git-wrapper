<?php
namespace IchHabRecht\GitWrapper\Test;

use IchHabRecht\GitWrapper\GitCommand;

class GitCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getCommandLineReturnsCommandDataProvider()
    {
        $quoteChar = DIRECTORY_SEPARATOR === '\\' ? '"' : '\'';

        return [
            'Empty parameter' => [
                [],
                [],
                'test',
            ],
            'Multiple options' => [
                [
                    'f',
                    'foo',
                    [
                        'bar' => 'baz',
                    ],
                    [
                        'b' => 'baz',
                    ],
                ],
                [],
                'test -f --foo --bar=' . $quoteChar . 'baz' . $quoteChar . ' -b ' . $quoteChar . 'baz' . $quoteChar,
            ],
            'Multiple arguments' => [
                [],
                [
                    'foo',
                    'bar',
                    'baz',
                ],
                'test ' . $quoteChar . 'foo' . $quoteChar . ' ' . $quoteChar . 'bar' . $quoteChar . ' ' . $quoteChar . 'baz' . $quoteChar,
            ],
        ];
    }

    /**
     * @param array $options
     * @param array $arguments
     * @param $expected
     *
     * @dataProvider getCommandLineReturnsCommandDataProvider
     */
    public function testGetCommandLineReturnsCommand(array $options, array $arguments, $expected)
    {
        $gitCommand = new GitCommand('test', $options, $arguments);

        $this->assertSame($expected, $gitCommand->getCommandLine());
    }
}
