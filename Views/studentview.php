<?php

echo 'Student added correctly';

echo '</br></br>Codigo: ', $student['studentid'];
echo '</br>Estado : ',$student['status'];
echo '</br>Carrera: ',$student['career'];
echo '</br>Nombre : ',$student['name'];
echo '</br>Correo Electronico: ',$student['email'];
if (isset($student['github'])) echo '</br>Github: ',$student['github'];
if (isset($student['cellphone'])) echo '</br>Numero de Celular: ',$student['cellphone'];
if (isset($student['web'])) echo '</br>Pagina Web: ',$student['web'];

?>