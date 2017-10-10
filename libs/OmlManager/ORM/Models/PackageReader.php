<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 03.05.2014
 * Time: 14:58
 * OmlManager\ORM\Models${NAME} 
 */

namespace OmlManager\ORM\Models;


class PackageReader {

	private $modelInfo;
	private $reflectionModelClass;


	public function __construct($packageClass) {

		$this->reflectionModelClass = new \ReflectionClass($packageClass);
		$this->modelInfo = $this->readTokensFromDocComment($this->reflectionModelClass->getDocComment());

	}


	public function getInfo() {

		return $this->modelInfo;
	}


	public function getModelDataBaseName() {
		if ( !isset($this->modelInfo['database']) ) {

			throw new ReaderException('Missing database name for Model ' . $this->reflectionModelClass->getName());
		}

		return trim($this->modelInfo['database']);
	}

	public function getModelDataDriverConfName() {
		if ( !isset($this->modelInfo['driver_conf']) ) {

			throw new ReaderException('Missing database driver_conf for Model' . $this->reflectionModelClass->getName());
		}

		return trim($this->modelInfo['driver_conf']);
	}

	/**
	 * Read tokens from Doc Comment
	 * @param $docComment
	 * @return array
	 * @throws \Exception
	 */
	private function readTokensFromDocComment($docComment) {

		$docComment =  trim(preg_replace('/[=,}{]/', ' ', $docComment));
		preg_match_all('/@([a-zA-Z_]+)\s+(.*?)\ /s', $docComment, $matches);

		if ( !isset($matches[1]) || !isset($matches[2]) ) {

			throw new ReaderException('Missing tokens');
		}

		$tokens = array_combine($matches[1], $matches[2]);

		return $tokens;
	}
} 