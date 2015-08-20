<div class="characterTypes index">
	<h2><?php __('Character Types');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('title');?></th>
			<th><?php echo $this->Paginator->sort('desc');?></th>
			<th><?php echo $this->Paginator->sort('skin_id');?></th>
			<th><?php echo $this->Paginator->sort('needed_presence');?></th>
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
	foreach ($characterTypes as $characterType):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $characterType['CharacterType']['id']; ?>&nbsp;</td>
		<td><?php echo $characterType['CharacterType']['title']; ?>&nbsp;</td>
		<td><?php echo $characterType['CharacterType']['desc']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($characterType['Skin']['title'], array('controller' => 'skins', 'action' => 'view', $characterType['Skin']['id'])); ?>
		</td>
		<td><?php echo $characterType['CharacterType']['needed_presence']; ?>&nbsp;</td>
		<td><?php echo $characterType['CharacterType']['lft']; ?>&nbsp;</td>
		<td><?php echo $characterType['CharacterType']['rght']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($characterType['ParentCharacterType']['title'], array('controller' => 'character_types', 'action' => 'view', $characterType['ParentCharacterType']['id'])); ?>
		</td>
		<td><?php echo $characterType['CharacterType']['active']; ?>&nbsp;</td>
		<td><?php echo $characterType['CharacterType']['created']; ?>&nbsp;</td>
		<td><?php echo $characterType['CharacterType']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $characterType['CharacterType']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $characterType['CharacterType']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $characterType['CharacterType']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $characterType['CharacterType']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Character Type', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Skins', true), array('controller' => 'skins', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skin', true), array('controller' => 'skins', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Character Types', true), array('controller' => 'character_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Character Type', true), array('controller' => 'character_types', 'action' => 'add')); ?> </li>
	</ul>
</div>