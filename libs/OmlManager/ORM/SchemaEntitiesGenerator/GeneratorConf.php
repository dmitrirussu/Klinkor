<?php
/**
 * Created by Dmitri Russu. <dmitri.russu@gmail.com>
 * Date: 25.04.2014
 * Time: 08:39
 * OmlManager\ORM\Console${NAME}
 */

namespace OmlManager\ORM\SchemaEntitiesGenerator;



class GeneratorConf {

	const PACKAGE_NAME = '[PACKAGE_NAME]';
	const DATABASE_NAME = '[DATABASE_NAME]';
	const DRIVER_NAME = '[DRIVER_NAME]';
	const DRIVER_CONF = '[DRIVER_CONF]';

	const CLASS_NAME = '[CLASS_NAME]';
	const CLASS_EXTENDS = '[EXTENDS]';
	const EXTEND_PACKAGE = '[EXTEND_PACKAGE]';
	const TABLE_NAME = '[TABLE_NAME]';

	const FUNC_NAME = '[FUNC_NAME]';
	const CONTENT = '[CONTENT]';

	const FIELD_NAME = '[FIELD_NAME]';
	const TYPE = '[TYPE]';
	const AUTO_INCREMENT = '[AUTO_INCREMENT]';
	const PRIMARY_KEY = '[PRIMARY_KEY]';
	const LENGTH = '[LENGTH]';

	public static $_PHP = "<?php\n\n";
	public static $_CLASS_PACKAGE_DOC_COMMENT =
		"\n\n\n/**\n * Class [CLASS_NAME]\n * @package [PACKAGE_NAME]\n * @database [DATABASE_NAME]\n * @driver_conf [DRIVER_CONF]\n * @driver [DRIVER_NAME]\n*/";
	public static $_CLASS_DOC_COMMENT = "\n\n\n/**\n * Class [CLASS_NAME]\n * @table [TABLE_NAME]\n */";
	public static $_ATTRIBUTE_DOC_COMMENT = "
	/**
	* @var $[FIELD_NAME] {@field = [FIELD_NAME], @type = [TYPE][AUTO_INCREMENT][PRIMARY_KEY][LENGTH]}
	*/";
	public static $_METHOD_DOC_COMMENT = "
	/**
	 * @return [TYPE]
	 */";

	public static $ATTRIBUTE_AUTO_INCREMENT = ', @auto_increment = true';
	public static $ATTRIBUTE_PRIMARY_KEY = ', @primary_key = true';
	public static $ATTRIBUTE_LENGTH = ', @length = ';

	public static $_NAMESPACE = "\nnamespace ";
	public static $_USE = "\nuse ";
	public static $_CLASS = "\nclass [CLASS_NAME] [EXTENDS] {\n[CONTENT]\n}";
	public static $_CLASS_EXTENDS = ' extends [EXTEND_PACKAGE]';
	public static $_PRIVATE_VAR = "\n\tprivate $";
	public static $_GET_METHOD = "\n\tpublic function get[FUNC_NAME]() {\n\n\t\treturn [CONTENT]\n\t}\n";
	public static $_GET_STATIC_METHOD = "\n\tpublic static function get[FUNC_NAME]() {\n\n\t\treturn [CONTENT]\n\t}\n";
	public static $_SET_METHOD = "\n\tpublic function set[FUNC_NAME](\$value) {\n\t\t[CONTENT]\n\n\t\treturn \$this;\n\t}\n";
	public static $_OML_MANAGER_METHOD = "
	/**
	 * @return \\OmlManager\\ORM\\Query\\OML\\OMLQueryMangerOperationsInterface|DriverTransactionInterface
	 */
	public static function oml() {

		return OmlORManager::oml()->model(new self);
	}\n";
}