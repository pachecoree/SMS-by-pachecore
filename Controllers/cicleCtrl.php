<?php


class cicleCtrl{

	function __construct($driver) {
		#Constructor
		require('Models/cicleMdl.php');
		#Create Model object
		$this -> cicleMdl = new cicleMdl($driver);
		#Create the errors object 
		require('Views/errors.php');
		$this -> errors = new errors();
		#Create the validation object 
		require('Controllers/validationCtrl.php');
		$this -> validation = new validationCtrl();
		require('Controllers/mailCtrl.php');
		$this -> emailCtrl = new mailCtrl();
		require('Controllers/templatesCtrl.php');
		$this -> templateCtrl = new templatesCtrl();
	}

	function run() {
		#Check if the activity was input
		if (isset($_GET['act'])) {
			switch ($_GET['act']) {
				case 'new':
					$session = $this -> validation -> active_session();
					if ($session >= 3) {
						$footer = file_get_contents('Views/Footer.html');
						$header = file_get_contents('Views/Head.html');
						$content = file_get_contents('Views/addcicle.html');
						$content = $this -> templateCtrl -> get_menu($content);
						echo $header . $content . $footer;
					}
					else if ($session == false) {
						$header = file_get_contents('Views/Head.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = file_get_contents('Views/login.html');
						$content = $this -> templateCtrl -> procesarPlantilla_login($content,-1);
						echo $header.$content.$footer;
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;
				case 'add':
					$session = $this -> validation -> active_session();
					if ($session >= 3) {
						#Check if cicle exists
						if (isset($_POST['cicle'])) {
							#Validate if cicle is correct
							if ($this -> validation -> validate_cicle($_POST['cicle'])) {
								#The cicle format is correct
								#Check if both start and end of cicle dates exists
								if (isset($_POST['begindate']) && isset($_POST['enddate']) && $_POST['begindate'] != "" && $_POST['enddate'] != "") {
									#Validate if dates are correct
									$fechainicial = $this -> validation -> validate_date($_POST['begindate']);
									$fechafinal = $this -> validation -> validate_date($_POST['enddate']);
									if (!($this -> validation -> compare_dates($fechainicial,$fechafinal))) {
										$this -> errors -> date_range_fail();
										return;
									}
									if ( (!is_bool($fechainicial)) && (!is_bool($fechafinal))) {
										#Check if non working days exists
										if (isset($_POST['nonworking'])) {
											$non_workingarray = array();
											if (is_array($_POST['nonworking'])) {
												while (list($key,$date) = each($_POST['nonworking'])) {
													$date_ = $this->validation -> validate_nonworking($date,$fechainicial,$fechafinal);
													if ($date_) {
														$non_workingarray[] = new DateTime($date);
													}
													else {
														$this -> errors ->date_not_valid($date);
														return;
													}
												}
											}
											else {
												$date = $this -> validation -> validate_date($_POST['nonworking']);
												if (!is_bool($date)) {
													$non_workingarray[] = $date;
												}
												else {
													$this -> errors -> not_valid_date($date);
													return;
												}
											}
										}
										#Send the data to the model
										$ciclo = $_POST['cicle'];
										$cicle_array = array ( 'clave_ciclo' => $ciclo,
															   'inicio' => $fechainicial,
															   'fin' => $fechafinal);
										$cicle_return = $this -> cicleMdl -> add_cicle($cicle_array,$non_workingarray);
										if ($cicle_return == true) {
											#Cicle created correctly
											//require('Views/cicleview.php');//
											$footer = file_get_contents('Views/Footer.html');
											$header = file_get_contents('Views/Head.html');
											$content = file_get_contents('Views/cicleview.html');
											$content = $this -> templateCtrl -> get_menu($content);
											$ciclos = $this -> cicleMdl -> std_obj -> get_all_cicles();
											$cicle_array = $this -> cicleMdl -> std_obj -> get_cicleinfo(null);
											$non_workingarray = $this -> cicleMdl -> std_obj -> get_nonworking($cicle_array['clave_ciclo']);
											$content = $this -> templateCtrl -> procesarPlantilla_cicleview($content,$cicle_array,$non_workingarray,$ciclos);
											echo $header . $content . $footer;
										}
										else {
											#Cicle creation failed
											$footer = file_get_contents('Views/Footer.html');
											$header = file_get_contents('Views/Head.html');
											$content = file_get_contents('Views/error.html');
											$content = $this -> templateCtrl -> get_menu($content);
											$mensaje = "No se pudo Agregar el Ciclo ".$_POST['cicle'];
											$content = str_replace("{{'mensaje-error'}}",$mensaje ,$content);
											echo $header .$content.$footer;
										}
										return;
									}
									else {
										#At least one date is not valid
										$this -> errors -> not_valid_date();
									}
								}
								else {
									#At least one date is missing
									$this -> errors -> not_found_input('Cicle Date(s)');
								}
							}
							else {
								#Cicle format is incorrect
								$this -> errors -> not_valid_format($_POST['cicle'],'cicle');
							}
						}
						else {
							#Cicle was not input
							$this -> errors -> not_found_input('Cicle');

							return;
						}
					}
					else if ($session == false) {
						$header = file_get_contents('Views/Head.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = file_get_contents('Views/login.html');
						$content = $this -> templateCtrl -> procesarPlantilla_login($content,0);
						echo $header.$content.$footer;
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;
			case 'view_cicle' :
				$session = $this -> validation -> active_session();
				if ($session >= 1) {
					$footer = file_get_contents('Views/Footer.html');
					$header = file_get_contents('Views/Head.html');
					$content = file_get_contents('Views/cicleview.html');
					$content = $this -> templateCtrl -> get_menu($content);
					if (isset($_POST['ciclo'])) {
						$cicle_array = $this -> cicleMdl -> std_obj -> get_cicleinfo($_POST['ciclo']);
						$non_workingarray = $this -> cicleMdl -> std_obj -> get_nonworking($cicle_array['clave_ciclo']);
						echo $this -> templateCtrl -> procesarPlantilla_cicleviewcontent($content,$cicle_array,$non_workingarray);
					}
					else {
						$cicle_array = $this -> cicleMdl -> std_obj -> get_cicleinfo(null);
					$cicles = $this -> cicleMdl -> std_obj -> get_all_cicles();
					$non_workingarray = $this -> cicleMdl -> std_obj -> get_nonworking($cicle_array['clave_ciclo']);
					$content = $this -> templateCtrl -> procesarPlantilla_cicleview($content,$cicle_array,$non_workingarray,$cicles);
					echo $header . $content . $footer;
					}
				}
				else if ($session == false) {
					$header = file_get_contents('Views/Head.html');
					$footer = file_get_contents('Views/Footer.html');
					$content = file_get_contents('Views/login.html');
					$content = $this -> templateCtrl -> procesarPlantilla_login($content,0);
					echo $header.$content.$footer;
				}
				break;
			case 'modify':
					#Disabling module
					$this -> errors -> module_disabled();
					die();
					$session = $this -> validation -> active_session();
					if ($session >= 3) {
						#Check if cicle exists
						if (isset($_GET['cicle'])) {
							#Validate cicle
							if ($this -> validation -> validate_cicle($_GET['cicle'])) {
								#Check if status modifier exists
								if (isset($_GET['status'])) {
									#Validate status
									$value = $this -> validation ->  validate_ciclestatus($_GET['status']);
									if ($value == 0) {
										#Send data to the model
										$return_value = $this -> cicleMdl -> modify_status($_GET['cicle'],$_GET['status']);
										#Create the array
										$status_array = array("status" => $_GET['status'],
															  "cicle" => $_GET['cicle']);
										if ($return_value) {
											#Get the View
											require('Views/modify_cicle_statusview.php');
										}
										else {
											$this -> errors -> not_modify_cicle($_GET['cicle']);
										}
									}
									else {
										#Status modifier is not valid
										$this -> errors -> not_valid_format($_GET['status'],'Status');
										return;
									}
								}
								else {
									#Status Modifier was not input
									$this -> errors -> not_found_input('Status');
								}
							}
							else {
								#Cicle format is incorrect
								$this -> errors -> not_valid_format($_GET['cicle'],'Cicle');
							}
						}
						else {
							#Cicle was not input
							$this -> errors -> not_found_input('Cicle');
						}
					}
					else if ($session == false) {
						$this -> errors -> not_logged_in();
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;

			default:
					#Activity is not valid
					$this -> errors -> not_valid_input($_GET['act'],'Activity');
					break;
			}
		}
		else {
			#Act was not input
			$this -> errors -> not_found_input('Activity');
		}
	}
}

?>