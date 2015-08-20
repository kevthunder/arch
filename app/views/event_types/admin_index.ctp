<div class="eventTypes index">
	<h2><?php __('Event Types');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('requester_alias');?></th>
			<th><?php echo $this->Paginator->sort('controlled_alias');?></th>
			<th><?php echo $this->Paginator->sort('lft');?></th>
			<th><?php echo $this->Paginator->sort('rght');?></th>
			<th><?php echo $this->Paginator->sort('parent_id');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($eventTypes as $eventType):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $eventType['EventType']['id']; ?>&nbsp;</td>
		<td><?php echo $eventType['EventType']['name']; ?>&nbsp;</td>
		<td><?php echo $eventType['EventType']['requester_alias']; ?>&nbsp;</td>
		<td><?php echo $eventType['EventType']['controlled_alias']; ?>&nbsp;</td>
		<td><?php echo $eventType['EventType']['lft']; ?>&nbsp;</td>
		<td><?php echo $eventType['EventType']['rght']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($eventType['ParentEventType']['name'], array('controller' => 'event_types', 'action' => 'view', $eventType['ParentEventType']['id'])); ?>
		</td>
		<td><?php echo $eventType['EventType']['active']; ?>&nbsp;</td>
		<td><?php echo $eventType['EventType']['created']; ?>&nbsp;</td>
		<td><?php echo $eventType['EventType']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $eventType['EventType']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $eventType['EventType']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $eventType['EventType']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $eventType['EventType']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Event Type', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Event Types', true), array('controller' => 'event_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Event Type', true), array('controller' => 'event_types', 'action' => 'add')); ?> </li>
	</ul>
</div>