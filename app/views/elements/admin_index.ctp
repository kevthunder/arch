<div class="elements index">
	<h2><?php __('Elements');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('title');?></th>
			<th><?php echo $this->Paginator->sort('desc');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($elements as $element):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $element['Element']['id']; ?>&nbsp;</td>
		<td><?php echo $element['Element']['title']; ?>&nbsp;</td>
		<td><?php echo $element['Element']['desc']; ?>&nbsp;</td>
		<td><?php echo $element['Element']['active']; ?>&nbsp;</td>
		<td><?php echo $element['Element']['created']; ?>&nbsp;</td>
		<td><?php echo $element['Element']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $element['Element']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $element['Element']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $element['Element']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $element['Element']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Element', true), array('action' => 'add')); ?></li>
	</ul>
</div>