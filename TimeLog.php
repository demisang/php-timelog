<?php

namespace demi\timelog;

/**
 * Class TimeLog
 */
class TimeLog
{
    /**
     * Start microtime
     *
     * @var float
     */
    public $start;
    /**
     * All data count
     *
     * @var int
     */
    public $all;
    /**
     * Handled items count
     *
     * @var int
     */
    public $handled = 0;
    /**
     * Characters count in $all count number
     *
     * @var int
     */
    protected $allCountLength;

    // Console output colors
    const FG_GREEN = 32;
    const FG_YELLOW = 33;

    /**
     * TimeLog constructor.
     *
     * @param int|null $allCount All data count
     */
    public function __construct($allCount = null)
    {
        $this->start = microtime(true);
        $this->all = $allCount;
        $this->allCountLength = strlen("$allCount");
    }

    /**
     * Set new handled count value
     *
     * @param int $count
     */
    public function setHandled($count)
    {
        $this->handled = $count;
    }

    /**
     * Get count handled elements per second
     *
     * @return float
     */
    public function getSpeed()
    {
        $diff = $this->diffBetweenStart();

        return $this->handled / $diff;
    }

    /**
     * Get remaining seconds
     *
     * @return int Number of seconds
     */
    public function getRemaining()
    {
        $speed = $this->getSpeed();
        $notHandledCount = $this->all - $this->handled;

        $remainingSeconds = $speed != 0 ? $notHandledCount / $speed : $notHandledCount;

        return round($remainingSeconds);
    }

    /**
     * Get number of seconds between start and current time
     *
     * @return float
     */
    protected function diffBetweenStart()
    {
        $current = microtime(true);

        return $current - $this->start;
    }

    /**
     * Get status message
     *
     * @return string
     */
    public function getStatus()
    {
        $speed = round($this->getSpeed(), 2);
        $remaining = $this->getRemaining();

        // Remaining time
        $remainingTime = $this->getFormatTime($remaining);

        // Handled
        $handled = str_pad($this->handled, $this->allCountLength, ' ', STR_PAD_LEFT);

        return "Handled: $handled/$this->all\tRemaining: $remainingTime\tSpeed: $speed/sec";
    }

    /**
     * Get formatted time
     *
     * @param float $time
     *
     * @return string
     */
    protected function getFormatTime($time)
    {
        // Remaining times
        $hours = str_pad(floor($time / 60 / 60), 2, '0', STR_PAD_LEFT);
        $minutes = str_pad(floor(($time - ((int)$hours * 60 * 60)) / 60), 2, '0', STR_PAD_LEFT);
        $seconds = str_pad(floor(($time - ((int)$hours * 60 * 60) - ((int)$minutes * 60))), 2, '0', STR_PAD_LEFT);

        return "$hours:$minutes:$seconds";
    }

    /**
     * Output start message
     */
    public function showStart()
    {
        $message = "Starting handling: $this->all items";

        $this->stdout(PHP_EOL . $message . PHP_EOL, static::FG_GREEN);
    }

    /**
     * Output status message
     *
     * @param int|null $frequency How often display a message
     */
    public function showStatus($frequency = null)
    {
        if ($frequency !== null && $this->handled % $frequency !== 0) {
            return;
        }

        $this->stdout($this->getStatus() . PHP_EOL, static::FG_YELLOW);
    }

    /**
     * Output finih message
     */
    public function showFinish()
    {
        $processingTime = $this->getFormatTime($this->diffBetweenStart());
        $message = "Finished after $processingTime\tHandled: $this->handled items";

        $this->stdout(PHP_EOL . $message . PHP_EOL, static::FG_GREEN);
        // Yii::$app->controller->stdout(PHP_EOL . $message . PHP_EOL, Console::FG_GREEN);
    }

    /**
     * Reset current time log
     *
     * @param int|null $newAllCount
     */
    public function reset($newAllCount = null)
    {
        if ($newAllCount !== null) {
            $this->all = $newAllCount;
            $this->allCountLength = strlen("$newAllCount");
        }
        $this->start = microtime(true);
        $this->handled = 0;
    }

    /**
     * Prints a string to STDOUT
     *
     * You may optionally format the string with ANSI codes by
     * passing additional parameters using the constants defined in [[\yii\helpers\Console]].
     *
     * Example:
     *
     * ~~~
     * $this->stdout('This will be red and underlined.', Console::FG_RED, Console::UNDERLINE);
     * ~~~
     *
     * @param string $string the string to print
     *
     * @return int|boolean Number of bytes printed or false on error
     */
    protected function stdout($string)
    {
        $args = func_get_args();
        array_shift($args);
        $string = static::ansiFormat($string, $args);

        return fwrite(\STDOUT, $string);
    }

    /**
     * Will return a string formatted with the given ANSI style
     *
     * @param string $string the string to be formatted
     * @param array $format  An array containing formatting values.
     *                       You can pass any of the FG_*, BG_* and TEXT_* constants
     *                       and also [[xtermFgColor]] and [[xtermBgColor]] to specify a format.
     *
     * @return string
     */
    protected static function ansiFormat($string, $format = array())
    {
        $code = implode(';', $format);

        return "\033[0m" . ($code !== '' ? "\033[" . $code . "m" : '') . $string . "\033[0m";
    }
}
