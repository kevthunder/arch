<div class="skills form">
<?php echo $this->Form->create('Skill');?>
	<fieldset>
		<legend><?php __('Add Skill'); ?></legend>
	<?php
		echo $this->Form->input('active', array('checked' => 'checked'));
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

		<li><?php echo $this->Html->link(__('List Skills', true), array('action' => 'index'));?></li>
	</ul>
</div>