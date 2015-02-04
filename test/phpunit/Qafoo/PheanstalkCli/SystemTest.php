<?php

namespace Qafoo\PheanstalkCli;

class SystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Pheanstalk_PheanstalkInterface
     */
    private static $pheanstalk;

    /**
     * @var int
     */
    private static $testJobId;

    /**
     * @var string
     */
    private $binPath;

    public static function setUpBeforeClass()
    {
        $factory = new PheanstalkFactory();
        self::$pheanstalk = $factory->create();

        self::$testJobId = self::$pheanstalk->putInTube('test-tube', 'i:23;');
    }

    public static function tearDownAfterClass()
    {
        foreach (self::$pheanstalk->listTubes() as $tube) {
            do {
                $potentiallyHasMore = false;

                $tubeStats = self::$pheanstalk->statsTube($tube);

                if ($tubeStats['current-jobs-ready'] != 0) {
                    $jobId = self::$pheanstalk->peekReady($tube);
                    self::$pheanstalk->delete($jobId);
                    $potentiallyHasMore = true;
                }
                if ($tubeStats['current-jobs-delayed'] != 0) {
                    $jobId = self::$pheanstalk->peekDelayed($tube);
                    self::$pheanstalk->delete($jobId);
                    $potentiallyHasMore = true;
                }
                if ($tubeStats['current-jobs-buried'] != 0) {
                    $jobId = self::$pheanstalk->peekBuried($tube);
                    self::$pheanstalk->delete($jobId);
                    $potentiallyHasMore = true;
                }
            } while ($potentiallyHasMore);
        }
        // Cleanup unclean state
        try {
            $job = self::$pheanstalk->peek(self::$testJobId);
            self::$pheanstalk->delete($job);
        } catch (\Exception $e) {
            // Eat exception, since this is expected after delete was tested
        }
    }

    public function setUp()
    {
        $this->binPath = __DIR__ . '/../../../../src/bin/pheanstalk-cli';
    }

    public function testStatsCommand()
    {
        $result = `{$this->binPath} stats`;

        $this->assertRegexp(
            '(current-jobs-urgent:)',
            $result
        );
    }

    public function testListTubesCommand()
    {
        $result = `{$this->binPath} list-tubes`;

        $this->assertEquals(
            "default\ntest-tube\n",
            $result
        );
    }

    public function testPeekReadyCommand()
    {
        $result = `{$this->binPath} peek-ready -t test-tube`;

        $this->assertEquals(
            sprintf("ID: %s\nData:\ni:23;\n", self::$testJobId),
            $result
        );
    }

    public function testPeekReadyPrettyCommand()
    {
        $result = `{$this->binPath} peek-ready -t test-tube --pretty serialized-php`;

        $this->assertEquals(
            sprintf("ID: %s\nData:\nint(23)\n", self::$testJobId),
            $result
        );
    }

    public function testStatsJobCommand()
    {
        $testJobId = self::$testJobId;

        $result = `{$this->binPath} stats-job {$testJobId}`;

        $this->assertRegexp(
            '(tube: test-tube)',
            $result
        );
    }

    public function testDeleteCommand()
    {
        $testJobId = self::$testJobId;

        $result = `{$this->binPath} delete {$testJobId}`;

        $this->assertEquals(
            sprintf("Successfully deleted job %s\n", $testJobId),
            $result
        );
    }

    public function testPeekDelayedCommand()
    {
        $testJobId = self::$pheanstalk->putInTube(
            'another-test-tube',
            serialize('foo'),
            1024,
            23000
        );

        $result = `{$this->binPath} peek -t another-test-tube -s delayed`;

        $this->assertRegexp(
            '(ID: ' . $testJobId . ')',
            $result
        );
    }
}
