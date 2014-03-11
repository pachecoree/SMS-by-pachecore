<?php

class errors {

	function error_add_course($course) {
		echo 'Could not add "',$course,'" course';
	}

	function error_add_cicle($cicle) {
		echo 'Could not add "',$cicle,'" cicle';
	}

	function not_found_input($input) {
		#Recieves the input and displays the error
		echo 'Could not find "',$input,'"';
	}

	function not_valid_input($input,$case) {
		#Recieves the input and displays the error
		echo $case,': "',$input,'" is not valid';
	}

	function not_valid_format($input,$case) {
		#Recieves the input and displays the error
		echo $case,': ',$input ,' format is not valid';
	}
}


?>