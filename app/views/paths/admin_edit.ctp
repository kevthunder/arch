<div class="paths form">
<?php echo $this->Form->create('Path');?>
	<fieldset>
		<legend><?php __('Admin Edit Path'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('character_id');
		echo $this->Form->input('start_tile_id');
		echo $this->Form->input('start_time');
		echo $this->Form->input('end_tile_id');
		echo $this->Form->input('end_time');
		echo $this->Form->input('steps');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Path.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Path.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Paths', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Characters', true), array('controller' => 'characters', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Character', true), array('controller' => 'characters', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Tiles', true), array('controller' => 'tiles', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Start Tile', true), array('controller' => 'tiles', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Nodes', true), array('controller' => 'nodes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Node', true), array('controller' => 'nodes', 'action' => 'add')); ?> </li>
	</ul>
</div>