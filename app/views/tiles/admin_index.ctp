<div class="tiles index">
	<h2><?php __('Tiles');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('x');?></th>
			<th><?php echo $this->Paginator->sort('y');?></th>
			<th><?php echo $this->Paginator->sort('fertility');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($tiles as $tile):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $tile['Tile']['id']; ?>&nbsp;</td>
		<td><?php echo $tile['Tile']['x']; ?>&nbsp;</td>
		<td><?php echo $tile['Tile']['y']; ?>&nbsp;</td>
		<td><?php echo $tile['Tile']['fertility']; ?>&nbsp;</td>
		<td><?php echo $tile['Tile']['active']; ?>&nbsp;</td>
		<td><?php echo $tile['Tile']['created']; ?>&nbsp;</td>
		<td><?php echo $tile['Tile']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $tile['Tile']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $tile['Tile']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $tile['Tile']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $tile['Tile']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Tile', true), array('action' => 'add')); ?></li>
	</ul>
</div>