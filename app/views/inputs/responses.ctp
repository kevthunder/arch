<architecturers time="<?php echo microtime(true) ?>" >
<?php foreach($responses as $response){
	if(!empty($response['data'])){
		$data = $this->XmlLink->dataSplitAttrib($response['data']);
	}	
	?>
	
	<response no="<?php echo $response['no'] ?>"<?php if(!empty($data)) echo $this->XmlLink->xmlAttrib($data['attrib']) ?>>

	<?php if(!empty($response['items'])){ ?>
		<items>
			<?php echo $this->XmlLink->makeItemXml($response['items']); ?>
		</items>
	<?php 
		} 
		if(!empty($data)){
			echo $this->XmlLink->makeItemXml($data['collections']);
		}
		echo $this->element('warnings',array('warnings'=>$response['warnings'])); 
	?>

	</response>
<?php } ?>

</architecturers>