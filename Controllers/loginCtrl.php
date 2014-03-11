<?php

	class loginCtrl {
		#constructor function
		function __construct() {
			#get the Model create loginMdl object 
			require('Models/loginMdl.php');
			$this -> mdl_obj = new loginMdl();
		}

		function validate_userid (){
			#Check if userid was input 
			if (isset($_GET['userid'])) {
				#Validate if userid is correct
				if ($this -> mdl_obj -> get_userid(strtolower($_GET['userid']))) {
					#Userid is correct
					return true;
				}
			}
			else {
				#Userid was not input
				echo 'Could not find UserID in this context</br>';
			}
			return false;
		}

		function validate_password() {
			#Check if password was input
			if (isset($_GET['password'])) {
				#Validate if password is correct
				if ($this -> mdl_obj -> get_password($_GET['password'])) {
					#Password is correct
					return true;
				}
			}
			else {
				#Password was not input
				echo 'Could not find Password in this context</br>';
			}
			return false;
		}

		function validate_login_data() {
			#Callback to validate functions
			if ($this -> validate_userid()) {
				if ($this -> validate_password()) {
					#Show view
					require('Views/loginCorrectly.php');
					return true;
				}
			}
			#Login validation was unsuccesful
			return false;
		}
	}
?>