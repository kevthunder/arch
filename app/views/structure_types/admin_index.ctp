<div class="structureTypes index">
	<h2><?php __('Structure Types');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('title');?></th>
			<th><?php echo $this->Paginator->sort('desc');?></th>
			<th><?php echo $this->Paginator->sort('variant');?></th>
			<th><?php echo $this->Paginator->sort('parent_id');?></th>
			<th><?php echo $this->Paginator->sort('skin_id');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($structureTypes as $structureType):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $structureType['StructureType']['id']; ?>&nbsp;</td>
		<td><?php echo $structureType['StructureType']['title']; ?>&nbsp;</td>
		<td><?php echo $structureType['StructureType']['desc']; ?>&nbsp;</td>
		<td><?php echo $structureType['StructureType']['variant']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($structureType['ParentStructureType']['title'], array('controller' => 'structure_types', 'action' => 'view', $structureType['ParentStructureType']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($structureType['Skin']['title'], array('controller' => 'structure_types', 'action' => 'view', $structureType['Skin']['id'])); ?>
		</td>
		<td><?php echo $structureType['StructureType']['active']; ?>&nbsp;</td>
		<td><?php echo $structureType['StructureType']['created']; ?>&nbsp;</td>
		<td><?php echo $structureType['StructureType']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $structureType['StructureType']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $structureType['StructureType']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $structureType['StructureType']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $structureType['StructureType']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Structure Type', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Structure Types', true), array('controller' => 'structure_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Structure Type', true), array('controller' => 'structure_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Nodes', true), array('controller' => 'nodes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Node', true), array('controller' => 'nodes', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Structures', true), array('controller' => 'structures', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Structure', true), array('controller' => 'structures', 'action' => 'add')); ?> </li>
	</ul>
</div>