<div class="skills form">
<?php echo $this->Form->create('Skill');?>
	<fieldset>
		<legend><?php __('Edit Skill'); ?></legend>
	<?php
		echo $this->Form->input('active');
		echo $this->Form->input('id');
		echo $this->Form->input('title');
		echo $this->Form->input('desc');
		echo $this->Form->input('range');
		echo $this->Form->input('cast_time');
		echo $this->Form->input('recovery_time');
		echo $this->Form->input('cool_down');
		//echo $this->Form->input('data');
		//echo $this->Form->input('ui_behaviors');
		
		echo $this->Form->input('user_node',array('options'=>$nodes,'empty'=>__('None',true)));
		echo $this->Form->input('target_handler',array('options'=>$targetHandlers,'empty'=>__('None',true)));
		echo $this->Form->input('target_node',array('options'=>$nodes,'empty'=>__('None',true)));
		echo $this->Form->input('provider_node',array('options'=>$nodes,'empty'=>__('None',true)));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Skill.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Skill.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Skills', true), array('action' => 'index'));?></li>
	</ul>
</div>