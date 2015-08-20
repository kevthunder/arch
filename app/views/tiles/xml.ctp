<architecturers>
	<?php foreach($tiles as $tile) { ?>
		<tile
			id="<?php echo $tile['Tile']['id'] ?>" 
			x="<?php echo $tile['Tile']['x'] ?>" 
		    y="<?php echo $tile['Tile']['y'] ?>" 
			top_id="<?php echo $tile['Tile']['top_id'] ?>" 
			right_id="<?php echo $tile['Tile']['right_id'] ?>" 
			bottom_id="<?php echo $tile['Tile']['bottom_id'] ?>" 
			left_id="<?php echo $tile['Tile']['left_id'] ?>">
		</tile>
	<?php } ?>
</architecturers>