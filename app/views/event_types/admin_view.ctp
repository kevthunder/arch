<div class="eventTypes view">
<h2><?php  __('Event Type');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $eventType['EventType']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $eventType['EventType']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Lft'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $eventType['EventType']['lft']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Rght'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $eventType['EventType']['rght']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Parent Event Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($eventType['ParentEventType']['name'], array('controller' => 'event_types', 'action' => 'view', $eventType['ParentEventType']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $eventType['EventType']['active']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $eventType['EventType']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $eventType['EventType']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Event Type', true), array('action' => 'edit', $eventType['EventType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Event Type', true), array('action' => 'delete', $eventType['EventType']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $eventType['EventType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Event Types', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Event Type', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Event Types', true), array('controller' => 'event_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Event Type', true), array('controller' => 'event_types', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Event Types');?></h3>
	<?php if (!empty($eventType['ChildEventType'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Lft'); ?></th>
		<th><?php __('Rght'); ?></th>
		<th><?php __('Parent Id'); ?></th>
		<th><?php __('Active'); ?></th>
		<th><?php __('Created'); ?></th>
		<th><?php __('Modified'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($eventType['ChildEventType'] as $childEventType):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $childEventType['id'];?></td>
			<td><?php echo $childEventType['name'];?></td>
			<td><?php echo $childEventType['lft'];?></td>
			<td><?php echo $childEventType['rght'];?></td>
			<td><?php echo $childEventType['parent_id'];?></td>
			<td><?php echo $childEventType['active'];?></td>
			<td><?php echo $childEventType['created'];?></td>
			<td><?php echo $childEventType['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'event_types', 'action' => 'view', $childEventType['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'event_types', 'action' => 'edit', $childEventType['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'event_types', 'action' => 'delete', $childEventType['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $childEventType['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Child Event Type', true), array('controller' => 'event_types', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
