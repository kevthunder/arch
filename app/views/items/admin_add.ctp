<div class="items form">
<?php echo $this->Form->create('Item');?>
	<fieldset>
		<legend><?php __('Admin Add Item'); ?></legend>
	<?php
		echo $this->Form->input('active',array('checked'=>true));
		echo $this->Form->input('title');
		echo $this->Form->input('tile_id');
		echo $this->Form->input('character_id');
		echo $this->Form->input('item_type_id');
		echo $this->Form->input('strength');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Items', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Tiles', true), array('controller' => 'tiles', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Tile', true), array('controller' => 'tiles', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Characters', true), array('controller' => 'characters', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Character', true), array('controller' => 'characters', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Item Types', true), array('controller' => 'item_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Item Type', true), array('controller' => 'item_types', 'action' => 'add')); ?> </li>
	</ul>
</div>