<div class="itemTypes form">
<?php echo $this->Form->create('ItemType');?>
	<fieldset>
		<legend><?php __('Admin Edit Item Type'); ?></legend>
	<?php
		echo $this->Form->input('active');
		echo $this->Form->input('parent_id',array('empty'=>__('--root--',true)));
		echo $this->Form->input('id');
		echo $this->Form->input('title');
		echo $this->Form->input('desc');
		echo $this->Form->input('skin_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('ItemType.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('ItemType.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Item Types', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Skins', true), array('controller' => 'skins', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skin', true), array('controller' => 'skins', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Item Types', true), array('controller' => 'item_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Item Type', true), array('controller' => 'item_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Items', true), array('controller' => 'items', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Item', true), array('controller' => 'items', 'action' => 'add')); ?> </li>
	</ul>
</div>