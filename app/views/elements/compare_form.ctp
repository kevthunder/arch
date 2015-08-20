<?php
	$this->Html->script('jquery-ui-1.8.24.custom.min',array('inline'=>false));
	$this->Html->css('jquery-ui/jquery-ui-1.8.24.custom',null,array('inline'=>false));
	
	$targetModeId = $this->Form->domId($prefix.'targetMode');
	$this->Html->scriptBlock('
		(function( $ ) {
			$(function(){
				$( "#CompareTagetModeTabs" ).tabs({
					show:function(event, ui){
						$("input, select, textarea",this).attr("disabled",true);
						$("input, select, textarea",ui.panel).removeAttr("disabled");
						$("#'.$targetModeId.'").val($(ui.tab).attr("tagetmode"));
					}
				});
			})
		})( jQuery );
	',array('inline'=>false));
?>

<?php
	if(empty($prefix)) $prefix = '';
	/*if(empty($data)) {
		if(!empty($prefix)){
			Set::extract(rtrim($prefix,'.'),$this->data);
		}
	}*/
	echo $this->Form->input($prefix.'target', array('type'=>'text'));
	echo $this->Form->input($prefix.'operator',array('type'=>'select','options'=>$operators));
	echo $this->Form->input($prefix.'val', array('type'=>'text'));
	echo $this->Form->input($prefix.'or', array('type'=>'checkbox'));
	echo $this->SparkForm->specialValues($prefix.'emptyReturn', array('type'=>'text'));
	echo $this->SparkForm->specialValues($prefix.'matchReturn', array('type'=>'text'));
	echo $this->SparkForm->specialValues($prefix.'noMatchReturn', array('type'=>'text'));
	echo $this->Form->input($prefix.'targetMode', array('type'=>'hidden'));
	$tMode = null;
	if(!empty($data['targetMode'])){
		$tMode = $data['targetMode'];
	}
?>
<div id="CompareTagetModeTabs">
	<ul>
		<li<?php echo ($tMode == 'Direct'?' class="ui-tabs-selected"':'') ?>><a href="#TagetMode-Direct" tagetmode="Direct">Direct</a></li>
		<li<?php echo ($tMode == 'NodeRef'?' class="ui-tabs-selected"':'') ?>><a href="#TagetMode-NodeRef" tagetmode="NodeRef">NodeRef</a></li>
		<li<?php echo ($tMode == 'ForeignKey'?' class="ui-tabs-selected"':'') ?>><a href="#TagetMode-ForeignKey" tagetmode="ForeignKey">ForeignKey</a></li>
	</ul>
	<div id="TagetMode-Direct">
		<?php
			echo $this->Form->input($prefix.'path', array('type'=>'text'));
			echo $this->Form->input($prefix.'many', array('type'=>'checkbox'));
		?>
	</div>
	<div id="TagetMode-NodeRef">
		<?php
			echo $this->Form->input($prefix.'modelName', array('type'=>'text'));
			echo $this->Form->input($prefix.'field', array('type'=>'text'));
		?>
	</div>
	<div id="TagetMode-ForeignKey">
		<?php
			echo $this->Form->input($prefix.'modelName', array('type'=>'text'));
			echo $this->Form->input($prefix.'field', array('type'=>'text'));
			echo $this->Form->input($prefix.'checkVirtual', array('type'=>'checkbox'));
		?>
	</div>
</div>