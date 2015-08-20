<div class="eventTypes form">
<?php echo $this->Form->create('EventType');?>
	<fieldset>
		<legend><?php __('Admin Edit Event Type'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('active');
		echo $this->Form->input('name');
		echo $this->Form->input('requester_alias');
		echo $this->Form->input('controlled_alias');
		echo $this->Form->input('parent_id',array('empty'=>'--root--'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('EventType.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('EventType.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Event Types', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Event Types', true), array('controller' => 'event_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Event Type', true), array('controller' => 'event_types', 'action' => 'add')); ?> </li>
	</ul>
</div>