<?php

echo 'Student '.$student['action'].' correctly</br></br>';
if (isset($student['generica']))
	echo 'Se ha mandado un mensaje al correo del alumno con la informacion de su cuenta';
echo '</br></br>Codigo: ', $student['codigo'];
echo '</br>Estado : ',$student['estado'];
echo '</br>Carrera: ',$student['carrera'];
echo '</br>Nombre : ',$student['nombre'];
echo '</br>Correo Electronico: ',$student['correo'];
if (isset($student['github'])) echo '</br>Github: ',$student['github'];
if (isset($student['celular'])) echo '</br>Numero de Celular: ',$student['celular'];
if (isset($student['web'])) echo '</br>Pagina Web: ',$student['web'];
?>