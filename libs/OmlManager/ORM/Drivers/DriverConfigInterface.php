<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 26.04.2014
 * Time: 13:55
 * OmlManager\ORM\Config${NAME}
 */

namespace OmlManager\ORM\Drivers;


interface DriverConfigInterface {
	public function getConfig();
	public function getDriverConfName();
	public function getDataBaseDriverName();
	public function getDataBaseName();
	public function getDataBaseHost();
	public function getDataBaseUser();
	public function getDataBasePassword();
	public function getDataBasePort();
}