<div class="tiles form">
<?php echo $this->Form->create('Tile');?>
	<fieldset>
		<legend><?php __('Edit Tile'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('x');
		echo $this->Form->input('y');
		echo $this->Form->input('top_id');
		echo $this->Form->input('right_id');
		echo $this->Form->input('bottom_id');
		echo $this->Form->input('left_id');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Tile.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Tile.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Tiles', true), array('action' => 'index'));?></li>
	</ul>
</div>