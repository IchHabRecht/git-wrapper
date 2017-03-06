<?php
namespace IchHabRecht\GitWrapper\Test;

use IchHabRecht\GitWrapper\GitRepository;
use IchHabRecht\GitWrapper\GitWrapper;
use Symfony\Component\Filesystem\Filesystem;

class GitRepositoryTest extends \PHPUnit_Framework_TestCase
{
    const REMOTE_REPOSITORY_DIRECTORY = 'build/test/remote/';
    const LOCAL_REPOSITORY_DIRECTORY = 'build/test/local/';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var GitRepository
     */
    protected $gitRemoteRepository;

    /**
     * @var GitWrapper
     */
    protected $gitWrapper;

    protected function setUp()
    {
        $this->filesystem = new  Filesystem();
        $this->gitWrapper = new GitWrapper();

        $this->gitRemoteRepository = $this->initializeRemoteRepository();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->filesystem->chmod(self::REMOTE_REPOSITORY_DIRECTORY, 0777, 0000, true);
        $this->filesystem->remove(self::REMOTE_REPOSITORY_DIRECTORY);

        if (is_dir(self::LOCAL_REPOSITORY_DIRECTORY)) {
            $this->filesystem->chmod(self::LOCAL_REPOSITORY_DIRECTORY, 0777, 0000, true);
            $this->filesystem->remove(self::LOCAL_REPOSITORY_DIRECTORY);
        }
    }

    public function testCreateClonesRepository()
    {
        $this->initializeLocalRepository();
        $this->assertFileExists(self::LOCAL_REPOSITORY_DIRECTORY . '.git/HEAD');
    }

    public function testBranchReturnsBranches()
    {
        $result = $this->gitRemoteRepository->branch();

        $this->assertCount(2, $result);
    }

    public function testCheckoutSwitchesToBranch()
    {
        $result = $this->gitRemoteRepository->checkout([], ['foo']);

        $this->assertStringStartsWith('Switched to', $result);
    }

    public function testGetCurrentBranchReturnsBranch()
    {
        $this->gitRemoteRepository->checkout([], ['foo']);
        $this->assertSame('foo', $this->gitRemoteRepository->getCurrentBranch());
    }

    public function testHasTrackingBranchReturnsFalse()
    {
        $this->assertFalse($this->gitRemoteRepository->hasTrackingBranch());
    }

    public function testGetTrackingInformationWithoutChanges()
    {
        $localRepository = $this->initializeLocalRepository();

        $this->assertSame(
            [
                'branch' => 'master',
                'remoteBranch' => 'origin/master',
                'ahead' => 0,
                'behind' => 0,
            ],
            $localRepository->getTrackingInformation()
        );
    }

    public function testGetTrackingInformationWithAheadAndBehind()
    {
        $localRepository = $this->initializeLocalRepository();

        $this->changeRepository($this->gitRemoteRepository);
        $this->changeRepository($localRepository);
        $localRepository->fetch();

        $this->assertSame(
            [
                'branch' => 'master',
                'remoteBranch' => 'origin/master',
                'ahead' => 1,
                'behind' => 1,
            ],
            $localRepository->getTrackingInformation()
        );
    }

    public function testPullGetsChanges()
    {
        $localRepository = $this->initializeLocalRepository();

        $this->changeRepository($this->gitRemoteRepository);
        $localRepository->fetch();

        $this->assertSame(
            [
                'branch' => 'master',
                'remoteBranch' => 'origin/master',
                'ahead' => 0,
                'behind' => 1,
            ],
            $localRepository->getTrackingInformation()
        );

        $localRepository->pull();

        $this->assertSame(
            [
                'branch' => 'master',
                'remoteBranch' => 'origin/master',
                'ahead' => 0,
                'behind' => 0,
            ],
            $localRepository->getTrackingInformation()
        );
    }

    public function testResetRestoresRepository()
    {
        $localRepository = $this->initializeLocalRepository();
        $this->changeRepository($localRepository);

        $this->assertSame(
            [
                'branch' => 'master',
                'remoteBranch' => 'origin/master',
                'ahead' => 1,
                'behind' => 0,
            ],
            $localRepository->getTrackingInformation()
        );

        $localRepository->reset([], ['origin/master']);

        $this->assertSame(
            [
                'branch' => 'master',
                'remoteBranch' => 'origin/master',
                'ahead' => 0,
                'behind' => 0,
            ],
            $localRepository->getTrackingInformation()
        );
    }

    public function testHasChangesFindsChanges()
    {
        touch(self::REMOTE_REPOSITORY_DIRECTORY . uniqid('foo', true));

        $this->assertTrue($this->gitRemoteRepository->hasChanges());
    }

    /**
     * @return GitRepository
     */
    protected function initializeRemoteRepository()
    {
        $this->filesystem->mkdir(self::REMOTE_REPOSITORY_DIRECTORY);
        $this->gitWrapper->execute('init', [], ['--shared=0777'], self::REMOTE_REPOSITORY_DIRECTORY);

        $repository = new GitRepository($this->gitWrapper, self::REMOTE_REPOSITORY_DIRECTORY);
        $this->changeRepository($repository);

        $this->gitWrapper->execute('branch', [], ['foo'], self::REMOTE_REPOSITORY_DIRECTORY);

        return $repository;
    }

    /**
     * @return GitRepository
     */
    protected function initializeLocalRepository()
    {
        $cloneUrl = $this->filesystem->makePathRelative(self::REMOTE_REPOSITORY_DIRECTORY, dirname(self::LOCAL_REPOSITORY_DIRECTORY));
        $localRepository = new GitRepository($this->gitWrapper, self::LOCAL_REPOSITORY_DIRECTORY);
        $localRepository->create($cloneUrl);

        return $localRepository;
    }

    /**
     * @param GitRepository $gitRepository
     */
    protected function changeRepository($gitRepository)
    {
        $repositoryDirectory = rtrim($gitRepository->getDirectory(), '/\\') . '/';
        $fileName = uniqid('foo', true);
        touch($repositoryDirectory . $fileName);
        $this->gitWrapper->execute('add', [], [$fileName], $repositoryDirectory);
        $this->gitWrapper->execute('commit', ['m'], ['Add ' . $fileName], $repositoryDirectory);
    }
}
