<div class="structureTypes view">
<h2><?php  __('Structure Type');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $structureType['StructureType']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Title'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $structureType['StructureType']['title']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Desc'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $structureType['StructureType']['desc']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Lft'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $structureType['StructureType']['lft']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Rght'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $structureType['StructureType']['rght']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Parent Structure Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($structureType['ParentStructureType']['title'], array('controller' => 'structure_types', 'action' => 'view', $structureType['ParentStructureType']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $structureType['StructureType']['active']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $structureType['StructureType']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $structureType['StructureType']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Structure Type', true), array('action' => 'edit', $structureType['StructureType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Structure Type', true), array('action' => 'delete', $structureType['StructureType']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $structureType['StructureType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Structure Types', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Structure Type', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Structure Types', true), array('controller' => 'structure_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Structure Type', true), array('controller' => 'structure_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Nodes', true), array('controller' => 'nodes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Node', true), array('controller' => 'nodes', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Structures', true), array('controller' => 'structures', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Structure', true), array('controller' => 'structures', 'action' => 'add')); ?> </li>
	</ul>
</div>
	<div class="related">
		<h3><?php __('Related Nodes');?></h3>
	<?php if (!empty($structureType['Node'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $structureType['Node']['id'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Parent Id');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $structureType['Node']['parent_id'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Model');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $structureType['Node']['model'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Foreign Key');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $structureType['Node']['foreign_key'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Alias');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $structureType['Node']['alias'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Lft');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $structureType['Node']['lft'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Rght');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $structureType['Node']['rght'];?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<div class="actions">
			<ul>
				<li><?php echo $this->Html->link(__('Edit Node', true), array('controller' => 'nodes', 'action' => 'edit', $structureType['Node']['id'])); ?></li>
			</ul>
		</div>
	</div>
	<div class="related">
	<h3><?php __('Related Structure Types');?></h3>
	<?php if (!empty($structureType['ChildStructureType'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Title'); ?></th>
		<th><?php __('Desc'); ?></th>
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
		foreach ($structureType['ChildStructureType'] as $childStructureType):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $childStructureType['id'];?></td>
			<td><?php echo $childStructureType['title'];?></td>
			<td><?php echo $childStructureType['desc'];?></td>
			<td><?php echo $childStructureType['lft'];?></td>
			<td><?php echo $childStructureType['rght'];?></td>
			<td><?php echo $childStructureType['parent_id'];?></td>
			<td><?php echo $childStructureType['active'];?></td>
			<td><?php echo $childStructureType['created'];?></td>
			<td><?php echo $childStructureType['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'structure_types', 'action' => 'view', $childStructureType['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'structure_types', 'action' => 'edit', $childStructureType['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'structure_types', 'action' => 'delete', $childStructureType['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $childStructureType['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Child Structure Type', true), array('controller' => 'structure_types', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Structures');?></h3>
	<?php if (!empty($structureType['Structure'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Title'); ?></th>
		<th><?php __('Strength'); ?></th>
		<th><?php __('Tile Id'); ?></th>
		<th><?php __('Structure Type Id'); ?></th>
		<th><?php __('Active'); ?></th>
		<th><?php __('Created'); ?></th>
		<th><?php __('Modified'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($structureType['Structure'] as $structure):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $structure['id'];?></td>
			<td><?php echo $structure['title'];?></td>
			<td><?php echo $structure['strength'];?></td>
			<td><?php echo $structure['tile_id'];?></td>
			<td><?php echo $structure['structure_type_id'];?></td>
			<td><?php echo $structure['active'];?></td>
			<td><?php echo $structure['created'];?></td>
			<td><?php echo $structure['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'structures', 'action' => 'view', $structure['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'structures', 'action' => 'edit', $structure['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'structures', 'action' => 'delete', $structure['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $structure['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Structure', true), array('controller' => 'structures', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
