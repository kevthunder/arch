<div class="itemTypes view">
<h2><?php  __('Item Type');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $itemType['ItemType']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Title'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $itemType['ItemType']['title']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Desc'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $itemType['ItemType']['desc']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Skin'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($itemType['Skin']['title'], array('controller' => 'skins', 'action' => 'view', $itemType['Skin']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Lft'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $itemType['ItemType']['lft']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Rght'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $itemType['ItemType']['rght']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Parent Item Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($itemType['ParentItemType']['title'], array('controller' => 'item_types', 'action' => 'view', $itemType['ParentItemType']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $itemType['ItemType']['active']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $itemType['ItemType']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $itemType['ItemType']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Item Type', true), array('action' => 'edit', $itemType['ItemType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Item Type', true), array('action' => 'delete', $itemType['ItemType']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $itemType['ItemType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Item Types', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Item Type', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skins', true), array('controller' => 'skins', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skin', true), array('controller' => 'skins', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Item Types', true), array('controller' => 'item_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Item Type', true), array('controller' => 'item_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Items', true), array('controller' => 'items', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Item', true), array('controller' => 'items', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Item Types');?></h3>
	<?php if (!empty($itemType['ChildItemType'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Title'); ?></th>
		<th><?php __('Desc'); ?></th>
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
		foreach ($itemType['ChildItemType'] as $childItemType):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $childItemType['id'];?></td>
			<td><?php echo $childItemType['title'];?></td>
			<td><?php echo $childItemType['desc'];?></td>
			<td><?php echo $childItemType['skin_id'];?></td>
			<td><?php echo $childItemType['lft'];?></td>
			<td><?php echo $childItemType['rght'];?></td>
			<td><?php echo $childItemType['parent_id'];?></td>
			<td><?php echo $childItemType['active'];?></td>
			<td><?php echo $childItemType['created'];?></td>
			<td><?php echo $childItemType['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'item_types', 'action' => 'view', $childItemType['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'item_types', 'action' => 'edit', $childItemType['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'item_types', 'action' => 'delete', $childItemType['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $childItemType['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Child Item Type', true), array('controller' => 'item_types', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Items');?></h3>
	<?php if (!empty($itemType['Item'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Title'); ?></th>
		<th><?php __('Tile Id'); ?></th>
		<th><?php __('Character Id'); ?></th>
		<th><?php __('Item Type Id'); ?></th>
		<th><?php __('Strength'); ?></th>
		<th><?php __('Active'); ?></th>
		<th><?php __('Created'); ?></th>
		<th><?php __('Modified'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($itemType['Item'] as $item):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $item['id'];?></td>
			<td><?php echo $item['title'];?></td>
			<td><?php echo $item['tile_id'];?></td>
			<td><?php echo $item['character_id'];?></td>
			<td><?php echo $item['item_type_id'];?></td>
			<td><?php echo $item['strength'];?></td>
			<td><?php echo $item['active'];?></td>
			<td><?php echo $item['created'];?></td>
			<td><?php echo $item['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'items', 'action' => 'view', $item['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'items', 'action' => 'edit', $item['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'items', 'action' => 'delete', $item['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $item['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Item', true), array('controller' => 'items', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
