<?php

#Show Attendance list of the course
	echo 'Materia: ', $attendance_array['subject'],' NRC: ',$attendance_array['nrc'],' Seccion: ',$attendance_array['section'],'</br></br>';
	while (list($key,$student) = each($attendance_array['attendance'])) {
		echo $student['studentid'],' | ',$student['name'],' | ',$student['attendance'],'</br>';
	}
?>