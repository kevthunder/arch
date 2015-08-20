<div class="structureTypes form">
<?php echo $this->Form->create('StructureType');?>
	<fieldset>
		<legend><?php __('Admin Add Structure Type'); ?></legend>
	<?php
		echo $this->Form->input('title');
		echo $this->Form->input('desc');
		echo $this->Form->input('variant');
		//echo $this->Form->input('lft');
		//echo $this->Form->input('rght');
		echo $this->Form->input('parent_id',array('empty'=>'--root--'));
		echo $this->Form->input('skin_id',array('empty'=>'None'));
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Structure Types', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Structure Types', true), array('controller' => 'structure_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Structure Type', true), array('controller' => 'structure_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Nodes', true), array('controller' => 'nodes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Node', true), array('controller' => 'nodes', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Structures', true), array('controller' => 'structures', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Structure', true), array('controller' => 'structures', 'action' => 'add')); ?> </li>
	</ul>
</div>