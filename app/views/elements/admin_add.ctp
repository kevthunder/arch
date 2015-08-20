<div class="elements form">
<?php echo $this->Form->create('Element');?>
	<fieldset>
		<legend><?php __('Admin Add Element'); ?></legend>
	<?php
		echo $this->Form->input('title');
		echo $this->Form->input('desc');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Elements', true), array('action' => 'index'));?></li>
	</ul>
</div>