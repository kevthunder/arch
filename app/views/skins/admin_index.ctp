<div class="skins index">
	<h2><?php __('Skins');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('title');?></th>
			<th><?php echo $this->Paginator->sort('class_name');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($skins as $skin):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $skin['Skin']['id']; ?>&nbsp;</td>
		<td><?php echo $skin['Skin']['title']; ?>&nbsp;</td>
		<td><?php echo $skin['Skin']['class_name']; ?>&nbsp;</td>
		<td><?php echo $skin['Skin']['active']; ?>&nbsp;</td>
		<td><?php echo $skin['Skin']['created']; ?>&nbsp;</td>
		<td><?php echo $skin['Skin']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $skin['Skin']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $skin['Skin']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $skin['Skin']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $skin['Skin']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Skin', true), array('action' => 'add')); ?></li>
	</ul>
</div>