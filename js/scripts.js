
	function dias_libres_a(){
		var element_tr = document.getElementById("ciclo_libres");
		var element_nuevo = element_tr.cloneNode(true);
		element_nuevo.removeAttribute('id');
		element_tr.parentNode.insertBefore(element_nuevo,document.getElementById("btn_submit"));
	}

	function dias_libres_r(element) {
		if (element.parentNode.parentNode.getAttribute('id') != 'ciclo_libres') {
		element = element.parentNode.parentNode;
		var element_p = element.parentNode;
		element_p.removeChild(element);
		}
	}

	function rubros_a(){
		var elementos_rubros = document.getElementsByName("rubros");
		if (elementos_rubros.length >= 11) return;
		var element_tr = document.getElementById("rubros");
		var element_nuevo = element_tr.cloneNode(true);
		element_nuevo.removeAttribute('id');
		element_tr.parentNode.insertBefore(element_nuevo,document.getElementById("btn_submit"));
	}

	function rubros_r(element) {
		if (element.parentNode.parentNode.getAttribute('id') != 'rubros') {
		element = element.parentNode.parentNode;
		var element_p = element.parentNode;
		element_p.removeChild(element);
		}
	}

	function horario_a(){
		var elementos_horario = document.getElementsByName("horario");
		if (elementos_horario.length >= 3) return;
		var element_tr = document.getElementById("horario");
		var element_nuevo = element_tr.cloneNode(true);
		element_nuevo.removeAttribute('id');
		element_tr.parentNode.insertBefore(element_nuevo,document.getElementById("btn_submit"));
	}

	function horario_r(element) {
		if (element.parentNode.parentNode.getAttribute('id') != 'horario') {
		element = element.parentNode.parentNode;
		var element_p = element.parentNode;
		element_p.removeChild(element);
		}
	}

	function materia_select(element) {
		document.getElementById("txtmateria").value = element.value;
	}

	function materia_input(element) {
		element.value = element.value.toUpperCase();
		if (element.value.length == 5) {
			document.getElementById("selmateria").value = element.value;
		}
		else {
			document.getElementById("selmateria").value = -1;
		}
	}

	function maestro_select(element) {
		if (element.value.length > 0)
			document.getElementById("txtmaestro").value = element.value;
	}

	function nrc_select(element) {
		document.getElementById("txtnrc").value = element.value;
	}

	function nrc_input(element) {
		element.value = element.value.toUpperCase();
		if (element.value.length == 5) {
			document.getElementById("selnrc").value = element.value;
			$('#selnrc').trigger('change');
		}
		else {
			document.getElementById("selnrc").value = -1;
		}
	}

	function maestro_input(element) {
		element.value = element.value.toUpperCase();
		if (element.value.length >= 6 && element.value.length <= 8) {
			document.getElementById("selmaestro").value = element.value;
		}
		else {
			document.getElementById("selmaestro").value = -1;
		}
		$('#selmaestro').trigger('change');
	}

	function toUpper(element) {
		element.value = element.value.toUpperCase();
	}

	function add_fields() {
		var elementos_porcentaje = document.getElementsByName("percentage[]");
		var porcentaje = 0;
		for(i=0; i < elementos_porcentaje.length; i++) { 
        	porcentaje += Number(elementos_porcentaje[i].value);
    	}
    	if (porcentaje < 100) {
    		$("#h2rubros").html('<h3>El porcentaje no puede menor a 100</h3>');
    		return false;
    	}
    	else if (porcentaje > 100) {
    		$("#h2rubros").html('<h3>El porcentaje no puede mayor a 100</h3>');
    		return false;
    	}
    	else {
    		
    	}
	}

	function apilar_asistencia(element) {
		var id = element.id;
		if (element.checked) {
			var chk = '1';
		}
		else {
			var chk = '0';
		}
		if (document.getElementById(id+'_') != null) {
			document.getElementById(id+'_').setAttribute("value",element.getAttribute("id")+'_'+chk);
			return;
		}
		var hidden_element = document.createElement("input");
		hidden_element.setAttribute("type","hidden");
		hidden_element.setAttribute("name","cambiar_asistencia[]");
		hidden_element.setAttribute("id",id+'_');
		hidden_element.setAttribute("value",element.id+'_'+chk);
		var form = document.getElementById("form_asistencias");
		form.appendChild(hidden_element);
	}

	function regresar_viewcicle() {
		var form = document.getElementById("form");
		form.setAttribute("action", "index.php?ctrl=course&act=viewcourse");
		var nrc = document.getElementById("nrc");
		var ciclo = document.getElementById("cicle");
		form.submit();
	}
