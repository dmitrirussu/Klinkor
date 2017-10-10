<?php
/**
 * Created by Dumitru Russu.
 * Date: 12.09.2014
 * Time: 12:46
 * HtmlFormBuilder${NAME} 
 */

namespace HtmlFormBuilder\Utils;


class FormBuilderUtils {

	/**
	 * Get Getter method
	 * @param $fieldName
	 * @return string
	 */
	public static function getGetterMethod($fieldName) {
		$methodName = 'get'.implode('',array_map(function($field){
				return ucfirst($field);
			}, explode('_', $fieldName)));

		return $methodName;
	}
} 