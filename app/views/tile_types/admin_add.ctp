<div class="tileTypes form">
<?php echo $this->Form->create('TileType');?>
	<fieldset>
		<legend><?php __('Admin Add Tile Type'); ?></legend>
	<?php
		echo $this->Form->input('active',array('checked'=>true));
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

		<li><?php echo $this->Html->link(__('List Tile Types', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Skins', true), array('controller' => 'skins', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skin', true), array('controller' => 'skins', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Tile Types', true), array('controller' => 'tile_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Tile Type', true), array('controller' => 'tile_types', 'action' => 'add')); ?> </li>
	</ul>
</div>