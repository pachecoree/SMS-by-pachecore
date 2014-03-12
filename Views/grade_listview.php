<?php

#Show Grade list of the course
	while (list($key,$student) = each($grade_array)) {
		echo $student['codigo'],' | ',$student['nombre'],' | ',$student['calificacion'],'</br>';
	}
?>