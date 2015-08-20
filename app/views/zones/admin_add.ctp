<div class="zones form">
<?php echo $this->Form->create('Zone');?>
	<fieldset>
		<legend><?php __('Admin Add Zone'); ?></legend>
	<?php
		echo $this->Form->input('desc');
		echo $this->Form->input('name');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Zones', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Tiles', true), array('controller' => 'tiles', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Tile', true), array('controller' => 'tiles', 'action' => 'add')); ?> </li>
	</ul>
</div>