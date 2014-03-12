<?php

class courseMdl {

	function __construct() {

	}

	function add_course($course_info) {
		#Go the the DB and add the course
		#It will return true if it is succesful or false if it fails 
		return true;
	}

	function add_student_to_course($studentid) {
		#Go to the DB and get student to be added
		#Will return false if the student was not found, or return full name if an error ocurred while adding
		#Return array with student information to confirm the it was added to a course
		$student = array ('studentid' => $studentid, 'fullname' => "Romero Pacheco Carlos Mauricio");
		return $student;
	}

	function view_course_attendance($courseid) {
		#Go to the DB and get all students in actual the course
		#Will return false if the course was not found, or return an array containing all students in
		#the course and the attendance list
		$course[] = array ("codigo" => 211213995, "nombre" => "Romero Pacheco Carlos Mauricio","asistencia" => "* | * | / | * | / | * | * | *");
		$course[] = array ("codigo" => 210519152, "nombre" => "Villanueva Venegas Luis Guillermo","asistencia" => "/ | / | / | * | / | * | * | *");
		return $course;
	}

	function view_course_grade($courseid) {
		#Go to the DB and get all students in actual the course
		#Will return false if the course was not found, or return an array containing all students in
		#the course with grades
		$course[] = array ("codigo" => 211213995, "nombre" => "Romero Pacheco Carlos Mauricio","calificacion" => "9.1");
		$course[] = array ("codigo" => 210519152, "nombre" => "Villanueva Venegas Luis Guillermo","calificacion" => "7.3");
		return $course;
	}
}

?>