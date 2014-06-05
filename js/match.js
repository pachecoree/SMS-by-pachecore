	function validate() {
		var elementos_error = document.getElementsByName("error");
    	if (elementos_error.length > 0) {
    		$("#mensaje_error").html('<h3>Los Campos marcados no son validos!</h3>');
    		return false;
    	}
	}

	function validate_Sid(element) {
		if (element.value.length == 0) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
			return;
		}
		else if (element.value.match(/^[0-9d][0-9]{8}$/i)) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
		}
		else {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
	}

	function validate_Tid(element) {
		if (element.value.length == 0) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
			return;
		}
		else if (element.value.match(/^[0-9d][0-9]{5,7}$/i)) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
		}
		else {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
	}

	function validate_grade(element) {

		if (element.value.length == 0) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
			return;
		}
		else if (element.value >= 0 && element.value <= 10) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
		}
		else {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
	}
	

	function validate_field(element) {
		if (element.value.length == 0) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
			return;
		}
		else if (element.value.match(/^[1-9 a-z]{1,}$/i)) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
		}
		else {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
	}


	function validate_name(element) {
		if (element.value.length == 0) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
			return;
		}
		else if (element.value.match(/^[a-z ]{1,}$/i)) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
		}
		else {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
	}

	function validate_github(element) {
		if (element.value.length == 0) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
			return;
		}
		else if (element.value.match(/^[a-z0-9][a-z0-9-]{1,}$/i)) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
		}
		else {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
	}

	function validate_phone(element) {
		if (element.value.length == 0) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
			return;
		}
		else if (element.value.match(/^33([0-9]{2}){4}$/)) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
		}
		else {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
	}

	function validate_cicle(element) {
		if (element.value.length == 0) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
			return;
		}
		else if (element.value.match(/^[2][0-9]{3}[ABV]$/i)) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
		}
		else {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
	}	
	
	function validate_nrc(element) {
		if (element.value.length == 0) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
			return;
		}
		else if (element.value.match(/^[0-9]{5}$/)) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
		}
		else {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
	}

	function validate_section(element) {
		if (element.value.length == 0) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
			return;
		}
		else if (element.value.match(/^d[0-9]{2}$/i)) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
		}
		else {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
	}

	function validate_enddate(element) {
		if (document.getElementById("begindate").value < document.getElementById("enddate").value) {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
		}
		else {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
	}

	function validate_nonw(element) {
		if (document.getElementById("begindate").value > element.value) {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
		else if (document.getElementById("enddate").value < element.value) {
			element.parentNode.setAttribute("class","form-group has-error");
			element.parentNode.setAttribute("name","error");
		}
		else {
			element.parentNode.setAttribute("class","form-group");
			element.parentNode.removeAttribute("name");
		}
	}