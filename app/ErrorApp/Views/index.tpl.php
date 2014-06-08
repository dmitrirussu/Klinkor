<?php \AppLauncher\HTML::block('header', array('javaScriptFiles' => $javaScriptFiles, 'cssFiles' => $cssFiles)); ?>
<?php \AppLauncher\HTML::block($tplName, $globalVars); ?>
<?php \AppLauncher\HTML::block('footer'); ?>
