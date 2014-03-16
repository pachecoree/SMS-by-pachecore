<?php

class studentMdl {
	
	function add_student($student) {
		#Gets the student info
		#Goes to the DB to add the student, and add the "Active" status
		#will return array if it was succesfull , false if it fails
		$student['status'] = "Activo";
		return $student;
	}

	function view_student_grades($studentid,$cicle) {
		#Gets the Student ID and Cicle
		#Goes to the DB to search for the Student, and gets all his active courses info
		#will return array if it found it, false if not.
		$grades_info['name'] = "Carlos Mauricio Romero Pacheco";
		$grades_info['studentid'] = "211213995";
		$grades_info['career'] = "Computacion";
		$grades_info['grades'][] = array ("materia" => "Taller de Compiladores", "calificacion" => 86.4);
		$grades_info['grades'][]= array ("materia" => "Topicos Selectos de Computacion III","calificacion" => 81.2);
		$grades_info['grades'][] = array ("materia" => "Organizacion de Computadoras","calificacion" => 96);
		$grades_info['grades'][] = array ("materia" => "Programacion Logica y Funcional","calificacion" => 78.8);
		return $grades_info;
	}

	function view_student_course($studentid,$courseid) {
		#Gets the Student ID and Course
		#Goes to the DB to search for the Student, and gets all his course information (attendance and grades)
		#will return array if it found it, false if not.
		$course_info['name'] = "Carlos Mauricio Romero Pacheco";
		$course_info['studentid'] = "211213995";
		$course_info['career'] = "Computacion";
		$course_info['grades'][] = array ("materia" => "Taller de Compiladores", "calificacion" => 86.4,"asistencia" => " * | * | * | * | * | * | * | * |");
		return $course_info;
	}

	function modify_status($studentid,$value) {
		#Gets the Student ID
		#Goes to the DB to search for the Student, and gets all his information
		#Will change its status , to the one received
		#Return array if it was succesfully changed, or false if it failed
		$student_array = array("studentid" => $studentid,
							   "status" => $value,
							   "name" => "Pedro Ramirez Lopez");
		return $student_array;
	}

}

?>