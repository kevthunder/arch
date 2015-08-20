<div class="events index">
	<h2><?php __('Events');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('aro_id');?></th>
			<th><?php echo $this->Paginator->sort('aco_id');?></th>
			<th><?php echo $this->Paginator->sort('handler');?></th>
			<th><?php echo $this->Paginator->sort('function');?></th>
			<th><?php echo $this->Paginator->sort('event_type_id');?></th>
			<th><?php echo $this->Paginator->sort('phase');?></th>
			<th><?php echo $this->Paginator->sort('conditions');?></th>
			<th><?php echo $this->Paginator->sort('params');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($events as $event):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $event['Event']['id']; ?>&nbsp;</td>
		<td>
			<?php if(!empty($event['Aro']['display'])) echo $this->Html->link($event['Aro']['display'], array('controller' => 'nodes', 'action' => 'view', $event['Aro']['id'])); ?>
		</td>
		<td>
			<?php if(!empty($event['Aco']['display'])) echo $this->Html->link($event['Aco']['display'], array('controller' => 'nodes', 'action' => 'view', $event['Aco']['id'])); ?>
		</td>
		<td><?php echo $event['Event']['handler']; ?>&nbsp;</td>
		<td><?php echo $event['Event']['function']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($event['EventType']['name'], array('controller' => 'event_types', 'action' => 'view', $event['EventType']['id'])); ?>
		</td>
		<td><?php echo $event['Event']['phase']; ?>&nbsp;</td>
		<td><?php echo $event['Event']['conditions']; ?>&nbsp;</td>
		<td><?php echo var_export($event['Event']['params']); ?>&nbsp;</td>
		<td><?php echo $event['Event']['active']; ?>&nbsp;</td>
		<td><?php echo $event['Event']['created']; ?>&nbsp;</td>
		<td><?php echo $event['Event']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $event['Event']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $event['Event']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $event['Event']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $event['Event']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Event', true), array('action' => 'add')); ?></li>
	</ul>
</div>