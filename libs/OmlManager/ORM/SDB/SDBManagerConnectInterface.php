<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 16.04.2014
 * Time: 18:49
 * ${NAMESPACE}${NAME} 
 */

namespace OmlManager\ORM\SDB;

interface SDBManagerConnectInterface {
	/**
	 * @param string $dataBaseConfName
	 * @return SDBManagerInterface
	 */
	public static function getManager($dataBaseConfName = 'default');
} 