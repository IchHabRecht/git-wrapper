<?php
namespace IchHabRecht\GitWrapper\Test;

use IchHabRecht\GitWrapper\GitRepository;
use IchHabRecht\GitWrapper\GitWrapper;

class GitWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GitWrapper
     */
    protected $gitWrapper;

    protected function setUp()
    {
        $this->gitWrapper = new GitWrapper();
    }

    public function testExecuteVersion()
    {
        $this->assertStringStartsWith('git version ', $this->gitWrapper->execute('version'));
    }

    public function testGetRepositoryReturnsInstance()
    {
        $directory = @tempnam('git-wrapper', 'foo');
        $repository = $this->gitWrapper->getRepository($directory);

        $this->assertInstanceOf(GitRepository::class, $repository);
    }
}
