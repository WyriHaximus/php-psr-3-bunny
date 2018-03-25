<?php declare(strict_types=1);

namespace WyriHaximus\React\PSR3\Bunny;

use Bunny\Channel;
use Psr\Log\AbstractLogger;
use function WyriHaximus\PSR3\checkCorrectLogLevel;
use function WyriHaximus\PSR3\normalizeContext;
use function WyriHaximus\PSR3\processPlaceHolders;

final class BunnyLogger extends AbstractLogger
{
    /**
     * @var Channel
     */
    private $channel;

    /**
     * @var array
     */
    private $publishArguments;

    /**
     * @param Channel $channel
     * @param array   $publishArguments
     */
    public function __construct(Channel $channel, ...$publishArguments)
    {
        $this->channel = $channel;
        $this->publishArguments = $publishArguments;
    }

    public function log($level, $message, array $context = [])
    {
        checkCorrectLogLevel($level);
        $context = normalizeContext($context);
        $message = (string)$message;
        $message = processPlaceHolders($message, $context);
        $json = json_encode([
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ]);
        $this->channel->publish($json, ...$this->publishArguments);
    }
}
