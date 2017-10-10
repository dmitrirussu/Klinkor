<?php
/**
 * Created by Dumitru Russu.
 * Date: 09.05.2014
 * Time: 15:31
 * AppLauncher\Generator${NAME} 
 */

namespace AppLauncher\Generator;


class AppGeneratorFactory {

	public static function create($appName, $path) {
		return new AppGenerator($appName, $path);
	}
} 