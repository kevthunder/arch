<?php
	$this->Html->scriptBlock('
		(function( $ ) {
			$(function(){
				$("#EventHandler").change(function(){
					$("#EventHandler").addClass("loading_ajax");
					$(".functionInput").load("'.$this->Html->url(array('action'=>'function_list')).'/"+$("#EventHandler").val(), function(result, status){
						$("#EventHandler").removeClass("loading_ajax");
					});
				});
				$("#EventFunction").live("change",function(){
					$("#EventFunction").addClass("loading_ajax");
					$(".handlerParams").load("'.$this->Html->url(array('action'=>'handler_form')).'/"+$("#EventHandler").val()+"/"+$("#EventFunction").val()'.(!empty($this->data['Event']['id'])?'+"/"+'.$this->data['Event']['id']:'').', function(result, status){
						$("#EventFunction").removeClass("loading_ajax");
					});
				});
			})
		})( jQuery );
	',array('inline'=>false));
?>
<div class="events form">
<?php echo $this->Form->create('Event');?>
	<fieldset>
		<legend><?php __('Admin Edit Event'); ?></legend>
	<?php
		echo $this->Form->input('active');
		echo $this->Form->input('id');
		echo $this->Form->input('aro_id',array('empty'=>'--root--'));
		echo $this->Form->input('aco_id',array('empty'=>'--root--'));
		echo $this->Form->input('handler');
	?>
	<div class="functionInput">
		<?php echo $this->Form->input('function',array('empty'=>true)); ?>
	</div>
	<div class="handlerParams">
		<?php echo $this->element('handler_form',array()); ?>
	</div>
	<?php
		echo $this->Form->input('event_type_id');
		echo $this->Form->input('phase');
		//echo $this->Form->input('conditions');
		//echo $this->Form->input('params');
		echo $this->Form->input('log');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Event.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Event.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Events', true), array('action' => 'index'));?></li>
	</ul>
</div>