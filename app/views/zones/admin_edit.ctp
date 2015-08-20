<div class="zones form">
<?php echo $this->Form->create('Zone');?>
	<fieldset>
		<legend><?php __('Admin Edit Zone'); ?></legend>
	<?php
		echo $this->Form->input('id');
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

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Zone.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Zone.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Zones', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Tiles', true), array('controller' => 'tiles', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Tile', true), array('controller' => 'tiles', 'action' => 'add')); ?> </li>
	</ul>
</div>