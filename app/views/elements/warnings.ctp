<?php App::import('Lib', 'StatusCodes'); 
if(!empty($warnings)){
	foreach($warnings as $warningNo) { 
		$warning = StatusCodes::getCode($warningNo);
		?>
			<warning code="<?php echo $warning['no'] ?>"><?php echo $warning['msg'] ?></warning>
		<?php
	}
} 
?>