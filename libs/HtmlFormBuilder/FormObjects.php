<?php
/**
 * Created by Dumitru Russu.
 * Date: 28.06.2014
 * Time: 11:26
 * HtmlFormBuilder${NAME} 
 */

namespace HtmlFormBuilder;


class FormObjects {
	const FORM = '<form role="form" name="{name}" action="{action}" method="{method}" {attributes}>{fields}</form>';
	const INPUT = '<input type="{type}" name="{name}" value="{value}" placeholder="{placeholder}" {attributes}/>';
	const TEXTAREA = '<textarea name="{name}" placeholder="{placeholder}" {attributes}></textarea>';
	const SELECT = '<select name="{name}" {attributes}>{options}</select>';
	const SELECT_GROUP = '<optgroup label="{label}" {attributes}>{options}</optgroup>';
	const SELECT_OPTION = '<option value="{value}" {attributes}>{text}</option>';
	const BUTTON= '<button type="{type}" name="{name}" {attributes}>{text}</button>';
	//Internet Explorer Not Supported
	const METER = '<meter  low="{low}" high="{high}" max="{max}" value="{value}"></meter>';
	const PROGRESS = '<progress value="{value}" max="{max}"></progress>';

	const LABEL = '<label {attributes}>{text}</label>';
	const FIELDSET = '<fieldset {attributes}>{fields}</fieldset>';
	const DATALIST = '<datalist {attributes}>{options}</datalist>';
	const LEGEND = '<legend {attributes}>{text}</legend>';
	const OUTPUT = '<output name="{name}"></output>';

	const DISABLED = ' disabled="disabled"';
	const SELECTED = ' selected="selected"';
	const CLASS_NAME = ' class="{class}"';
	const ID_NAME = ' id="{id}"';
	const REQUIRED = ' required"';
	const STEP = ' step="{step}"';
	const MAX = ' max="{max}"';
	const MIN = ' min="{min}"';
	const LOW = ' low="{low}"';
	const HIGH = ' low="{high}"';

	//todo macros
	const MACROS_NAME = '{name}';
	const MACROS_ACTION = '{action}';
	const MACROS_METHOD = '{method}';
	const MACROS_TYPE = '{type}';
	const MACROS_VALUE = '{value}';
	const MACROS_ATTRIBUTES = '{attributes}';
	const MACROS_PLACEHOLDER = '{placeholder}';
	const MACROS_FIELDS = '{fields}';
	const MACROS_ID = '{id}';
	const MACROS_CLASS = '{class}';
	const MACROS_STEP = '{step}';
	const MACROS_MAX = '{max}';
	const MACROS_MIN = '{min}';
	const MACROS_LOW = '{low}';
	const MACROS_TEXT = '{text}';
	const MACROS_OPTIONS = '{options}';
	const MACROS_SELECTED = '{selected}';


	const FORM_METHOD_POST = 'post';
	const FORM_METHOD_GET = 'get';
}