<?php
/**
 * Created by Dumitru Russu.
 * Date: 28.06.2014
 * Time: 13:58
 * HtmlFormBuilder${NAME} 
 */

namespace HtmlFormBuilder\Translator;


class FormTranslations {

	private static $ENABLED = false;
	private static $LANG_CODE = 'en';
	private static $TRANSLATIONS = array();

	public function __construct($langCode = 'en', $enabled = true) {
		self::$LANG_CODE = $langCode;
		self::$ENABLED = $enabled;
	}

	/**
	 * @param $section
	 * @param $keyword
	 * @return string
	 */
	public static function t($section, $keyword) {

		if ( !self::$ENABLED ) {
			$data = explode('_', $keyword);
			if ( is_array($data) ) {

				return ucfirst(implode(' ', array_map(function($keyword){
					return trim($keyword);
				}, $data)));
			}
			return $keyword;
		}

		$section = trim(strtolower($section));
		$keyword = trim(strtolower($keyword));

		if ( isset(self::$TRANSLATIONS[$section][$keyword][self::$LANG_CODE]) ) {
			return self::$TRANSLATIONS[$section][$keyword][self::$LANG_CODE];
		}

		return self::$LANG_CODE.'_'.$section.'_'.$keyword;
	}
} 