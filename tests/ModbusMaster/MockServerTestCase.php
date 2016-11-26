<?php
namespace Tests\ModbusMaster;

use PHPUnit\Framework\TestCase;
use React\ChildProcess\Process;
use React\EventLoop\Factory;
use React\EventLoop\Timer\Timer;

abstract class MockServerTestCase extends TestCase
{
    public static function executeWithMockServer($packetToRespond, \Closure $closure, $protocol = 'TCP', $answerTimeout = 0, $port = 0)
    {
        $loop = Factory::create();

        $port = $port ?: mt_rand(40000, 50000);
        $process = new Process(PHP_BINARY . ' ' . __DIR__ . DIRECTORY_SEPARATOR . "MockResponseServer.php {$protocol} {$port} {$answerTimeout} {$packetToRespond}");

        $clientData = [];
        $loop->addTimer(0.001, function (Timer $timer) use ($process, $closure, $port, &$clientData) {
            $process->start($timer->getLoop());

            $process->stdout->on('data', function ($output) use (&$clientData) {
                $clientData[] = $output;
            });

            $closure($port);
        });

        $loop->run();
        return $clientData;
    }
}