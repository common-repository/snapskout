<?php

/**
 * Finally, a light, permissions-checking logging class.
 *
 * Originally written for use with wpSearch
 *
 * Usage:
 * $log = new Katzgrau_Logger('/var/log/', Katzgrau_Logger::INFO);
 * $log->logInfo('Returned a million search results'); //Prints to the log file
 * $log->logFatal('Oh dear.'); //Prints to the log file
 * $log->logDebug('x = 5'); //Prints nothing due to current severity threshhold
 *
 * @author  Kenny Katzgrau <katzgrau@gmail.com>
 * @since   July 26, 2008 — Last update July 1, 2012
 * @link    http://codefury.net
 * @version 0.2.0
 */

/**
 * Class documentation
 */
class Katzgrau_Logger
{
    /**
     * Error severity, from low to high. From BSD syslog RFC, secion 4.1.1
     * @link http://www.faqs.org/rfcs/rfc3164.html
     */
    const EMERG  = 0;  // Emergency: system is unusable
    const ALERT  = 1;  // Alert: action must be taken immediately
    const CRIT   = 2;  // Critical: critical conditions
    const ERR    = 3;  // Error: error conditions
    const WARN   = 4;  // Warning: warning conditions
    const NOTICE = 5;  // Notice: normal but significant condition
    const INFO   = 6;  // Informational: informational messages
    const DEBUG  = 7;  // Debug: debug messages

    //custom logging level
    /**
     * Log nothing at all
     */
    const OFF    = 8;
    /**
     * Alias for CRIT
     * @deprecated
     */
    const FATAL  = 2;

    /**
     * Internal status codes
     */
    const STATUS_LOG_OPEN    = 1;
    const STATUS_OPEN_FAILED = 2;
    const STATUS_LOG_CLOSED  = 3;

    /**
     * We need a default argument value in order to add the ability to easily
     * print out objects etc. But we can't use NULL, 0, FALSE, etc, because those
     * are often the values the developers will test for. So we'll make one up.
     */
    const NO_ARGUMENTS = 'Katzgrau_Logger::NO_ARGUMENTS';

    /**
     * Current status of the log file
     * @var integer
     */
    private $_logStatus         = self::STATUS_LOG_CLOSED;
    /**
     * Holds messages generated by the class
     * @var array
     */
    private $_messageQueue      = array();
    /**
     * Path to the log file
     * @var string
     */
    private $_logFilePath       = null;
    /**
     * Current minimum logging threshold
     * @var integer
     */
    private $_severityThreshold = self::INFO;
    /**
     * This holds the file handle for this instance's log file
     * @var resource
     */
    private $_fileHandle        = null;

    /**
     * Standard messages produced by the class. Can be modified for il8n
     * @var array
     */
    private $_messages = array(
        //'writefail'   => 'The file exists, but could not be opened for writing. Check that appropriate permissions have been set.',
        'writefail'   => 'The file could not be written to. Check that appropriate permissions have been set.',
        'opensuccess' => 'The log file was opened successfully.',
        'openfail'    => 'The file could not be opened. Check permissions.',
    );

    /**
     * Default severity of log messages, if not specified
     * @var integer
     */
    private static $_defaultSeverity    = self::DEBUG;
    /**
     * Valid PHP date() format string for log timestamps
     * @var string
     */
    private static $_dateFormat         = 'Y-m-d G:i:s';
    /**
     * Octal notation for default permissions of the log file
     * @var integer
     */
    private static $_defaultPermissions = 0777;
    /**
     * Array of Katzgrau_Logger instances, part of Singleton pattern
     * @var array
     */
    private static $instances           = array();

    /**
     * Partially implements the Singleton pattern. Each $logDirectory gets one
     * instance.
     *
     * @param string  $logDirectory File path to the logging directory
     * @param integer $severity     One of the pre-defined severity constants
     * @return Katzgrau_Logger
     */
    public static function instance($logDirectory = false, $severity = false)
    {
        if ($severity === false) {
            $severity = self::$_defaultSeverity;
        }
        
        if ($logDirectory === false) {
            if (count(self::$instances) > 0) {
                return current(self::$instances);
            } else {
                $logDirectory = dirname(__FILE__);
            }
        }

        if (in_array($logDirectory, self::$instances)) {
            return self::$instances[$logDirectory];
        }

        self::$instances[$logDirectory] = new self($logDirectory, $severity);

        return self::$instances[$logDirectory];
    }

    /**
     * Class constructor
     *
     * @param string  $logDirectory File path to the logging directory
     * @param integer $severity     One of the pre-defined severity constants
     * @return void
     */
    public function __construct($logDirectory, $severity)
    {
        $logDirectory = rtrim($logDirectory, '\\/');

        if ($severity === self::OFF) {
            return;
        }

        $this->_logFilePath = $logDirectory
            . DIRECTORY_SEPARATOR
            . 'log_'
            . date('Y-m-d')
            . '.txt';

        $this->_severityThreshold = $severity;
        if (!file_exists($logDirectory)) {
            mkdir($logDirectory, self::$_defaultPermissions, true);
        }

        if (file_exists($this->_logFilePath) && !is_writable($this->_logFilePath)) {
            $this->_logStatus = self::STATUS_OPEN_FAILED;
            $this->_messageQueue[] = $this->_messages['writefail'];
            return;
        }

        if (($this->_fileHandle = fopen($this->_logFilePath, 'a'))) {
            $this->_logStatus = self::STATUS_LOG_OPEN;
            $this->_messageQueue[] = $this->_messages['opensuccess'];
        } else {
            $this->_logStatus = self::STATUS_OPEN_FAILED;
            $this->_messageQueue[] = $this->_messages['openfail'];
        }
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        if ($this->_fileHandle) {
            fclose($this->_fileHandle);
        }
    }
    /**
     * Writes a $line to the log with a severity level of DEBUG
     *
     * @param string $line Information to log
     * @return void
     */
    public function logDebug($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::DEBUG);
    }

    /**
     * Returns (and removes) the last message from the queue.
     * @return string
     */
    public function getMessage()
    {
        return array_pop($this->_messageQueue);
    }

    /**
     * Returns the entire message queue (leaving it intact)
     * @return array
     */
    public function getMessages()
    {
        return $this->_messageQueue;
    }

    /**
     * Empties the message queue
     * @return void
     */
    public function clearMessages()
    {
        $this->_messageQueue = array();
    }

    /**
     * Sets the date format used by all instances of Katzgrau_Logger
     * 
     * @param string $dateFormat Valid format string for date()
     */
    public static function setDateFormat($dateFormat)
    {
        self::$_dateFormat = $dateFormat;
    }

    /**
     * Writes a $line to the log with a severity level of INFO. Any information
     * can be used here, or it could be used with E_STRICT errors
     *
     * @param string $line Information to log
     * @return void
     */
    public function logInfo($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::INFO, $args);
    }

    /**
     * Writes a $line to the log with a severity level of NOTICE. Generally
     * corresponds to E_STRICT, E_NOTICE, or E_USER_NOTICE errors
     *
     * @param string $line Information to log
     * @return void
     */
    public function logNotice($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::NOTICE, $args);
    }

    /**
     * Writes a $line to the log with a severity level of WARN. Generally
     * corresponds to E_WARNING, E_USER_WARNING, E_CORE_WARNING, or 
     * E_COMPILE_WARNING
     *
     * @param string $line Information to log
     * @return void
     */
    public function logWarn($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::WARN, $args);
    }

    /**
     * Writes a $line to the log with a severity level of ERR. Most likely used
     * with E_RECOVERABLE_ERROR
     *
     * @param string $line Information to log
     * @return void
     */
    public function logError($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::ERR, $args);
    }

    /**
     * Writes a $line to the log with a severity level of FATAL. Generally
     * corresponds to E_ERROR, E_USER_ERROR, E_CORE_ERROR, or E_COMPILE_ERROR
     *
     * @param string $line Information to log
     * @return void
     * @deprecated Use logCrit
     */
    public function logFatal($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::FATAL, $args);
    }

    /**
     * Writes a $line to the log with a severity level of ALERT.
     *
     * @param string $line Information to log
     * @return void
     */
    public function logAlert($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::ALERT, $args);
    }

    /**
     * Writes a $line to the log with a severity level of CRIT.
     *
     * @param string $line Information to log
     * @return void
     */
    public function logCrit($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::CRIT, $args);
    }

    /**
     * Writes a $line to the log with a severity level of EMERG.
     *
     * @param string $line Information to log
     * @return void
     */
    public function logEmerg($line, $args = self::NO_ARGUMENTS)
    {
        $this->log($line, self::EMERG, $args);
    }

    /**
     * Writes a $line to the log with the given severity
     *
     * @param string  $line     Text to add to the log
     * @param integer $severity Severity level of log message (use constants)
     */
    public function log($line, $severity, $args = self::NO_ARGUMENTS)
    {
        if ($this->_severityThreshold >= $severity) {
            $status = $this->_getTimeLine($severity);
            
            $line = "$status $line";
            
            if($args !== self::NO_ARGUMENTS) {
                /* Print the passed object value */
                $line = $line . '; ' . var_export($args, true);
            }
            
            $this->writeFreeFormLine($line . PHP_EOL);
        }
    }

    /**
     * Writes a line to the log without prepending a status or timestamp
     *
     * @param string $line Line to write to the log
     * @return void
     */
    public function writeFreeFormLine($line)
    {
        if ($this->_logStatus == self::STATUS_LOG_OPEN
            && $this->_severityThreshold != self::OFF) {
            if (fwrite($this->_fileHandle, $line) === false) {
                $this->_messageQueue[] = $this->_messages['writefail'];
            }
        }
    }

    private function _getTimeLine($level)
    {
        $time = date(self::$_dateFormat);

        switch ($level) {
            case self::EMERG:
                return "$time - EMERG -->";
            case self::ALERT:
                return "$time - ALERT -->";
            case self::CRIT:
                return "$time - CRIT -->";
            case self::FATAL: # FATAL is an alias of CRIT
                return "$time - FATAL -->";
            case self::NOTICE:
                return "$time - NOTICE -->";
            case self::INFO:
                return "$time - INFO -->";
            case self::WARN:
                return "$time - WARN -->";
            case self::DEBUG:
                return "$time - DEBUG -->";
            case self::ERR:
                return "$time - ERROR -->";
            default:
                return "$time - LOG -->";
        }
    }
}
