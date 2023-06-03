<?php
	/**
	*	Функция делает var_dump переменных, поданых как аргументы
	*	@return void
	*/
	function ShowVar( ) {
		$iNum = intval( func_num_args( ) );
		if ( $iNum ) {
			$arrArgs = func_get_args( );
			echo "<pre>";
			call_user_func_array( "var_dump", $arrArgs );
			echo "</pre>";
		}
	}
	
	/**
	*	Функция делает var_dump переменных, поданых как аргументы, и die
	*	@return void
	*/
	function ShowVarD( ) {
		$iNum = intval( func_num_args( ) );
		if ( $iNum ) {
			$arrArgs = func_get_args( );
			echo "<pre>";
			call_user_func_array( "var_dump", $arrArgs );
			echo "</pre>";
		}
		die( );
	}
?>