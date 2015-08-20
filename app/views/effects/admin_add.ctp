<div class="effects form">
<?php echo $this->Form->create('Effect');?>
	<fieldset>
		<legend><?php __('Admin Add Effect'); ?></legend>
	<?php
		echo $this->Form->input('skill_id');
		echo $this->Form->input('active',array('checked'=>true));
		echo $this->Form->input('skin_id');
		echo $this->Form->input('attachment');
		echo $this->Form->input('event_type_id');
		echo $this->Form->input('end_event_type_id',array('options'=>$eventTypes));
		echo $this->Form->input('alias',array('options'=>$aliases));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Effects', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Skills', true), array('controller' => 'skills', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill', true), array('controller' => 'skills', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skins', true), array('controller' => 'skins', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skin', true), array('controller' => 'skins', 'action' => 'add')); ?> </li>
	</ul>
</div>