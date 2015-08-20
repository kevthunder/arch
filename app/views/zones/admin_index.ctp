<div class="zones index">
	<h2><?php __('Zones');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('desc');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($zones as $zone):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $zone['Zone']['id']; ?>&nbsp;</td>
		<td><?php echo $zone['Zone']['desc']; ?>&nbsp;</td>
		<td><?php echo $zone['Zone']['name']; ?>&nbsp;</td>
		<td><?php echo $zone['Zone']['active']; ?>&nbsp;</td>
		<td><?php echo $zone['Zone']['created']; ?>&nbsp;</td>
		<td><?php echo $zone['Zone']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $zone['Zone']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $zone['Zone']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $zone['Zone']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $zone['Zone']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Zone', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Tiles', true), array('controller' => 'tiles', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Tile', true), array('controller' => 'tiles', 'action' => 'add')); ?> </li>
	</ul>
</div>