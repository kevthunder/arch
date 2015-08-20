<div class="characterTypes form">
<?php echo $this->Form->create('CharacterType');?>
	<fieldset>
		<legend><?php __('Admin Edit Character Type'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('active');
		echo $this->Form->input('complete_type');
		echo $this->Form->input('title');
		echo $this->Form->input('desc');
		echo $this->Form->input('skin_id',array('empty'=>'None'));
		echo $this->Form->input('total_hp');
		echo $this->Form->input('total_hp_multi');
		echo $this->Form->input('speed');
		echo $this->Form->input('speed_multi');
		echo $this->Form->input('needed_presence');
		echo $this->Form->input('parent_id',array('empty'=>'--root--'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('CharacterType.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('CharacterType.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Character Types', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Skins', true), array('controller' => 'skins', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skin', true), array('controller' => 'skins', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Character Types', true), array('controller' => 'character_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Character Type', true), array('controller' => 'character_types', 'action' => 'add')); ?> </li>
	</ul>
</div>