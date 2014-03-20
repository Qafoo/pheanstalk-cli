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

        self::$testJobId = self::$pheanstalk->putInTube('test-tube', 'test-data');
    }

    public static function tearDownAfterClass()
    {
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
            sprintf("ID: %s\nData:\ntest-data\n", self::$testJobId),
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
}
