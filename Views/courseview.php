<?php

#show the course view
echo 'Course created correctly</br></br>';

echo '</br>Cicle : ', $course_array['cicle'];
echo '</br>Clave de la Materia : ',$course_array['subject'];
echo '</br>Subject :', $course_array['subject_name'];
echo '</br>Section :', $course_array['section'];
echo '</br>NRC :',$course_array['nrc'];
echo '</br>Class Schedule : </br>';
while ((list( ,$schedule) = each($course_array['schedule']))
   && (list( ,$day) = each($course_array['days']))
   && (list( ,$hour) = each($course_array['hours']))) {
   echo 'Day ',$day,'- Hours ',$hour,' - ',$schedule,'</br>';
   }

?>