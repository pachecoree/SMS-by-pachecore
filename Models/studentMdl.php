<?php

class studentMdl {
	
	function add_student($student) {
		#Gets the student info
		#Goes to the DB to add the student, and add the "Active" status
		#will return array if it was succesfull , false if it fails
		$student['estado'] = "Activo";
		return $student;
	}

	function view_student_grades($studentid,$cicle) {
		#Gets the Student ID and Cicle
		#Goes to the DB to search for the Student, and gets all his active courses info
		#will return array if it found it, false if not.

		$grades_info[] = array ("materia" => "Taller de Compiladores", "calificacion" => 86.4);
		$grades_info[] = array ("materia" => "Topicos Selectos de Computacion III","calificacion" => 81.2);
		$grades_info[] = array ("materia" => "Organizacion de Computadoras","calificacion" => 96);
		$grades_info[] = array ("materia" => "Programacion Logica y Funcional","calificacion" => 78.8);
		return $grades_info;
	}

	function view_student_course($studentid,$courseid) {
		#Gets the Student ID and Course
		#Goes to the DB to search for the Student, and gets all his course information (attendance and grades)
		#will return array if it found it, false if not.

		$course_info[] = array ("materia" => "Taller de Compiladores", "calificacion" => 86.4,"asistencia" => " * | * | * | * | * | * | * | * |");
		$course_info[] = array ("materia" => "Topicos Selectos de Computacion III","calificacion" => 81.2,"asistencia" => " * | * | * | * | * | / | / | / |");
		$course_info[] = array ("materia" => "Organizacion de Computadoras","calificacion" => 96,"asistencia" => " * | * | * | * | * | * | * | * |");
		$course_info[] = array ("materia" => "Programacion Logica y Funcional","calificacion" => 78.8,"asistencia" => " / | / | / | * | * | / | / | * |");
		return $course_info;
	}

}

?>