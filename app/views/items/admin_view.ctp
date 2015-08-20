<div class="items view">
<h2><?php  __('Item');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $item['Item']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Title'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $item['Item']['title']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Tile'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($item['Tile']['id'], array('controller' => 'tiles', 'action' => 'view', $item['Tile']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Character'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($item['Character']['name'], array('controller' => 'characters', 'action' => 'view', $item['Character']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Item Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($item['ItemType']['title'], array('controller' => 'item_types', 'action' => 'view', $item['ItemType']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Strength'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $item['Item']['strength']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $item['Item']['active']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $item['Item']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $item['Item']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Item', true), array('action' => 'edit', $item['Item']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Item', true), array('action' => 'delete', $item['Item']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $item['Item']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Items', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Item', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Tiles', true), array('controller' => 'tiles', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Tile', true), array('controller' => 'tiles', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Characters', true), array('controller' => 'characters', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Character', true), array('controller' => 'characters', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Item Types', true), array('controller' => 'item_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Item Type', true), array('controller' => 'item_types', 'action' => 'add')); ?> </li>
	</ul>
</div>
