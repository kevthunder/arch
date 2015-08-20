<div class="paths index">
	<h2><?php __('Paths');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('character_id');?></th>
			<th><?php echo $this->Paginator->sort('start_tile_id');?></th>
			<th><?php echo $this->Paginator->sort('start_time');?></th>
			<th><?php echo $this->Paginator->sort('end_tile_id');?></th>
			<th><?php echo $this->Paginator->sort('end_time');?></th>
			<th><?php echo $this->Paginator->sort('steps');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($paths as $path):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $path['Path']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($path['Character']['name'], array('controller' => 'characters', 'action' => 'view', $path['Character']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($path['StartTile']['id'], array('controller' => 'tiles', 'action' => 'view', $path['StartTile']['id'])); ?>
		</td>
		<td><?php echo $path['Path']['start_time']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($path['EndTile']['id'], array('controller' => 'tiles', 'action' => 'view', $path['EndTile']['id'])); ?>
		</td>
		<td><?php echo $path['Path']['end_time']; ?>&nbsp;</td>
		<td><?php echo $path['Path']['steps']; ?>&nbsp;</td>
		<td><?php echo $path['Path']['active']; ?>&nbsp;</td>
		<td><?php echo $path['Path']['created']; ?>&nbsp;</td>
		<td><?php echo $path['Path']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $path['Path']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $path['Path']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $path['Path']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $path['Path']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Path', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Characters', true), array('controller' => 'characters', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Character', true), array('controller' => 'characters', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Tiles', true), array('controller' => 'tiles', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Start Tile', true), array('controller' => 'tiles', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Nodes', true), array('controller' => 'nodes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Node', true), array('controller' => 'nodes', 'action' => 'add')); ?> </li>
	</ul>
</div>