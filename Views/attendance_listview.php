<?php

#Show Attendance list of the course
	while (list($key,$student) = each($attendance_array)) {
		echo $student['codigo'],' | ',$student['nombre'],' | ',$student['asistencia'],'</br>';
	}
?>