<?php

class studentCtrl {

	function __construct($driver) {
		#Create the errors object
		require('Views/errors.php');
		$this -> errors = new errors();
		#Create the validation object
		require('Controllers/validationCtrl.php');
		$this -> validation = new validationCtrl();
		require('Controllers/templatesCtrl.php');
		$this -> templateCtrl = new templatesCtrl();
		require('Controllers/mailCtrl.php');
		$this -> emailCtrl = new mailCtrl();
		require('Models/studentMdl.php');
		$this -> obj_mdl = new studentMdl($driver);
	}

	function genera_password($length = 5) {
	    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@';
	    $rdmpass = '';
	    for ($i = 0; $i < $length; $i++) {
	        $rdmpass .= $caracteres[rand(0, strlen($caracteres) - 1)];
	    }
	    return $rdmpass;
	}

	function run() {
		#Check if the activity was input
		if (isset($_GET['act'])) {
			switch ($_GET['act']) {
				case 'viewinfo' :
					$session = $this -> validation -> active_session();
					if ($session == 1) {
						$header = file_get_contents('Views/Head.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = file_get_contents('Views/studentview.html');
						$content = $this -> templateCtrl -> get_menu($content);
						$student = $this -> obj_mdl -> std_obj -> get_student((int)$_SESSION['userid']);
						$content = $this -> templateCtrl -> procesarPlantilla_studentview($content,$student);
						$content = str_replace("{{'opciones-estudiante'}}", '', $content);
						echo $header.$content.$footer;
					}
					else if ($session == 2 || $session == 3) {
						if (isset($_POST['radiobusc']) && isset($_POST['txtbuscar'])) {
							if ($_POST['txtbuscar'] != "") {
								if ($_POST['radiobusc'] == "codigo") {
									if ($this -> validation -> validate_sid($_POST['txtbuscar'])) {
										$students = $this -> obj_mdl -> std_obj -> search_studentbycodigo($_POST['txtbuscar']);
										if (sizeof($students) == 0) $cadena = '<tr><td>No se encontro ningun resultado</td></tr>';
										else {
											$cadena = '<tr><th></th><th>Codigo</th><th>Alumno</th><th>Carrera</th></tr>';
											while ((list( ,$codigo) = each($students['codigo'])) && (list( ,$nombre) = each($students['nombre'])) && (list( ,$carrera) = each($students['carrera']))) {
												$cadena .=
												'<tr><td>
												<form method="post" action="index.php?ctrl=student&act=viewinfo" class="form-horizontal" role="form">
												<button class="btn btn-primary" type="submit">Ver</button>
												<input type="hidden" name="studentid" value="'.$codigo.'"></input>
												</form>
												</td>
												<td>'.$codigo.'</td><td>'.$nombre.'</td><td>'.$carrera.'</td></tr>';
											}
										}
										echo $cadena;
									}
									else {
										//echo 'codigo no es valido';
										return;
									}
								}
								else if ($_POST['radiobusc'] == "nombre") {
									if ($this -> validation -> validate_name($_POST['txtbuscar'])) {
										$students = $this -> obj_mdl -> std_obj -> search_studentbynombre($_POST['txtbuscar']);
										if (sizeof($students) == 0) $cadena = '<tr><td>No se encontro ningun resultado</td></tr>';
										else {
											$cadena = '<tr><th></th><th>Codigo</th><th>Alumno</th><th>Carrera</th></tr>';
											while ((list( ,$codigo) = each($students['codigo'])) && (list( ,$nombre) = each($students['nombre'])) && (list( ,$carrera) = each($students['carrera']))) {
												$cadena .=
												'<tr><td>
												<form method="post" action="index.php?ctrl=student&act=viewinfo" class="form-horizontal" role="form">
												<button class="btn btn-primary" type="submit">Ver</button>
												<input type="hidden" name="studentid" value="'.$codigo.'"></input>
												</form>
												</td>
												<td>'.$codigo.'</td><td>'.$nombre.'</td><td>'.$carrera.'</td></tr>';
											}
										}
										echo $cadena;
									}
									else {
										//echo 'nombre no es valido';
										return;
									}
								}
								else {
									//echo 'error en la opcion';
									return;
								}
							}
							else {
								//echo 'no selected';
							}
						}
						else if (isset($_POST['studentid'])) {
							$header = file_get_contents('Views/Head.html');
							$footer = file_get_contents('Views/Footer.html');
							$content = file_get_contents('Views/studentview.html');
							$content = $this -> templateCtrl -> get_menu($content);
							$student = $this -> obj_mdl -> std_obj -> get_student($_POST['studentid']);
							$content = $this -> templateCtrl -> procesarPlantilla_studentview($content,$student);
							$content = str_replace("{{'opciones-estudiante'}}", $this -> templateCtrl -> get_studentmenu($session,$student['estado'],$student['codigo']), $content);
							echo $header.$content.$footer;
						}	
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

				case 'modifyinfo':
					$session = $this -> validation -> active_session();
					if ($session == 1) {
						$header = file_get_contents('Views/Head.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = file_get_contents('Views/modifystudent.html');
						$content = $this -> templateCtrl -> get_menu($content);
						$student = $this -> obj_mdl -> std_obj -> get_student((int)$_SESSION['userid']);
						$content = $this -> templateCtrl -> procesarPlantilla_studentview($content,$student);
						echo $header.$content.$footer;
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

				case 'search' :
					$session = $this -> validation -> active_session();
					if ($session >= 2) {
						$header = file_get_contents('Views/Head.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = file_get_contents('Views/searchstudent.html');
						$content = $this -> templateCtrl -> get_menu($content);
						echo $header.$content.$footer;
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

				case 'new':
					$session = $this -> validation -> active_session();
					if ($session >= 3) {
						$footer = file_get_contents('Views/Footer.html');
						$header = file_get_contents('Views/Head.html');
						$content = file_get_contents('Views/addstudent.html');
						$content = $this -> templateCtrl -> get_menu($content);
						$carreras = $this -> obj_mdl -> std_obj -> get_carreras();
						$content = str_replace("{{'select_carrera'}}", $this->templateCtrl -> llena_select_carreras($carreras), $content);
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

				case 'update':
					$session = $this -> validation -> active_session();
					if ($session == 1) {
						if ($_POST['email'] != "") {
							$studentid = $_SESSION['userid'];
							$correo = $_POST['email'];
							$github = $_POST['github'];
							$celular = $_POST['cellphone'];
							$web = $_POST['web'];
							if ($this -> obj_mdl -> update_studentinfo($studentid,$correo,$github,$celular,$web)) {
								$student = $this -> obj_mdl -> std_obj -> get_student($studentid);
								$footer = file_get_contents('Views/Footer.html');
								$header = file_get_contents('Views/Head.html');
								$content = file_get_contents('Views/studentview.html');
								$content = $this -> templateCtrl -> get_menu($content);
								$content = $this -> templateCtrl -> procesarPlantilla_studentview($content,$student);
								$content = str_replace("{{'opciones-estudiante'}}", '',$content);
								echo $header . $content . $footer;
							}
							else {
								echo ' no se pudo actualizar la informacion';
							}
						}
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
					#Create student array
					$student = array();
					#Flags is to mark down errors on optional fields
					$bandera_datos = 0;
					if (isset($_POST['nacimiento']) && $_POST['nacimiento'] != "") {
						#Check if the name is valid
						if (isset($_POST['first']) && isset($_POST['second']) && isset($_POST['name'])) {
							#Validate full name
							if ($this -> validation -> validate_name(($_POST['name'].' '.$_POST['first'].' '.$_POST['second']))) {
								#Check if e-mail exists
								if (isset($_POST['email'])) {
									#Validate e-mail
									if ($this -> validation -> validate_email($_POST['email'])) {
										#Check if Student ID exists
										if (isset($_POST['studentid'])) {
											#Validate Student ID
											if ($this -> validation -> validate_sid($_POST['studentid'])) {
												if (isset($_POST['career'])) {
													if ($this -> validation -> validate_career($_POST['career'])) {
														#Check if Phonenumber exists
														if (isset($_POST['cellphone']) && $_POST['cellphone'] != "") {
															#Validate Phonenumber
															if ($this -> validation -> validate_phonenumber($_POST['cellphone'])) {
																$student['cellphone'] = $_POST['cellphone'];
															}
															else {
																#Phonenumber not valid
																$bandera_datos = 1;
																$this -> errors -> not_valid_format($_POST['cellphone'],"Phone Number");
															}
														}
														#Check if Github account exists
														if (isset($_POST['github']) && $_POST['github'] != "") {
															#Check if Github account is valid
															if ($this -> validation -> validate_github($_POST['github'])) {
																$student['github'] = $_POST['github'];
															}
															else {
																#Github not valid
																$bandera_datos =1;
																$this -> errors -> not_valid_format($_POST['github'],"Github Account");
															}
														}
														#Check if Web Page exists
														if (isset($_POST['web']) && $_POST['web'] != "") {
															#Validate Web Page
															if ($this -> validation -> validate_web($_POST['web'])) {
																$student['web'] = $_POST['web'];
															}
															else {
																#Web Page not valid
																$bandera_datos = 1;
																$this -> errors -> not_valid_format($_POST['web'],"Web Page");
															}
														}
														#All data has been validated
														if ($bandera_datos == 0) {
															#Fill Student array
															$student['password'] = $this -> genera_password();
															$student['nacimiento'] = $_POST['nacimiento'];
															$student['name'] = $_POST['name'];
															$student['first'] = $_POST['first'];
															$student['second'] = $_POST['second'];
															$student['studentid'] = $_POST['studentid'];
															$student['email'] = $_POST['email'];
															$student['career'] = $_POST['career'];
															$student = $this -> obj_mdl -> add_student($student);
															if (is_array($student)) {
																#Set action
																$student['action'] = "Added";
																#Get the View
																$footer = file_get_contents('Views/Footer.html');
																$header = file_get_contents('Views/Head.html');
																$content = file_get_contents('Views/studentview.html');
																$content = $this -> templateCtrl -> get_menu($content);
																$content = $this -> templateCtrl -> procesarPlantilla_studentview($content,$student);
																$content = str_replace("{{'opciones-estudiante'}}", '',$content);
																echo $header .$content.$footer;
															}
															else {
																#Display the add error
																$this -> errors -> error_add_student($_POST['first'].' '.$_POST['second'].' '. $_POST['name']);
															}
														}
													}
													else {
														#Career not valid
														$this -> errors -> not_valid_format($_POST['career'],"Career");
													}
												}
												else {
													#Career not input
													$this -> errors -> not_found_input("Career");
												}
											}
											else {
												#Student id not valid
												$this -> errors -> not_valid_format($_POST['studentid'],"Student ID");
											}
										}
										else {
											#Student ID not input
											$this -> errors -> not_found_input('Student ID');
										}
									}
									else {
										#E-mail is not valid
										$this -> errors -> not_valid_format($_POST['email'],"E-mail");
									}
								}
								else {
									#Correo was not input
									$this -> errors -> not_found_input('E-mail');
								}
							}
							else {
								#Full name is not valid
								$this -> errors -> not_valid_format(($_POST['name'].' '.$_POST['first'].' '.$_POST['second']),'Nombre Completo');
							}
						}
						else {
							#Name was not input
							$this -> errors -> not_found_input('Full Name');
						}
					}
					else {
						$this -> errors -> not_found_input('Date');
					}
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
				
				case 'modifystatus' :
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 3) {
						#User is allowed to execute action
						#Check if student ID exists
						if (isset($_POST['studentid'])) {
							#Validate Student ID
							if ($this -> validation -> validate_sid($_POST['studentid'])) {
								#Check if value exists
								if (isset($_POST['value'])) {
									#Validate value
									$value = $this -> validation -> validate_studentstatus($_POST['value']);
									if ($value == 2) {
										$this -> errors -> not_valid_format($_POST['value'],'Status Value');
										die();
									}
									$student = $this -> obj_mdl -> modify_status(strtoupper($_POST['studentid']),$value);
									if (is_array($student)) {
										#Set action
										$student['action'] = "Status Modified";
										#Get the view
										$body = 'Tu Status ha sido Modificado , por favor revisa los cambios';
										//$this -> mailCtrl -> send_mail($body,$student['correo'],$student['nombre']);
										//require('Views/studentview.php');
										$footer = file_get_contents('Views/Footer.html');
										$header = file_get_contents('Views/Head.html');
										$content = file_get_contents('Views/studentview.html');
										$content = $this -> templateCtrl -> get_menu($content);
										$content = $this -> templateCtrl -> procesarPlantilla_studentview($content,$student);
										$content = str_replace("{{'opciones-estudiante'}}", $this -> templateCtrl -> get_studentmenu($session,$student['estado'],$student['codigo']), $content);
										echo $header .$content.$footer;
										return;
									}
									else {
										#Could not modify Student Status
										$this -> errors -> not_modify_student_status($_POST['studentid']);
									}
								}
								else {
									#Status Value was not input
									$this -> errors -> not_found_input('Status Value');
								}
							}
							else {
								#Student ID is not valid
								$this -> errors -> not_valid_format($_POST['studentid'],'Student ID');
							}
						}
						else {
							#Student ID was not input
							$this -> errors -> not_found_input('Student ID');
						}
					}
					else if ($session == false) {
						$header = file_get_contents('Views/Head.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = file_get_contents('Views/login.html');
						$content = $this -> templateCtrl -> procesarPlantilla_login($content,-1);
						echo $header.$content.$footer;
					}
					else {
						#User is not allowed to execute action
						$this -> errors -> not_valid_usertype();
					}
					break;

				case 'list':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session == 1) {
						if (sizeof($_POST) == 0 ) {
							$header = file_get_contents('Views/Head.html');
							$footer = file_get_contents('Views/Footer.html');
							$content = file_get_contents('Views/viewstudent_courses.html');
							$content = $this -> templateCtrl -> get_menu($content);
							$ciclos = $this -> obj_mdl -> std_obj -> get_cicles_student($_SESSION['userid']);
							$actual = $this -> obj_mdl -> std_obj -> get_cicle();
							$content = $this -> templateCtrl -> procesarPlantilla_viewstudent_courses($content,$ciclos,$actual);
							echo $header.$content.$footer;
							return;
						}
						if (isset($_POST['ciclo'])) {
							$cadena = '';
							$cursos = $this -> obj_mdl -> std_obj -> get_studentCourses($_POST['ciclo'],$_SESSION['userid']);
							if(isset($cursos['nombre'])) {
								while ((list( , $nombre) = each($cursos['nombre'])) && (list( , $nrc) = each($cursos['nrc'])) && (list( , $calificacion) = each($cursos['calificacion']))
									  && (list( , $seccion) = each($cursos['seccion'])) && (list( , $clave) = each($cursos['clave']))) {
									$cadena .= '<tr><td>'.$nombre.'</td><td>'.$clave.'</td><td>'.$nrc.'</td><td>'.$seccion.'</td><td>'.$calificacion.'</td>
									<td><form method="post" action="index.php?ctrl=student&act=list" class="form-horizontal" role="form">
									<button class="btn btn-primary btn-block" type="submit">Calificacion</button>
									<input type="hidden" name="studentid" value="'.$_SESSION['userid'].'"></input>
									<input type="hidden" name="cicle" value="'.$_POST['ciclo'].'"></input>
									<input type="hidden" name="nrc" value="'.$nrc.'"></input>
									<input type="hidden" name="details" value="1"></input>
									</form></td>
									<td><form method="post" action="index.php?ctrl=student&act=list" class="form-horizontal">
									<button class="btn btn-primary btn-block" type="submit">Asistencias</button>
									<input type="hidden" name="studentid" value="'.$_SESSION['userid'].'"></input>
									<input type="hidden" name="cicle" value="'.$_POST['ciclo'].'"></input>
									<input type="hidden" name="nrc" value="'.$nrc.'"></input>
									</form></td></tr>';
								}
							}
							else $cadena = '<tr><td>El Alumno no tiene Cursos registrados en el Ciclo seleccionado</td></tr>';
							echo $cadena;
							return;
						}
						if (isset($_POST['studentid'])) {
							if ($this -> validation -> validate_userid($_POST['studentid']) == 1) {
								$student_id = $_POST['studentid'];	
							}
							else {
							$this -> errors -> not_valid_userid('Student');
								die();
							}
						}
						else {
							$this -> errors -> not_found_input('Student ID');
							die();
						}
						#User is allowed to execute action
					#Check if cicle exists
					if (isset($_POST['cicle'])) {
						#Validate if cicle is valid
						if ($this -> validation -> validate_cicle($_POST['cicle'])) {
									#Check if NRC exists
									if (isset($_POST['nrc'])) {
										if ($this -> validation -> validate_nrc($_POST['nrc'])) {
											if (isset($_POST['details'])) {
												$grade_array = $this -> obj_mdl -> view_student_courseDetails($_POST['nrc'].$_POST['cicle'],$_SESSION['userid']);
												$subject_array = $this -> obj_mdl -> std_obj -> obtener_curso($_POST['nrc']);
												$header = file_get_contents('Views/Head.html');
												$content = file_get_contents('Views/grade_listview.html');
												$footer = file_get_contents('Views/Footer.html');
												$content = $this -> templateCtrl -> procesarPlantilla_grade_listview($content,$grade_array,$subject_array);
												$content = $this -> templateCtrl -> get_menu($content);
												echo $header . $content . $footer;
												return;
											}
											$course_info = $this -> obj_mdl -> view_student_course($_POST['studentid'],$_POST['cicle'],$_POST['nrc']);
											if (is_array($course_info)) {
												#Get the View
												//require('Views/student_courseview.php');
												$subject_array = $this -> obj_mdl -> std_obj -> obtener_curso($_POST['nrc']);
												$header = file_get_contents('Views/Head.html');
												$content = file_get_contents('Views/attendance_listview.html');
												$footer = file_get_contents('Views/Footer.html');
												$content = $this -> templateCtrl -> procesarPlantilla_attendance_listview($content,$course_info,$subject_array);
												$content = $this -> templateCtrl -> get_menu($content);
												echo $header . $content . $footer;
											}
											else {
												#Could not find student
												$this -> errors -> student_not_found($_POST['studentid']);
											}
										}
										else {
											#Course NRC is not valid
											$this -> errors -> not_valid_input($_POST['nrc'],'NRC');
										}
									}
									else {
										#Get the model
										$grades_info = $this -> obj_mdl -> view_student_grades($_POST['studentid'],$_POST['cicle']);
										if (is_array($grades_info)) {
											#Get the View
											require('Views/students_gradesview.php');
										}
										else {
											#Could not find student
											$this -> errors -> student_not_found($_POST['studentid']);
										}
									}
								}
								else {
									#Cicle is not valid
									$this -> errors -> not_valid_format($_POST['cicle'],'cicle');
								}
							}
							else {
								#Cicle was not input
								$this -> errors -> not_found_input('cicle');
							}
					}
					else if ($session == false) {
						#Session is not started
						$header = file_get_contents('Views/Head.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = file_get_contents('Views/login.html');
						$content = $this -> templateCtrl -> procesarPlantilla_login($content,-1);
						echo $header.$content.$footer;
					}
					else {
						#User is not allowed to execute action
						$this -> errors -> not_valid_usertype();
					}
					break;

				default:
					#Activity is not valid
					$this -> errors -> not_valid_input($_POST['act'],'Activity');
					break;
			}
		}
		else {
			$this -> errors -> not_found_input('Activity');
		}
	}
}



?>