<?php
/**
 * Created by Dumitru Russu.
 * Date: 05.05.2014
 * Time: 21:39
 * AppLauncher\Action${NAME} 
 */

namespace AppLauncher\Action;


use AppLauncher\Action\Exceptions\SessionException;

class Session {


	private $sessionName;

	const DEFAULT_LIFETIME = 3600;


	/**
	 * @param string $name
	 */
	public function __construct($name = 'global') {

		$this->sessionName = $name;
		@session_start();

		if ($this->getLastActivityTime() && (time() - $this->getLastActivityTime() > $this->getSessionLifeTime())) {

			$this->destroy();
		}

		$this->updateLastActivityTime();
	}

	public function setSessionName($name) {
		$this->sessionName = $name;
		return $this;
	}


	/**
	 * Get Session Life Time
	 * @return null
	 */
	public function getSessionLifeTime() {

		return $this->getVar('lifeTime', self::DEFAULT_LIFETIME);
	}

	/**
	 * Set Session LifeTime
	 * @param $time
	 */
	public function setSessionLifeTime($time) {

		$this->setVar('lifeTime', $time);
	}

	public function updateLastActivityTime() {

		$this->setVar('lastActivityTime', time());
	}

	/**
	 * @return null
	 */
	public function getLastActivityTime() {
		return $this->getVar('lastActivityTime', false);
	}

	/**
	 * Set Cache Expire Time
	 * @param $expireLimiter
	 * @return $this
	 */
	public function setCacheExpire($expireLimiter) {

		session_cache_limiter($expireLimiter);

		return $this;
	}

	/**
	 * Set save Session Path
	 * @param $path
	 * @return $this
	 */
	public function setSavePath($path) {
		session_save_path($path);

		return $this;
	}

	/**
	 * Set Session Id
	 * @param $id
	 * @return $this
	 */
	public function setId($id) {
		session_id($id);

		return $this;
	}

	/**
	 * Set Var name
	 * @param $name
	 * @param $value
	 * @return $this
	 * @throws SessionException
	 */
	public function setVar($name, $value) {

		if ( empty($name) ) {

			throw new SessionException('Name of Var cannot be Empty');
		}

		$_SESSION[$this->sessionName][$name] = (is_object($value) ? $value : $value);

		return $this;
	}

	/**
	 * Get Var
	 * @param $name
	 * @param null $default
	 * @return null
	 */
	public function getVar($name, $default = null) {

		return (isset($_SESSION[$this->sessionName][$name]) ?
			(@unserialize($_SESSION[$this->sessionName][$name]) !== false ? unserialize($_SESSION[$this->sessionName][$name]) :
				$_SESSION[$this->sessionName][$name] ) :
			$default);
	}

	/**
	 * Get All session Vars
	 * @return mixed|null
	 */
	public function getVars() {

		return (isset($_SESSION[$this->sessionName]) ?
			(@unserialize($_SESSION[$this->sessionName]) !== false ? unserialize($_SESSION[$this->sessionName]) :
				$_SESSION[$this->sessionName] ) :
			null);
	}

	/**
	 * Unset Var
	 * @param $name
	 * @return bool
	 */
	public function unsetVar($name) {

		if ( isset($_SESSION[$this->sessionName][$name]) ) {

			unset($_SESSION[$this->sessionName][$name]);

			return true;
		}

		return false;
	}

	/**
	 * Get Current Session Id
	 * @return string
	 */
	public function getId() {

		return session_id();
	}

	/**
	 * Renew Id
	 * @param bool $deleteOldSession
	 * @return bool
	 */
	public function renewId($deleteOldSession = false) {

		return session_regenerate_id($deleteOldSession);
	}

	/**
	 * Get Cache Expire Time
	 * @return int
	 */
	public function getCacheExpire() {

		return session_cache_expire();
	}

	/**
	 * Write Close Session
	 * @return $this
	 */
	public function writeClose() {
		session_write_close();

		return $this;
	}

	/**
	 * Destroy Current Session
	 * @return bool
	 */
	public function destroy() {

		if ( isset($_SESSION[$this->sessionName]) ) {

			unset($_SESSION[$this->sessionName]);

			return true;
		}

		return false;
	}

	public function destroyAll() {
		session_unset();
		return session_destroy();
	}

	/**
	 * Decode Session String
	 * @param $string
	 * @return bool
	 */
	public function decode($string) {

		return session_decode($string);
	}

	/**
	 * Encoded Session
	 * @return string
	 */
	public function __toString() {

		return session_encode();
	}

	public function __destruct() {

	}
}
