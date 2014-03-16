<?php

#Show Grade list of the course
	echo 'Materia: ', $grade_array['subject'],' NRC: ',$grade_array['nrc'],' Seccion: ',$grade_array['section'],'</br></br>';
	while (list($key,$student) = each($grade_array['grade'])) {
		echo $student['studentid'],' | ',$student['name'],' | ',$student['grade'],'</br>';
	}
?>