<div class="zones view">
<h2><?php  __('Zone');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $zone['Zone']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Desc'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $zone['Zone']['desc']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $zone['Zone']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $zone['Zone']['active']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $zone['Zone']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $zone['Zone']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Zone', true), array('action' => 'edit', $zone['Zone']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Zone', true), array('action' => 'delete', $zone['Zone']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $zone['Zone']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Zones', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Zone', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Tiles', true), array('controller' => 'tiles', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Tile', true), array('controller' => 'tiles', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Tiles');?></h3>
	<?php if (!empty($zone['Tile'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('X'); ?></th>
		<th><?php __('Y'); ?></th>
		<th><?php __('Zone Id'); ?></th>
		<th><?php __('Tile Type Id'); ?></th>
		<th><?php __('Top Id'); ?></th>
		<th><?php __('Right Id'); ?></th>
		<th><?php __('Bottom Id'); ?></th>
		<th><?php __('Left Id'); ?></th>
		<th><?php __('Presence'); ?></th>
		<th><?php __('Fertility'); ?></th>
		<th><?php __('Pathing Cache'); ?></th>
		<th><?php __('Active'); ?></th>
		<th><?php __('Created'); ?></th>
		<th><?php __('Modified'); ?></th>
		<th><?php __('Synced'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($zone['Tile'] as $tile):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $tile['id'];?></td>
			<td><?php echo $tile['x'];?></td>
			<td><?php echo $tile['y'];?></td>
			<td><?php echo $tile['zone_id'];?></td>
			<td><?php echo $tile['tile_type_id'];?></td>
			<td><?php echo $tile['top_id'];?></td>
			<td><?php echo $tile['right_id'];?></td>
			<td><?php echo $tile['bottom_id'];?></td>
			<td><?php echo $tile['left_id'];?></td>
			<td><?php echo $tile['presence'];?></td>
			<td><?php echo $tile['fertility'];?></td>
			<td><?php echo $tile['pathing_cache'];?></td>
			<td><?php echo $tile['active'];?></td>
			<td><?php echo $tile['created'];?></td>
			<td><?php echo $tile['modified'];?></td>
			<td><?php echo $tile['synced'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'tiles', 'action' => 'view', $tile['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'tiles', 'action' => 'edit', $tile['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'tiles', 'action' => 'delete', $tile['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $tile['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Tile', true), array('controller' => 'tiles', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
