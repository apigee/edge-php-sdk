<?php

namespace Apigee\Drupal;

use Psr\Log\LogLevel;

/**
 * A logger class that implements Psr\Log\LoggerInterface. This class writes
 * log data to Drupal's watchdog table.
 *
 * This class will be removed in a future release.
 *
 * @deprecated
 */
class WatchdogLogger extends \Psr\Log\AbstractLogger
{

    private static $logThreshold = -1; // Log everything

    /**
     * Sets the logging threshold, below which log entries will be dropped
     * rather than logged, as definedby RFC 3164:
     * {@link http://www.faqs.org/rfcs/rfc3164.html}.
     * These values are implemented by Psr\Log\LogLevel as:
     * <ul>
     *   <li>LogLevel::EMERGENCY</li>
     *   <li>LogLevel::ALERT</li>
     *   <li>LogLevel::CRITICAL</li>
     *   <li>LogLevel::ERROR</li>
     *   <li>LogLevel::WARNING</li>
     *   <li>LogLevel::NOTICE</li>
     *   <li>LogLevel::INFO</li>
     *   <li>LogLevel::DEBUG</li>
     * </ul>
     *
     * @param int $threshold
     */
    public static function setLogThreshold($threshold)
    {
        self::$logThreshold = intval($threshold);
    }

    /**
     * Logs an event to watchdog.
     * This method is called automatically but you can call it
     * from your own code as well.
     *
     * @param string $level
     * @param mixed $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        // Translate Psr\LogLevel constants to WATCHDOG_* constants
        $severity = self::log2drupal($level);
        // Short-circuit if this event is too insignificant to log.
        // Note that Drupal's severity is in decreasing order, i.e.
        // 0 = emergency and 7 = debug
        if ($severity > self::$logThreshold) {
            return;
        }

        // Determine how we're going to handle this.
        $use_watchdog_exception = false;
        if ($message instanceof \Exception && !method_exists($message, '__toString')) {
            // use watchdog_exception ONLY for exceptions that don't have an explicit
            // __toString() method.
            $use_watchdog_exception = true;
        } elseif (is_array($message) || is_object($message)) {
            // massage non-strings into loggable strings.
            if (is_object($message) && method_exists($message, '__toString')) {
                $message = (string)$message;
            } else {
                ob_start();
                var_dump($message);
                $message = ob_get_clean();
            }
        }
	
        if(count($context) > 0) {
            $message = $this->interpolate($message, $context);
        }

        // Find the "type" (source) of the log request. Generally this is a class
        // or file name. It may be specified in $context, or it can be derived from
        // a debug_backtrace.
        if (isset($context['type'])) {
            $type = $context['type'];
        } else {
            if (version_compare(PHP_VERSION, '5.4', '>=')) {
                // Be more efficient when running PHP 5.4
                $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            } elseif (defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
                // Sigh. Pull entire backtrace.
                $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            } else {
                // Double-sigh. We are dealing with PHP < 5.3.6 dinosaurs.
                $backtrace = debug_backtrace();
            }
            $type = basename($backtrace[1]['file']);
            $type = preg_replace('!\.(module|php)$!', '', $type);
        }

        $message = preg_replace("!Authorization: Basic [A-Za-z0-9+\\=]+!", 'Authorization: Basic [**masked**]', $message);

        if ($use_watchdog_exception) {
            watchdog_exception($type, $message, null, array(), $severity);
        } else {
            watchdog($type, $message, array(), $severity);
        }

    }

    /**
     * Translates Psr\LogLevel constants into WATCHDOG_* constants.
     *
     * @param string $level
     * @return int
     */
    private static function log2drupal($level)
    {
        switch ($level) {
            case LogLevel::ALERT:
                $level = WATCHDOG_ALERT;
                break;
            case LogLevel::CRITICAL:
                $level = WATCHDOG_CRITICAL;
                break;
            case LogLevel::DEBUG:
                $level = WATCHDOG_DEBUG;
                break;
            case LogLevel::ERROR:
                $level = WATCHDOG_ERROR;
                break;
            case LogLevel::EMERGENCY:
                $level = WATCHDOG_EMERGENCY;
                break;
            case LogLevel::INFO:
                $level = WATCHDOG_INFO;
                break;
            case LogLevel::NOTICE:
                $level = WATCHDOG_NOTICE;
                break;
            case LogLevel::WARNING:
                $level = WATCHDOG_WARNING;
                break;
            default:
                $level = WATCHDOG_NOTICE;
        }
        return $level;
    }

  /**
   * Interpolates context values into the message placeholders.
   */
  private function interpolate($message, array $context = array())
  {
      // build a replacement array with braces around the context keys
      $replace = array();
      foreach ($context as $key => $val) {
          if (!is_scalar($val)) {
              if (is_object($val) && method_exists($val, '__toString')) {
                  $val = $val->__toString();
              }
              else {
                  $val = print_r($val, TRUE);
              }
          }
          $replace['{' . $key . '}'] = $val;
    }

    // interpolate replacement values into the message and return
    return strtr($message, $replace);
  }
}