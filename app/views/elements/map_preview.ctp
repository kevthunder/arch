<?php
	$this->Html->css('map_preview',null,array('inline'=>false));
?>
<?php
	//debug($tiles);
?>

<div class="mapPreview" style="width:<?php echo $w*8 ?>px;height:<?php echo $h*8 ?>px;">
<?php foreach ($tiles as $tile) { 
	$moreClass = '';
	if(!empty($styled)){
		foreach($styled as $class => $list) { 
			if(in_array($tile['Tile']['id'],$list)){
				$moreClass .= ' '.$class ;
			}
		}
	}
?>
	<div id="Tile<?php echo $tile['Tile']['id'] ?>" class="tile <?php echo ($tile['Tile']['tile_type_id'] == 1?($tile['Tile']['pathing_cache']['walk']?'land':'blocked'):'void').$moreClass ?>" style="left:<?php echo ($tile['Tile']['x']-$x)*8 ?>px;top:<?php echo ($tile['Tile']['y']-$y)*8 ?>px;"></div>
<?php } ?>
</div>