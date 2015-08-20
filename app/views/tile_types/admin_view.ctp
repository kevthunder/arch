<div class="tileTypes view">
<h2><?php  __('Tile Type');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $tileType['TileType']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Title'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $tileType['TileType']['title']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Skin'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($tileType['Skin']['title'], array('controller' => 'skins', 'action' => 'view', $tileType['Skin']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Lft'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $tileType['TileType']['lft']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Rght'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $tileType['TileType']['rght']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Parent Tile Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($tileType['ParentTileType']['title'], array('controller' => 'tile_types', 'action' => 'view', $tileType['ParentTileType']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $tileType['TileType']['active']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $tileType['TileType']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $tileType['TileType']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Tile Type', true), array('action' => 'edit', $tileType['TileType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Tile Type', true), array('action' => 'delete', $tileType['TileType']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $tileType['TileType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Tile Types', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Tile Type', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skins', true), array('controller' => 'skins', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skin', true), array('controller' => 'skins', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Tile Types', true), array('controller' => 'tile_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Tile Type', true), array('controller' => 'tile_types', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Tile Types');?></h3>
	<?php if (!empty($tileType['ChildTileType'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Title'); ?></th>
		<th><?php __('Skin Id'); ?></th>
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
		foreach ($tileType['ChildTileType'] as $childTileType):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $childTileType['id'];?></td>
			<td><?php echo $childTileType['title'];?></td>
			<td><?php echo $childTileType['skin_id'];?></td>
			<td><?php echo $childTileType['lft'];?></td>
			<td><?php echo $childTileType['rght'];?></td>
			<td><?php echo $childTileType['parent_id'];?></td>
			<td><?php echo $childTileType['active'];?></td>
			<td><?php echo $childTileType['created'];?></td>
			<td><?php echo $childTileType['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'tile_types', 'action' => 'view', $childTileType['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'tile_types', 'action' => 'edit', $childTileType['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'tile_types', 'action' => 'delete', $childTileType['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $childTileType['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Child Tile Type', true), array('controller' => 'tile_types', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
