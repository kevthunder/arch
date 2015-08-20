<div class="tileTypes form">
<?php echo $this->Form->create('TileType');?>
	<fieldset>
		<legend><?php __('Admin Edit Tile Type'); ?></legend>
	<?php
		echo $this->Form->input('active');
		echo $this->Form->input('id');
		echo $this->Form->input('title');
		echo $this->Form->input('skin_id',array('empty'=>'None'));
		echo $this->Form->input('parent_id',array('empty'=>'--root--'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('TileType.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('TileType.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Tile Types', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Skins', true), array('controller' => 'skins', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skin', true), array('controller' => 'skins', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Tile Types', true), array('controller' => 'tile_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Tile Type', true), array('controller' => 'tile_types', 'action' => 'add')); ?> </li>
	</ul>
</div>