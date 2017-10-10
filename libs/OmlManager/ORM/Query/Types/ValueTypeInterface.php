<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 19.04.2014
 * Time: 19:23
 * OmlManager\ORM\DMLQuery\Types${NAME}
 */

namespace OmlManager\ORM\Query\Types;

interface ValueTypeInterface {
	public function setValue($value);
	public function setType($value);
	public function getValue();
	public static function getAvailableValueTypes();
} 