<?php
/**
 * Created by Dumitru Russu.
 * Date: 13.09.2014
 * Time: 14:35
 * HtmlFormBuilder\Interfaces${NAME} 
 */

namespace HtmlFormBuilder\Interfaces;


interface IFormValidator {
	public function isValid();
	public function getErrors();
}