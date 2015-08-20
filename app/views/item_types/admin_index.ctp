<div class="itemTypes index">
	<h2><?php __('Item Types');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('title');?></th>
			<th><?php echo $this->Paginator->sort('desc');?></th>
			<th><?php echo $this->Paginator->sort('skin_id');?></th>
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
	foreach ($itemTypes as $itemType):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $itemType['ItemType']['id']; ?>&nbsp;</td>
		<td><?php echo $itemType['ItemType']['title']; ?>&nbsp;</td>
		<td><?php echo $itemType['ItemType']['desc']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($itemType['Skin']['title'], array('controller' => 'skins', 'action' => 'view', $itemType['Skin']['id'])); ?>
		</td>
		<td><?php echo $itemType['ItemType']['lft']; ?>&nbsp;</td>
		<td><?php echo $itemType['ItemType']['rght']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($itemType['ParentItemType']['title'], array('controller' => 'item_types', 'action' => 'view', $itemType['ParentItemType']['id'])); ?>
		</td>
		<td><?php echo $itemType['ItemType']['active']; ?>&nbsp;</td>
		<td><?php echo $itemType['ItemType']['created']; ?>&nbsp;</td>
		<td><?php echo $itemType['ItemType']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $itemType['ItemType']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $itemType['ItemType']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $itemType['ItemType']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $itemType['ItemType']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Item Type', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Skins', true), array('controller' => 'skins', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skin', true), array('controller' => 'skins', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Item Types', true), array('controller' => 'item_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Item Type', true), array('controller' => 'item_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Items', true), array('controller' => 'items', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Item', true), array('controller' => 'items', 'action' => 'add')); ?> </li>
	</ul>
</div>