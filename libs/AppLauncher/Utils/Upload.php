<?php
/**
 * Created by Dumitru Russu.
 * Date: 07.07.2014
 * Time: 00:27
 * AppLauncher\Utils${NAME} 
 */

namespace AppLauncher\Utils;


class Upload {

	private static $ALLOW_EXTENSION = array();

	private function  __construct(){}
	private function __clone() {}


	public static function setAllowExtensions(array $extensions) {
		self::$ALLOW_EXTENSION = $extensions;
	}


	/**
	 * Check
	 * @param $ext
	 * @return bool
	 */
	private static function checkIsAllowedExtensions($ext) {
		if ( empty(self::$ALLOW_EXTENSION) ) {
			return true;
		}

		return (in_array(strtolower($ext), self::$ALLOW_EXTENSION));
	}

	/**
	 * Upload File
	 * @param array $files
	 * @param $destinationPath
	 * @param null $newFileName
	 * @throws \Exception
	 * @return bool
	 */
	public static function file(array $files, $destinationPath, &$newFileName = null) {
		$hasManyFiles = (isset($files[0]) || (isset($files['name']) && is_array($files['name'])) ? true : false);
		if ( $hasManyFiles ) {

			foreach($files as $key => $file) {
				if ($key === 'tmp_name' && isset($file[0])) {
					$fileTmpFiles = $file;

					$i = 0;
					foreach($fileTmpFiles as $idFile => $tmpFile) {
						if ( empty($files['name'][$idFile]) ) {
							continue;
						}

						$tmpFileName = $tmpFile;
						$fileName = $files['name'][$idFile];
						$fileInfo = pathinfo($fileName);

						if ( !isset($fileInfo['extension']) ) {
							throw new \Exception('Missing file!');
						}

						$ext = $fileInfo['extension'];

						if ( $newFileName && !$i) {
							$newFileNameInfo = pathinfo($newFileName);
							if ( isset($newFileNameInfo['extension']) && !empty($newFileNameInfo['extension'])) {
								$ext = $newFileNameInfo['extension'];
								$fileName = $newFileName;
							}
							else {
								$fileName = $newFileName.'.'.$ext;
							}
						}
						$newFileName = $fileName;

						//check is available
						if ( !self::checkIsAllowedExtensions($ext) ) {
							throw new \Exception('File extension is not available!');
						}

						if ( !move_uploaded_file($tmpFileName, $destinationPath.$fileName) ) {
							return false;
						}
						$i++;
					}
				}
				else if ($key === 'tmp_name') {

					$fileTmpFiles = $file;
					$i = 0;
					foreach($fileTmpFiles as $idFile => $tmpFile) {
						if ( empty($files['name'][$idFile]) ) {
							continue;
						}

						$tmpFileName = $tmpFile;
						$fileName = $files['name'][$idFile];
						$fileInfo = pathinfo($fileName);
						$ext = $fileInfo['extension'];

						if ( $newFileName && !$i) {
							$newFileNameInfo = pathinfo($newFileName);
							if ( isset($newFileNameInfo['extension']) && !empty($newFileNameInfo['extension'])) {
								$ext = $newFileNameInfo['extension'];
								$fileName = $newFileName;
							}
							else {
								$fileName = $newFileName.'.'.$ext;
							}
						}
						$newFileName = $fileName;

						//check is available
						if ( !self::checkIsAllowedExtensions($ext) ) {
							throw new \Exception('File extension is not available!');
						}

						if ( !is_dir($destinationPath) ) {
							throw new \Exception('Missing upload directory! -> ' . $destinationPath);
						}

						if ($error = !move_uploaded_file($tmpFileName, $destinationPath.$fileName) ) {
							return false;
						}
						$i++;
					}
				}
			}

			return true;
		}
		else {

			$fileName = $files['name'];
			$fileInfo = pathinfo($files['name']);

			if ( !isset($fileInfo['extension']) ) {
				throw new \Exception('Missing File!');
			}

			$ext = $fileInfo['extension'];
			if ( $newFileName ) {
				$newFileNameInfo = pathinfo($newFileName);
				$fileName = $newFileName.'.'.$ext;
				if ( isset($newFileNameInfo['extension']) && !empty($newFileNameInfo['extension'])) {
					$ext = $newFileNameInfo['extension'];
					$fileName = $newFileName;
				}
			}

			if ( !self::checkIsAllowedExtensions($ext) ) {
				throw new \Exception('File extension is not available!');
			}

			$newFileName = $fileName;

			if ( !move_uploaded_file($files['tmp_name'], $destinationPath.$fileName) ) {

				return false;
			}
		}
		return true;
	}
} 