<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 23.04.2014
 * Time: 12:15
 * OmlManager\ORM\Models${NAME}
 */

namespace OmlManager\ORM\Models;


interface ReaderInterface {
	public function getModelTableName();
	public function getModelDataBaseName();
	public function getModelPropertiesTokens();
} 