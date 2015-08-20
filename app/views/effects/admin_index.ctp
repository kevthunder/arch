<div class="effects index">
	<h2><?php __('Effects');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('skill_id');?></th>
			<th><?php echo $this->Paginator->sort('skin_id');?></th>
			<th><?php echo $this->Paginator->sort('attachment');?></th>
			<th><?php echo $this->Paginator->sort('event_type_id');?></th>
			<th><?php echo $this->Paginator->sort('alias');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($effects as $effect):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $effect['Effect']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($effect['Skill']['title'], array('controller' => 'skills', 'action' => 'view', $effect['Skill']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($effect['Skin']['title'], array('controller' => 'skins', 'action' => 'view', $effect['Skin']['id'])); ?>
		</td>
		<td><?php echo $effect['Effect']['attachment']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($effect['EventType']['name'], array('controller' => 'event_types', 'action' => 'view', $effect['EventType']['id'])); ?>
		</td>
		<td><?php if(!is_null($effect['Effect']['alias']))echo $aliases[$effect['Effect']['alias']]; ?>&nbsp;</td>
		<td><?php echo $effect['Effect']['active']; ?>&nbsp;</td>
		<td><?php echo $effect['Effect']['created']; ?>&nbsp;</td>
		<td><?php echo $effect['Effect']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $effect['Effect']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $effect['Effect']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $effect['Effect']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $effect['Effect']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Effect', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Skills', true), array('controller' => 'skills', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill', true), array('controller' => 'skills', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skins', true), array('controller' => 'skins', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skin', true), array('controller' => 'skins', 'action' => 'add')); ?> </li>
	</ul>
</div>