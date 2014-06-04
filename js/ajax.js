function nuevoAjax() {
	var xmlhttp = false;
	try {
		//Creacion de objecto AJAX para navegadores no IE
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	}
	catch(e) {
		try {
			//creacion del objecto AJAX para IE
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch(e) {
			if (!xmlhttp & typeof XMLHttpRequest!='undefined')
				xmlhttp= new XMLHttpRequest();
		}
	}
	return xmlhttp;
}

function get_ciclo_info(elemento) {
	var ajax = new nuevoAjax();
	ajax.open('post','?ctrl=cicle&act=view_cicle',true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

	var id = elemento.value;
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 4) {
			var contenido = ajax.responseText;
			$( "#periodo" ).replaceWith(contenido);

		}
	}
	ajax.send('ciclo='+id);
}

function get_nrc() {
	var ajax = new nuevoAjax();
	ajax.open('post','?ctrl=course&act=search',true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

	var ciclo = document.getElementById("ciclo").value;
	var codigo_maestro = document.getElementById("selmaestro").value;

	ajax.onreadystatechange = function() {
		if (ajax.readyState == 4) {
			var contenido = ajax.responseText;
			$("#selnrc").empty();
			$("#selnrc").html(contenido);
		}
	}

	ajax.send('ciclo='+ciclo+'&teacher_id='+codigo_maestro);
}

function buscarAlumno() {
	var ajax = new nuevoAjax();
	ajax.open('post','?ctrl=student&act=viewinfo',true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

	ajax.onreadystatechange = function() {
		if (ajax.readyState == 4) {
			var contenido = ajax.responseText;
			$("#alumnos").empty();
			$("#alumnos").html(contenido);
		}
	}
	var radio_seleccionado = $('input[name="radiobusc"]:checked').val();
	var txtbuscar = $('#txtbuscar').val();
	ajax.send('radiobusc='+radio_seleccionado+'&txtbuscar='+txtbuscar);
}

	function popup_curso() {
		$('#opciones_estudiante').hide();
		$('#contenedor_med').hide();
		$('#info_curso').show();
		$('#agregar_curso').show();

		var ajax = nuevoAjax();
		ajax.open('post','?ctrl=course&act=addstudent',true);
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

		ajax.onreadystatechange = function() {
			if (ajax.readyState == 4) {
				var contenido = ajax.responseText;
				$('#agregar_curso').empty();
				$('#mensaje').empty();
				$('#agregar_curso').html(contenido);
				$('#selnrc').trigger('change');
				$('#mensaje').empty();
			}
		}
		var sid = $('#codigoAlumno').text();
		ajax.send('agrega_curso=1&studentid='+sid); 
	}

	function mostrar_nrc_info(elemento) {
		var ajax = nuevoAjax();
		ajax.open('post','?ctrl=course&act=addstudent',true);
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

		ajax.onreadystatechange = function() {
			if (ajax.readyState == 4) {
				var contenido = ajax.responseText;
				$('#info_curso').empty();
				$('#info_curso').html(contenido);
			}
		}
		var nrc = $('#selnrc').val();
		ajax.send('agrega_curso=1&nrc='+nrc);
	}

	function Agregar_aCurso() {
		$('#opciones_estudiante').show();
		$('#contenedor_med').show();
		$('#info_curso').hide();
		$('#agregar_curso').hide();
		$('#mensaje').empty();
		var ajax = nuevoAjax();
		ajax.open('post','?ctrl=course&act=addstudent',true);
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

		ajax.onreadystatechange = function() {
			if (ajax.readyState == 4) {
				var contenido = ajax.responseText;
				contenido = '<div class="container" id="mensaje"><h2>'+contenido+'</h2></div>';
				$( contenido ).insertBefore( $( "#contenedor_med" ) );
			}
		}
		var sid = $('#codigoAlumno').text();
		var nrc = $('#selnrc').val();
		ajax.send('studentid='+sid+'&nrc='+nrc); 
	}

	function Cancelar_aCurso() {
		$('#opciones_estudiante').show();
		$('#contenedor_med').show();
		$('#info_curso').hide();
		$('#agregar_curso').hide();
		$('#mensaje').empty();
	}

	function cursos_alumno() {
		var ajax = nuevoAjax();
		ajax.open('post','?ctrl=student&act=list',true);
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

		ajax.onreadystatechange = function() {
			if (ajax.readyState == 4) {
				var contenido = ajax.responseText;
				$('#materias').empty();
				$('#materias').html(contenido);
			}
		}
		var ciclo = $('#ciclo').val();
		ajax.send('ciclo='+ciclo);
	}

	function busca_evaluacion() {
		var ajax = nuevoAjax();
		ajax.open('post','?ctrl=course&act=capture',true);
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		ajax.onreadystatechange = function() {
			if (ajax.readyState == 4) {
				var contenido = ajax.responseText;
				$('#detalles_rubro').empty();
				$('#detalles_rubro').html(contenido);
				$('#detalles_rubro').attr("align","center");
			}
		}
		var clave_rubro = $('#fields').val();
		var codigo_alumno = $('#studentid').val();
		var nrc = $('#h_nrc').val();
		var ciclo = $('#h_ciclo').val();
		$('#h_studentid').attr("value",codigo_alumno);
		$('#h_field').attr("value",clave_rubro);
		ajax.send('details="1"&clave_rubro='+clave_rubro+'&studentid='+codigo_alumno+'&nrc='+nrc+'&ciclo='+ciclo);
	}