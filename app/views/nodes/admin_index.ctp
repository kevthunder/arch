<div class="nodes index">
	<h2><?php __('Nodes');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('parent_id');?></th>
			<th><?php echo $this->Paginator->sort('model');?></th>
			<th><?php echo $this->Paginator->sort('foreign_key');?></th>
			<th><?php echo $this->Paginator->sort('alias');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($nodes as $node):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $node['Node']['id']; ?>&nbsp;</td>
		<td><?php echo $node['Node']['parent_id']; ?>&nbsp;</td>
		<td><?php echo $node['Node']['model']; ?>&nbsp;</td>
		<td><?php echo $node['Node']['foreign_key']; ?>&nbsp;</td>
		<td><?php echo $node['Node']['alias']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $node['Node']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $node['Node']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $node['Node']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $node['Node']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Node', true), array('action' => 'add')); ?></li>
	</ul>
</div>