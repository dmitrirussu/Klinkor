<?php
/**
 * Created by Dumitru Russu.
 * Date: 12.11.2015
 * Time: 17:53
 * AppLauncher\Utils${NAME} 
 */

namespace AppLauncher\Utils;

interface AppLogInterface {
	/**
	 * @param string $scriptLogFileName
	 * @return AppLogWriterInterface
	 */
	public function open($scriptLogFileName = 'global_app_log');
}

interface AppLogWriterInterface {
	public function writeEmergency($message);
	public function writeAlert($message);
	public function writeCritical($message);
	public function writeError($message);
	public function writeWarning($message);
	public function writeNotice($message);
	public function writeInfo($message);
	public function writeDebug($message);
}

class AppLog implements AppLogInterface, AppLogWriterInterface {
	const LOG_EMERG = LOG_EMERG; 	//system is unusable
	const LOG_ALERT = LOG_ALERT; 	//action must be taken immediately
	const LOG_CRIT = LOG_CRIT; 	//critical conditions
	const LOG_ERR = LOG_ERR; 	//error conditions
	const LOG_WARNING = LOG_WARNING; 	//warning conditions
	const LOG_NOTICE = LOG_NOTICE; 	//normal, but significant, condition
	const LOG_INFO = LOG_INFO; 	//informational message
	const LOG_DEBUG = LOG_DEBUG; 	//debug-level message

	private static $instance;
	private function __construct() {}

	/**
	 * @return AppLogInterface
	 */
	public static function getInstance() {
		if ( empty(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @param string $scriptLogFileName
	 * @return $this
	 */
	public function open($scriptLogFileName = 'global_app_log') {
		if ( empty($scriptLogFileName) ) {
			throw new \InvalidArgumentException('Missing Argument');
		}

		// open syslog, include the process ID and also send
		// the log to standard error, and use a user defined
		// logging mechanism
		openlog($scriptLogFileName, LOG_PID | LOG_PERROR, LOG_LOCAL0);

		return $this;
	}
	private function getCurrentDatetime() {
		return date('d-m-Y H:i:s', time());
	}

	private function prepareMessage(&$message) {
		$message = $this->getCurrentDatetime(). ': ' .$message;
	}

	private function writeLog($priority, $message) {
		$this->prepareMessage($message);
		syslog($priority, $message);

		return $this;
	}

	public function writeEmergency($message) {
		$this->writeLog(self::LOG_EMERG, $message);

		return $this;
	}

	public function writeAlert($message) {
		$this->writeLog(self::LOG_ALERT, $message);

		return $this;
	}

	public function writeCritical($message) {
		$this->writeLog(self::LOG_CRIT, $message);

		return $this;
	}

	public function writeError($message) {
		$this->writeLog(self::LOG_ERR, $message);

		return $this;
	}

	public function writeWarning($message) {
		$this->writeLog(self::LOG_WARNING, $message);

		return $this;
	}

	public function writeNotice($message) {
		$this->writeLog(self::LOG_NOTICE, $message);

		return $this;
	}

	public function writeInfo($message) {
		$this->writeLog(self::LOG_INFO, $message);

		return $this;
	}

	public function writeDebug($message) {
		$this->writeLog(self::LOG_INFO, $message);

		return $this;
	}

	public function __destruct() {
		closelog();
	}
}

