<?php

class courseMdl {

	function __construct() {

	}

	function add_course($course_info) {
		#Go the the DB and add the course
		#It will return true if it is succesful or false if it fails 
		return true;
	}

	function add_student_to_course($studentid,$courseid) {
		#Go to the DB and get student to be added
		#Will return false if the student was not found, or return full name if an error ocurred while adding
		#Return array with student information to confirm the it was added to a course
		$student = array ('studentid' => $studentid, "name" => "Carlos Mauricio Romero Pacheco");
		$student['subject'] = "Topicos Selectos de Computacion";
		$student['section'] = "D27";
 		return $student;
	}

	function view_course_attendance($courseid) {
		#Go to the DB and get all students in actual the course
		#Will return false if the course was not found, or return an array containing all students in
		#the course and the attendance list
		$course['subject'] = "Taller de Compiladores";
		$course['section'] = "D04";
		$course['nrc'] = "91780";
		$course['attendance'][] = array ("studentid" => 211213995, "name" => "Romero Pacheco Carlos Mauricio","attendance" => "* | * | / | * | / | * | * | *");
		$course['attendance'][] = array ("studentid" => 210519152, "name" => "Villanueva Venegas Neo Octavio","attendance" => "/ | / | / | * | / | * | * | *");
		return $course;
	}

	function view_course_grade($courseid) {
		#Go to the DB and get all students in actual the course
		#Will return false if the course was not found, or return an array containing all students in
		#the course with grades
		$course['subject'] = "Ingenieria de Software";
		$course['section'] = "D01";
		$course['nrc'] = "14580";
		$course['grade'][] = array ("studentid" => 211213995, "name" => "Romero Pacheco Carlos Mauricio","grade" => "9.1");
		$course['grade'][] = array ("studentid" => 210519152, "name" => "Villanueva Venegas Neo Octavio","grade" => "7.3");
		return $course;
	}

	function add_field_to_course($field_array) {
		#Go to the DB and get the course info
		#Validate percentage value is accepted by getting percentage total and adding the new one , and making sure its equal or less than 100
		#Then proceed to the insertion of the new field
		#Return the array if the field was succesfully added , or false if it failed
		$field_array['subject'] = "Topicos Selectos de Computacion I";
		return $field_array;
	}

	function add_grade_to_field($field_array) {
		#Go to the DB and get the course's and student's info
		#Add the grade to the field of the student's course
		#Return true the grade was succesfully captured , or false if it failed

		return true;
	}
}

?>