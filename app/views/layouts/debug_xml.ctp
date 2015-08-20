<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php __('CakePHP: the rapid development php framework:'); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');

		echo $scripts_for_layout;
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1><?php echo $this->Html->link(__('CakePHP: the rapid development php framework', true), 'http://cakephp.org'); ?></h1>
		</div>
		<div id="content">

			<?php echo $this->Session->flash(); ?>
			<?php $out = $content_for_layout; ?>
			<?php $out = $this->XmlLink->stripDebug($content_for_layout,$debug); ?>
			<pre><?php echo htmlentities($out); ?></pre>
			<?php echo $debug; ?>
			
		</div>
	</div>
	<?php 
	if(!empty($debugMsgs)){ 
		echo '<div class="debugMsg">'.$debugMsgs.'</div>';
	}
	?>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>