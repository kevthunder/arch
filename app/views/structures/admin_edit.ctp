<div class="structures form">
<?php echo $this->Form->create('Structure');?>
	<fieldset>
		<legend><?php __('Edit Structure'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('title');
		echo $this->Form->input('strength');
		echo $this->Form->input('tile_id');
		echo $this->Form->input('structure_type_id');
		echo $this->Form->input('skin_id',array('empty'=>'None'));
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Structure.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Structure.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Structures', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Tiles', true), array('controller' => 'tiles', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Tile', true), array('controller' => 'tiles', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Structure Types', true), array('controller' => 'structure_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Structure Type', true), array('controller' => 'structure_types', 'action' => 'add')); ?> </li>
	</ul>
</div>