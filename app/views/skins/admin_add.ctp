<div class="skins form">
<?php echo $this->Form->create('Skin');?>
	<fieldset>
		<legend><?php __('Admin Add Skin'); ?></legend>
	<?php
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

		<li><?php echo $this->Html->link(__('List Skins', true), array('action' => 'index'));?></li>
	</ul>
</div>