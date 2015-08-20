<div class="nodes form">
<?php echo $this->Form->create('Node');?>
	<fieldset>
		<legend><?php __('Admin Add Node'); ?></legend>
	<?php
		echo $this->Form->input('parent_id',array('empty'=>'--root--'));
		echo $this->Form->input('model');
		echo $this->Form->input('foreign_key');
		echo $this->Form->input('alias');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Nodes', true), array('action' => 'index'));?></li>
	</ul>
</div>