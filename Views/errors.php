<?php

class errors {

	function error_add_course() {
		echo 'Could not add course';
	}

	function error_add_sheet() {
		echo 'Could not add sheet';
	}

	function error_add_cicle($cicle) {
		echo 'Could not add "',$cicle,'" cicle';
	}

	function error_add_field($field) {
		echo 'Could not add "',$field,'" field to course';
	}
	function error_add_student($fullname) {
		echo 'Could not add "',$fullname,'" ';
	}

	function error_add_student_course($fullname) {
		echo 'Could not add Student: ',$fullname ,' to Course';	
	}

	function error_capture_grade() {
		echo 'Error capturing grade';
	}

	function not_valid_schedule() {
		echo 'Could not validate Class Schedule';
	}
	function date_range_fail() {
		echo 'End date can not be before Begin Date';
	}

	function error_query_list($course) {
		echo 'Could not find course "',$course,'"';
	}

	function student_not_found($studentid) {
		echo 'Could not find Student with ID : ' ,$studentid ;
	}

	function not_valid_date() {
		echo 'Error: Date(s) not valid';
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