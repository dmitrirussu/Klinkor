<div class="bg-warning table-bordered text-warning">
	<h2 class="panel-body">Error message: <?php echo($exceptionMessage); ?></h2>
</div>
<div class="panel-body">
	<?php foreach($exceptionTrace AS $trace): ?>
		<div class="alert alert-warning fade in row">
			<div class="col-lg-10">
				<table>
					<tr>
						<td>
							<b>Line:</b>&nbsp;
						</td>
						<td>
							<?php echo($trace['line']); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b>Method:</b>&nbsp;
						</td>
						<td>
							<?php echo($trace['function']); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b>Class:</b>&nbsp;
						</td>
						<td>
							<?php echo($trace['class']); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b>File:</b>&nbsp;
						</td>
						<td>
							<?php echo($trace['file']); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b>Type:</b>&nbsp;
						</td>
						<td>
							<?php

								if ( strpos($trace['type'], '::') !== false ) {

									echo("static (::)");
								}
								elseif( strpos($trace['type'], '->') !== false ) {
									echo("instance (->)");
								}

							?>
						</td>
					</tr>
					<?php if( $trace['args'] ) : ?>
						<tr>
							<td>
								<b>Args:</b>&nbsp;
							</td>
							<td>
								<pre><?php print_r($trace['args']); ?></pre>
							</td>
						</tr>
					<?php endif; ?>
				</table>
			</div>
			<button class="close" data-dismiss="alert">&times;</button>
		</div>
	<?php endforeach; ?>
</div>

