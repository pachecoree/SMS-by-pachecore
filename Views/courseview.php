<?php

#show the course view

echo 'Course created correctly</br></br>';

echo '</br>Cicle : ', $course_array['ciclo'];
echo '</br>Clave de la Materia : ',$course_array['clave_materia'];
echo '</br>Subject :', $course_array['materia'];
echo '</br>Section :', $course_array['seccion'];
echo '</br>NRC :',$course_array['nrc'];
echo '</br>Profesor: ',$course_array['maestro'];
echo '</br>Horario de la Clase : </br>';
while ((list( ,$inicio) = each($course_array['inicio']))
   && (list( ,$fin) = each($course_array['fin']))
   && (list( ,$dia) = each($course_array['dia']))
   && (list( ,$horas) = each($course_array['horas']))) {
   echo '</br>Dia : ',$dia,'</br>Horario : ',$inicio,' - ',$fin,'</br>Horas: ',$horas;
   }
?>