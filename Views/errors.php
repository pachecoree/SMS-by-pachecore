<?php

class errors {

	function error_add_course() {
		echo 'Could not add course';
	}

	function error_add_sheet() {
		echo 'Could not add sheet';
	}

	function error_login_data() {
		echo 'Incorrect User ID or Password';
	}

	function session_active() {
		echo 'Theres already a user logged in';
	}

	function not_modify_cicle($cicle) {
		echo 'Could not modify "',$cicle,'" status';
	}

	function not_modify_student_status($studentid) {
		echo 'Could not modify "',$studentid,'" status';
	}

	function notstudents_att() {
		echo 'No students attendance registries were modified';
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

	function date_not_valid($date) {
		echo $date ,'is not a valid date';
	}

	function not_logged_in() {
		echo 'Please log in first';
	}

	function not_valid_usertype() {
		echo 'You do not have the privileges to perform that action';
	}

	function not_valid_userid($user) {
		echo $user, ' ID is not valid';
	}

	function student_in_course($studentid) {
		echo 'Student is already enrolled in this course';
	}

	function module_disabled() {
		echo 'This module has been disabled';
	}
}


?>