<div class="skins form">
<?php echo $this->Form->create('Skin');?>
	<fieldset>
		<legend><?php __('Admin Edit Skin'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('title');
		echo $this->Form->input('class_name');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Skin.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Skin.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Skins', true), array('action' => 'index'));?></li>
	</ul>
</div>