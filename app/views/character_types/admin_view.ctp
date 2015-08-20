<div class="characterTypes view">
<h2><?php  __('Character Type');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $characterType['CharacterType']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Title'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $characterType['CharacterType']['title']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Desc'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $characterType['CharacterType']['desc']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Skin'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($characterType['Skin']['title'], array('controller' => 'skins', 'action' => 'view', $characterType['Skin']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Needed Presence'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $characterType['CharacterType']['needed_presence']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Lft'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $characterType['CharacterType']['lft']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Rght'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $characterType['CharacterType']['rght']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Parent Character Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($characterType['ParentCharacterType']['title'], array('controller' => 'character_types', 'action' => 'view', $characterType['ParentCharacterType']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $characterType['CharacterType']['active']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $characterType['CharacterType']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $characterType['CharacterType']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Character Type', true), array('action' => 'edit', $characterType['CharacterType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Character Type', true), array('action' => 'delete', $characterType['CharacterType']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $characterType['CharacterType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Character Types', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Character Type', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skins', true), array('controller' => 'skins', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skin', true), array('controller' => 'skins', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Character Types', true), array('controller' => 'character_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Character Type', true), array('controller' => 'character_types', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Character Types');?></h3>
	<?php if (!empty($characterType['ChildCharacterType'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Title'); ?></th>
		<th><?php __('Desc'); ?></th>
		<th><?php __('Skin Id'); ?></th>
		<th><?php __('Needed Presence'); ?></th>
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
		foreach ($characterType['ChildCharacterType'] as $childCharacterType):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $childCharacterType['id'];?></td>
			<td><?php echo $childCharacterType['title'];?></td>
			<td><?php echo $childCharacterType['desc'];?></td>
			<td><?php echo $childCharacterType['skin_id'];?></td>
			<td><?php echo $childCharacterType['needed_presence'];?></td>
			<td><?php echo $childCharacterType['lft'];?></td>
			<td><?php echo $childCharacterType['rght'];?></td>
			<td><?php echo $childCharacterType['parent_id'];?></td>
			<td><?php echo $childCharacterType['active'];?></td>
			<td><?php echo $childCharacterType['created'];?></td>
			<td><?php echo $childCharacterType['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'character_types', 'action' => 'view', $childCharacterType['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'character_types', 'action' => 'edit', $childCharacterType['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'character_types', 'action' => 'delete', $childCharacterType['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $childCharacterType['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Child Character Type', true), array('controller' => 'character_types', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
