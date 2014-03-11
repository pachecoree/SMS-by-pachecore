<?php

class studentMdl {
	
	function add_student($student) {
		#Gets the student info
		#Goes to the DB to add the student, and add the "Active" status
		#will return array if it was succesfull , false if it fails
		$student['estado'] = "Activo";
		return $student;
	}
}

?>