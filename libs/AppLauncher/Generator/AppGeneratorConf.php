<?php
/**
 * Created by Dumitru Russu.
 * Date: 09.05.2014
 * Time: 15:29
 * AppLauncher\Generator${NAME} 
 */

namespace AppLauncher\Generator;


class AppGeneratorConf {
	const CLASS_NAME = '[CLASS_NAME]';
	const EXTEND_CLASS = '[EXTEND_CLASS]';
	const CONTENT = '[CONTENT]';
	const METHOD_NAME = '[METHOD_NAME]';

	const _NAMESPACE = '[NAMESPACE]';

	public static $_CLASS = "\n\nclass [CLASS_NAME] [EXTEND_CLASS] {\n\n\n[CONTENT]}\n";

	public static $_NAMESPACE = "namespace [NAMESPACE];\n\n";
	public static $_USE = "use [NAMESPACE];\n";

	public static $_METHOD = "\tpublic function [METHOD_NAME]() {\n \t[CONTENT]\n\t}\n\n";
} 