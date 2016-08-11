<?php
   if( (isset($_POST['action'])) && ($_POST['action'] == 'getConstants') ){
	  	
		$constantsBeforeInclude = getUserDefinedConstants();
		include($_POST['path']);
		$constantsAfterInclude = getUserDefinedConstants();
		$languageConstants = array_diff_assoc($constantsAfterInclude, $constantsBeforeInclude);
		echo json_encode($languageConstants);
		exit;
	}
  
	function getUserDefinedConstants() {
		$constants = get_defined_constants(true);
		return (isset($constants['user']) ? $constants['user'] : array());  
	}
?>