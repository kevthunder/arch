<div class="eventTypes form">
<?php echo $this->Form->create('EventType');?>
	<fieldset>
		<legend><?php __('Admin Add Event Type'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('active',array('checked'=>true));
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

		<li><?php echo $this->Html->link(__('List Event Types', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Event Types', true), array('controller' => 'event_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Event Type', true), array('controller' => 'event_types', 'action' => 'add')); ?> </li>
	</ul>
</div>