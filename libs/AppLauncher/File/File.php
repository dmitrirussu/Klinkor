<?php
/**
 * Created by Dumitru Russu.
 * Date: 04.05.2014
 * Time: 14:53
 * Files${NAME} 
 */

namespace Files;


class File {

	private $fileLink;

	public function __construct($fileName, $mode = 'w') {

		$this->fileLink = fopen($fileName, $mode);
	}

	public function getFileLink() {

		if ( empty($this->fileLink) ) {

			throw new FileException('Cannot be created file link');
		}

		return $this->fileLink;
	}

	public function read($length = 0) {
		$this->getFileLink();

		return fread($this->fileLink, $length);
	}

	public function write($string) {
		$this->getFileLink();

		return fwrite($this->fileLink, $string);
	}

	public function __destruct() {

		fclose($this->fileLink);
	}
}

class FileException extends \Exception {

}
