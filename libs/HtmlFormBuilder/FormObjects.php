<?php
/**
 * Created by Dumitru Russu.
 * Date: 28.06.2014
 * Time: 11:26
 * HtmlFormBuilder${NAME} 
 */

namespace HtmlFormBuilder;


use HtmlFormBuilder\Exceptions\FormObjectException;

class FormObjects {

	const DISABLED = ' disabled="disabled"';
	const SELECTED = ' selected="selected"';
	const CHECKED = ' checked="checked"';
	const REQUIRED = ' required"';

	const FORM_METHOD_POST = 'post';
	const FORM_METHOD_GET = 'get';

	/**
	 * Form Container
	 * @param $form
	 * @param null $class
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function formContainer($form, $class = null, $attributes = null) {
		if ( empty($form) ) {
			throw new FormObjectException('Missing Form to put in container');
		}
		return '<div class="'.$class.'" '. $attributes .' ><section class="panel"><div class="panel-body">'.$form.'</div></section></div>';
	}


	/**
	 * Form Object
	 * @param $name
	 * @param $method
	 * @param $objects
	 * @param null $action
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function form($name, $method, $objects, $action = null, $attributes = null) {

		if ( empty($name) || empty($method) || empty($objects) ) {
			throw new FormObjectException('Form container has next required fields [name, method=[get, post],
			objects=[input,select,textarea,button]]');
		}

		return "<form role=\"form\" name=\"{$name}\" action=\"{$action}\"  enctype=\"multipart/form-data\"
		method=\"{$method}\" $attributes >$objects</form>";
	}

	/**
	 * Generate Input
	 * @param $type
	 * @param $name
	 * @param null $value
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function input($type, $name, $value = null, $attributes = null) {

		if (empty($type) || empty($name)) {
			throw new FormObjectException('Input form Object has as required next Fields [type, name]');
		}

		return "<input type=\"$type\" name=\"$name\" value=\"$value\" $attributes />";
	}

	/**
	 * Text Area
	 * @param $name
	 * @param null $value
	 * @param bool $disabled
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function textArea($name, $value = null, $disabled = false, $attributes = null) {

		if ( empty($name) ) {
			throw new FormObjectException('TextArea Object required fields [name]');
		}

		if ( $disabled ) {
			$disabled = self::DISABLED;
		}

		return "<textarea name=\"{$name}\" $disabled $attributes >{$value}</textarea>";
	}

	/**
	 *
	 * @param $name
	 * @param $options
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function select($name, $options, $attributes = null) {

		if ( empty($name) || empty($options) ) {
			throw new FormObjectException('Select form Object has as required next fields [name, options]');
		}

		return "<select name=\"$name\" $attributes >{$options}</select>";
	}

	/**
	 * Generate Select box Option
	 * @param $label
	 * @param $value
	 * @param $selected
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function option($label, $value = null, $selected = null, $attributes = null) {

		if ( empty($label) ) {
			throw new FormObjectException('Select Option has next required fields [name]');
		}

		if ( $selected ) {
			$selected = self::SELECTED;
		}

		return "<option value=\"$value\" $selected $attributes >$label</option>";
	}

	/**
	 * Grouped option
	 * @param $options
	 * @param $label
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function groupedOption($label, $options, $attributes = null) {

		if ( empty($options) || empty($label) ) {
			throw new FormObjectException('Group option');
		}

		return "<optgroup label=\"{$label}\" $attributes >{$options}</optgroup>";
	}

	/**
	 * Button Object
	 * @param $type
	 * @param $name
	 * @param $label
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function button($type, $name, $label, $attributes = null) {

		if ( empty($type) || empty($name) || empty($label) ) {
			throw new FormObjectException('Button object has required next fields [type, name, label]');
		}

		return "<button type=\"{$type}\" name=\"{$name}\" $attributes >{$label}</button>";
	}


	/**
	 * Meter form object
	 * @param int $low
	 * @param $high
	 * @param $max
	 * @param $value
	 * @param null $attributes
	 * @throws Exceptions\FormObjectException
	 * @return string
	 */
	public static function meter($low = 0, $high, $max, $value = null, $attributes = null) {

		if ( empty($high) || empty($max) ) {
			throw new FormObjectException('Meter object has required next fields [high, max] ');
		}

		return "<meter  low=\"{$low}\" high=\"{$high}\" max=\"{$max}\" value=\"{$value}\" $attributes ></meter>";
	}

	/**
	 * Progress Attributes
	 * @param $value
	 * @param $max
	 * @param null $attributes
	 * @return string
	 */
	public static function progress($max = 0, $value = null, $attributes = null) {

		return "<progress value=\"{$value}\" max=\"{$max}\" $attributes ></progress>";
	}


	/**
	 * Label object
	 * @param $label
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function label($label, $attributes = null) {

		if ( empty($label) ) {
			throw new FormObjectException('Label Object should contain a text');
		}

		return "<label {$attributes} >{$label}</label>\n";
	}

	/**
	 * FieldSet object
	 * @param $label
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function fieldset($label, $attributes = null) {
		if ( empty($label) ) {
			throw new FormObjectException('Fieldset Object should contain a textLabel');
		}

		return "<fieldset {$attributes}>{$label}</fieldset>\n";
	}

	/**
	 * Data List object
	 * @param $options
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function datalist($options, $attributes = null) {
		if ( empty($options) ) {
			throw new FormObjectException('DataList Object should contain options');
		}

		return "<datalist $attributes >{$options}</datalist>\n";
	}

	/**
	 * Legend Object
	 * @param $label
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function legend($label, $attributes = null) {
		if ( empty($label) ) {
			throw new FormObjectException('Fieldset Object should contain a textLabel');
		}

		return "<legend $attributes >{$label}</legend>\n";
	}

	/**
	 * Output Object
	 * @param $label
	 * @param null $attributes
	 * @return string
	 * @throws Exceptions\FormObjectException
	 */
	public static function output($label, $attributes = null) {
		if ( empty($label) ) {
			throw new FormObjectException('Output Object should contain a label');
		}

		return "<output name=\"{$label}\" $attributes ></output>\n";
	}
}