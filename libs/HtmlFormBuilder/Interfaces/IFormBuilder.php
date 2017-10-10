<?php
/**
 * Created by Dumitru Russu.
 * Date: 12.09.2014
 * Time: 11:35
 * HtmlFormBuilder${NAME} 
 */

namespace HtmlFormBuilder\Interfaces;


use HtmlFormBuilder\Validator\FormValidator;

interface IFormBuilder {


	/**
	 * Add model to Build a form
	 * @param $model
	 * @param array $options
	 * @return mixed
	 */
	public function addModel($model, array $options);


	/**
	 * Set Used Fields
	 * @param $args
	 * @return mixed
	 */
	public function setUsedFields($args);


	/**
	 * Set Unused Fields
	 * @param $args
	 * @return mixed
	 */
	public function setUnusedFields($args);


	/**
	 * Unify forms to one
	 * @return mixed
	 */
	public function unify();


	/**
	 * Get Form HTML content
	 * @return string
	 */
	public function getForm();


	/**
	 * Show form do the print of HTML content
	 * @return string
	 */
	public function show();


	/**
	 * Do Form field values Validation
	 * @return FormValidator
	 */
	public function validator();

	/**
	 * Get Form Builder Errors if Validation is fail
	 * @return array
	 */
	public function getErrors();
}