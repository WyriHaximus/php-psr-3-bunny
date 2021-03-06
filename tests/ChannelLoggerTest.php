<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\PSR3\Bunny;

use Bunny\Channel;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\LoggerInterfaceTest;
use WyriHaximus\React\PSR3\Bunny\ChannelLogger;

final class ChannelLoggerTest extends LoggerInterfaceTest
{
    /**
     * @var array
     */
    private $logs = [];

    public function getLogger()
    {
        $channel = $this->prophesize(Channel::class);
        $channel->publish(Argument::that(function ($message) {
            $message = json_decode($message, true);
            $this->logs[] = $message['level'] . ' ' . $message['message'];

            return true;
        }), [], 'exchange', 'queue')->shouldBeCalled();

        return new ChannelLogger($channel->reveal(), [], 'exchange', 'queue');
    }

    public function getLogs()
    {
        return $this->logs;
    }

    public function testImplements()
    {
        self::assertInstanceOf(LoggerInterface::class, new ChannelLogger($this->prophesize(Channel::class)->reveal(), []));
    }

    /**
     * @expectedException \Psr\Log\InvalidArgumentException
     */
    public function testThrowsOnInvalidLevel()
    {
        (new ChannelLogger($this->prophesize(Channel::class)->reveal(), []))->log('invalid level', 'Foo');
    }
}
