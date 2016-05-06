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
                        'bar' => 'baz'
                    ],
                    [
                        'b' => 'baz',
                    ]
                ],
                [],
                'test -f --foo --bar="baz" -b "baz"',
            ],
            'Multiple arguments' => [
                [],
                [
                    'foo',
                    'bar',
                    'baz',
                ],
                'test "foo" "bar" "baz"'
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
