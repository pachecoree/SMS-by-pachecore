<?php

#show the cicle view
echo '</br>Ciclo : ',$ciclo,' - Activo ';
echo '</br>Fecha de inicio del curso : ',date_format($fechainicial,'d/m/Y');
echo '</br>Fecha de fin del curso : ',date_format($fechafinal, 'd/m/Y');
if (isset($non_workingarray)) {
	echo '</br>Dias que no se laboran : </br>';
	while (list($key,$date) = each($non_workingarray)) {
		echo '</br>', date_format($date, 'd/m/Y');
	}
}

?>