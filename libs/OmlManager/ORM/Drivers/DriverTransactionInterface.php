<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 03.05.2014
 * Time: 15:51
 * OmlManager\ORM\Drivers${NAME} 
 */

namespace OmlManager\ORM\Drivers;


interface DriverTransactionInterface {

	public function beginTransaction();
	public function commitTransaction();
	public function rollbackTransaction();
}