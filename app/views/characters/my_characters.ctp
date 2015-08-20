<architecturers>
	<?php if(!empty($characters)){
		foreach($characters as $char) { ?>
		<character 
			id="<?php echo $char['Character']['id'] ?>" 
			tile_id="<?php echo $char['Character']['tile_id'] ?>" 
			name="<?php echo $char['Character']['name'] ?>"></character>
	<?php }} ?>
	<?php echo $this->element('warnings') ?>
</architecturers>