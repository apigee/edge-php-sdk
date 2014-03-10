<?php
/**
 * @file
 * Provides a common logging mechanism.
 *
 * @author djohnson
 *
 * @deprecated
 * Use Psr\Log\LoggerInterface implementations instead.
 */

namespace Apigee\Util;

/**
 * Provides a common logging mechanism.
 *
 * <p><b>Note:</b> This class has been deprecated.
 * Use Psr\Log\LoggerInterface implementations instead.
 * See the {@link Apigee\Drupal\WatchdogLogger} class for an example that
 * implements the Psr\Log\LoggerInterface.</p>
 *
 * @author djohnson
 *
 * @deprecated
 * Use Psr\Log\LoggerInterface implementations instead.
 */
class Log
{

    // NOTE: these constants are the same values as Drupal's corresponding
    // WATCHDOG_* constants.
    const LOGLEVEL_DEBUG = 7;
    const LOGLEVEL_NOTICE = 5;
    const LOGLEVEL_WARNING = 4;
    const LOGLEVEL_ERROR = 3;
    const LOGLEVEL_CRITICAL = 2;

    /**
     * @var callable
     */
    public static $logCallback = 'watchdog';

    /**
     * @static
     * @param string $source
     * @param int $level
     * @param mixed $message [... $message] ...
     */
    public static function write($source, $level = self::LOGLEVEL_NOTICE, $message)
    {
        $log_threshold = Cache::get('apigee_log_threshold', self::LOGLEVEL_WARNING);
        if ($level > $log_threshold) {
            return;
        }
        $args = func_get_args();
        // strip off first two arguments
        array_shift($args);
        array_shift($args);

        if (count($args) > 1 || !is_string($message)) {
            if (is_object($message) && method_exists($message, '__toString')) {
                $message = $message->__toString();
            } else {
                ob_start();
                var_dump($args);
                $message = ob_get_clean();
            }
        }
        if (self::$logCallback == 'watchdog' && function_exists('watchdog')) {
            watchdog($source, $message, array(), $level);
        } else {
            call_user_func(self::$logCallback, $source, $message, $level);
        }
    }

    public static function warnDeprecated($source)
    {
        if (version_compare(PHP_VERSION, '5.4.0', 'ge')) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        } else {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }
        $frame = $backtrace[2];
        $message = 'Deprecated method ' . $source . '::' . $frame['function'] . ' was invoked in file ' . $frame['file'] . ', line ' . $frame['line'] . '. Please use camelCase method name instead.';
        self::write($source, self::LOGLEVEL_NOTICE, $message);
    }
}
