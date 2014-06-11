
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>DemoSecuredApp</title>
	<?php if ($cssFiles): ?>
		<?php foreach($cssFiles AS $file): ?>
			<link rel="stylesheet" type="text/css" href="/public/global/css/<?php echo($file); ?>.css" />
		<?php endforeach; ?>
	<?php endif; ?>

<?php if ($javaScriptFiles): ?>
	<?php foreach($javaScriptFiles AS $file): ?>
		<script type="text/javascript" language="javascript" src="/public/global/js/<?php echo($file); ?>.js"></script>
	<?php endforeach; ?>
<?php endif; ?>
</head>
<body>
<div class="header"></div>
<div class="container">
