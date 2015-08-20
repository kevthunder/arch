<div class="characters form">
<?php echo $this->Form->create('Character');?>
	<fieldset>
		<legend><?php __('Admin Edit Character'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('tile_id');
		echo $this->Form->input('hp');
		echo $this->Form->input('total_hp');
		echo $this->Form->input('user_id');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Character.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Character.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Characters', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Tiles', true), array('controller' => 'tiles', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Tile', true), array('controller' => 'tiles', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>