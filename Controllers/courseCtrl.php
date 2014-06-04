<?php

class courseCtrl {

	function __construct($driver) {
		#Create errors object
		require('Views/errors.php');
		$this -> errors = new errors();
		#Create the validation object
		require('Controllers/validationCtrl.php');
		$this -> validation = new validationCtrl();
		#Create Model object
		require('Models/courseMdl.php');
		$this -> mdl_obj = new courseMdl($driver);
		require('Controllers/templatesCtrl.php');
		$this -> templateCtrl = new templatesCtrl();
		require('Controllers/mailCtrl.php');
		$this -> emailCtrl = new mailCtrl();
	}


	function run() {
			#Check if the activity was input
			if (isset($_GET['act'])) {
				switch ($_GET['act']) {
					case 'viewcourse':
						$session = $this -> validation -> active_session();
						if ($session >= 2) {
							if (isset($_POST['nrc']) && isset($_POST['ciclo'])) {
								if (($_POST['nrc'] != "") && ($_POST['ciclo'] != "")) {
									$clave_curso = $_POST['nrc'].$_POST['ciclo'];
									if (is_array($info_curso = $this -> mdl_obj -> std_obj -> crear_curso_datos($clave_curso))) {
										$ciclo_actual = $this -> mdl_obj -> std_obj -> get_cicle();
										$band_enrolled = false;
										if ($ciclo_actual == $_POST['ciclo']) {
											$band_ciclo = true;
											if ($this -> mdl_obj -> std_obj -> check_if_course_empty($clave_curso)) {
												$band_enrolled = true;
											}
										}
										else $band_ciclo = false;
										$header = file_get_contents('Views/Head.html');
										$footer = file_get_contents('Views/Footer.html');
										$content = file_get_contents('Views/courseview.html');
										$rubros = $this -> mdl_obj -> std_obj -> get_rubros($clave_curso);
										$content = $this -> templateCtrl -> get_menu($content);
										$content = $this -> templateCtrl -> procesarPlantilla_courseview($content,$info_curso,$rubros,$band_ciclo,$band_enrolled);
										echo $header.$content.$footer;
									}
									else {
										echo 'not found';
									}
								}
								else 
									echo 'nrc or ciclo are incorrect';
							}
							else {
								echo 'nrc or ciclo no were not found';
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
					case 'search':
						$session = $this -> validation -> active_session();
						if ($session >= 2) {
							if (isset($_POST['ciclo']) && isset($_POST['teacher_id'])) {
								$nrc = $this -> mdl_obj -> std_obj -> get_nrcs($_POST['ciclo'],$_POST['teacher_id']);

								echo $this -> templateCtrl -> llena_select_nrc($nrc);
							}
							else {
								if ($session == 3) {
									$teachers = $this -> mdl_obj -> std_obj -> get_all_teachers();
								}
								else {
									$teachers['clave'][] = $_SESSION['userid'];
									$teachers['nombre'][] = $_SESSION['user'];
								}
								$header = file_get_contents('Views/Head.html');
								$footer = file_get_contents('Views/Footer.html');
								$content = file_get_contents('Views/searchcourse.html');
								$content = $this -> templateCtrl -> get_menu($content);
								$cicles = $this -> mdl_obj -> std_obj -> get_all_cicles();
								$content = str_replace("{{'select_maestros'}}", $this -> templateCtrl -> llena_select_maestros($teachers), $content);
								$content = $this -> templateCtrl -> procesarPlantilla_searchcourse($content,$cicles);
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
					case 'new':
						$session = $this -> validation -> active_session();
						if ($session >= 2) {
							if ($session == 3) {
								$teachers = $this -> mdl_obj -> std_obj -> get_all_teachers();
							}
							else {
								$teachers['clave'][] = $_SESSION['userid'];
								$teachers['nombre'][] = $_SESSION['user'];
							}
							$header = file_get_contents('Views/Head.html');
							$footer = file_get_contents('Views/Footer.html');
							$content = file_get_contents('Views/addcourse.html');
							$content = $this -> templateCtrl -> get_menu($content);
							$materias = $this -> mdl_obj -> std_obj -> get_all_materias();
							$dias = $this -> mdl_obj -> std_obj -> get_all_dias();
							$horas = $this -> mdl_obj -> std_obj -> get_all_horas();
							$content = str_replace("{{'select_maestros'}}", $this -> templateCtrl -> llena_select_maestros($teachers), $content);
							$content = str_replace("{{'select_materia'}}", $this -> templateCtrl -> llena_select_materias($materias),$content);
							$content = str_replace("{{'select_dia'}}", $this -> templateCtrl -> llena_select_dias($dias),$content);
							$content = str_replace("{{'select_hora'}}", $this -> templateCtrl -> llena_select_horas($horas),$content);
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
					case 'create':
					$session = $this -> validation -> active_session();
					if ($session >= 2) {
						if ($session == 3) {
							if (isset($_POST['teacher_id'])) {
								if ($this -> validation -> validate_userid($_POST['teacher_id']) == 2) {
									$teacher_id = $_POST['teacher_id'];	
								}
								else {
									$this -> errors -> not_valid_userid('Teacher');
									die();
								}
							}
							else {
								$this -> errors -> not_found_input('Teacher ID');
								die();
							}
						}
						else
							$teacher_id = $_SESSION['userid'];
						if (isset($_POST['subject'])) {
							#Validate if the subject name is valid
							if ($this -> validation -> validate_subject($_POST['subject'])) {
								#Check if the NRC exists
								if (isset($_POST['nrc'])) {
									#Validate if the nrc is valid
									if ($this -> validation -> validate_nrc($_POST['nrc'])) {
										#Check if section exists
										if (isset($_POST['section'])) {
											#Validate if the section is correct
											if ($this -> validation -> validate_section($_POST['section'])) {
												#Check if the course schedule is correct
												#Check if course days and hours exists
												if (isset($_POST['days']) && isset($_POST['hours']) && isset($_POST['schedule'])) {
													if ((is_array($_POST['days']) && is_array($_POST['hours']) && is_array($_POST['schedule'])) && ($this -> validation -> validate_schedule($_POST['days'],$_POST['hours'],$_POST['schedule']))) {
														#Create the course array and set the values
														$course_array = array( "subject" => strtoupper($_POST['subject']),
																		 	   "teacher_id" => strtoupper($teacher_id), 
																		 	   "section" => strtoupper($_POST['section']),
																			   "nrc" => $_POST['nrc']);
														#Separate days elements and add them to course array
														foreach ($_POST['days'] as $key => $value) {
															$aux_array[] = $value;
														}
														$course_array['days'] = $aux_array;
														$aux_array = array();
														#Separate hours elements and add them to course array
														foreach ($_POST['hours'] as $key => $value) {
															$aux_array[] = $value;
														}
														$course_array['hours'] = $aux_array;
														$aux_array = array();
														#Separate schedule elements and add them to course array
														foreach ($_POST['schedule'] as $key => $value) {
															$aux_array[] = $value;
														}
														$course_array['schedule'] = $aux_array;
														#Callback to the add function, sending the array created as a parameter
														if (is_array($course_array = $this -> mdl_obj -> add_course($course_array))) {
															#Get the view
															//require('Views/courseview.php');
															$header = file_get_contents('Views/Head.html');
															$footer = file_get_contents('Views/Footer.html');
															$content = file_get_contents('Views/courseview.html');
															$rubros = $this -> mdl_obj -> std_obj -> get_rubros($course_array['ciclo']);
															$content = $this -> templateCtrl -> get_menu($content);
															$content = $this -> templateCtrl -> procesarPlantilla_courseview($content,$course_array,$rubros,true,true);

															echo $header.$content.$footer;
														}
														else {
															#Error adding course
															$this -> errors -> error_add_course($_POST['subject']);
														}
													}
													else {
														#Class schedule is incorrect
														$this -> errors -> not_valid_schedule();
													}
												}
												else {
													#Class schedule is incorrect
													$this -> errors -> not_found_input('Schedule');
												}
											}
											else {
												#Section is not valid
												$this -> errors -> not_valid_format($_POST['section'],'Section');
											}
										}
										else {
											#Section was not input
											$this -> errors -> not_found_input('Section');
										}
									}
									else {
										#NRC is not valid
										$this -> errors -> not_valid_format($_POST['nrc'],'NRC');
									}
								}
								else {
									#NRC was not input
									$this -> errors -> not_found_input('NRC');
								}
							}
							else {
								#Subject name is not valid
								$this -> errors -> not_valid_format($_POST['subject'],'Subject Name');
							}
						}
						else {
							#Subject name was not input
							$this -> errors -> not_found_input('Subject Name');
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
				case 'clone':
					$session = $this -> validation -> active_session();
					if (sizeof($_POST) == 0) {
						if ($session >= 2) {
							if ($session == 3) {
								$teachers = $this -> mdl_obj -> std_obj -> get_all_teachers();
							}
							else {
								$teachers['clave'][] = $_SESSION['userid'];
								$teachers['nombre'][] = $_SESSION['user'];
							}
							$header = file_get_contents('Views/Head.html');
							$footer = file_get_contents('Views/Footer.html');
							$content = file_get_contents('Views/clonecourse.html');

							$cicles = $this -> mdl_obj -> std_obj -> get_all_cicles();
							$materias = $this -> mdl_obj -> std_obj -> get_all_materias();

							$content = $this -> templateCtrl -> get_menu($content);
							$content = str_replace("{{'select_maestros'}}", $this -> templateCtrl -> llena_select_maestros($teachers), $content);
							$content = str_replace("{{'select_materia'}}", $this -> templateCtrl -> llena_select_materias($materias),$content);
							$content = $this -> templateCtrl -> procesarPlantilla_clonecourse($content,$cicles);

							echo $header.$content.$footer;
						}
						else if ($session == false) {
							$header = file_get_contents('Views/Head.html');
							$footer = file_get_contents('Views/Footer.html');
							$content = file_get_contents('Views/login.html');
							$content = $this -> templateCtrl -> procesarPlantilla_login($content,-1);
							echo $header.$content.$footer;
						}
					}
					else {
						if ($session >= 2) {
							if ($session == 3) {
								if (isset($_POST['teacher_id'])) {
									if ($this -> validation -> validate_userid($_POST['teacher_id']) == 2) {
										$teacher_id = $_POST['teacher_id'];	
									}
									else {
										$this -> errors -> not_valid_userid('Teacher');
										die();
									}
								}
								else {
									$this -> errors -> not_found_input('Teacher ID');
									die();
								}
							}
							else
								$teacher_id = $_SESSION['userid'];
							#Check if cicle_c exists
							if (isset($_POST['cicle_c'])) {
								#Validate if cicle_c is correct
								if ($this -> validation -> validate_cicle($_POST['cicle_c'])) {
									#Check if the NRC exists
										if (isset($_POST['nrc'])) {
											#Validate if the nrc is valid
											if ($this -> validation -> validate_nrc($_POST['nrc'])) {
												#Check if section exists
												if (isset($_POST['section'])) {
													#Validate if the section is correct
													if ($this -> validation -> validate_section($_POST['section'])) {
														#Check if nrc_c exists
														if (isset($_POST['nrc_c'])) {
															#Validate if nrc_c is correct
															if ($this -> validation -> validate_nrc($_POST['nrc_c'])) {
																#Check if subject exists
																if (isset($_POST['subject'])) {
																	#Validate subject
																	if ($this -> validation -> validate_subject($_POST['subject'])) {
																		#Create the course array and set the values
																		$course_array = array( "ciclo_anterior" =>strtoupper($_POST['cicle_c']),
																					 	       "seccion" => strtoupper($_POST['section']),
																					 	       "clave_materia" => strtoupper($_POST['subject']),
																							   "nrc_anterior" => $_POST['nrc_c'],
																							   "clave_maestro" => strtoupper($teacher_id),
																							   "nrc" => $_POST['nrc']);

																		#Callback to the add function, sending the array created as a parameter
																		$course_array = $this -> mdl_obj -> clone_course($course_array);
																		if (is_array($course_array)) {
																			#Get the view
																			//require('Views/courseview.php');
																			$header = file_get_contents('Views/Head.html');
																			$footer = file_get_contents('Views/Footer.html');
																			$content = file_get_contents('Views/courseview.html');
																			$rubros = $this -> mdl_obj -> std_obj -> get_rubros($course_array['ciclo']);
																			$content = $this -> templateCtrl -> get_menu($content);
																			$content = $this -> templateCtrl -> procesarPlantilla_courseview($content,$course_array,$rubros,true,true);

																			echo $header.$content.$footer;
																		}
																		else {
																			#Error adding course
																			$this -> errors -> error_add_course($_POST['subject']);
																		}
																	}
																	else {
																		#Subject is not valid
																		$this -> errors -> not_valid_format($_POST['subject'],'Subject ID');
																	}
																}
																else {
																	#Subject was not input
																	$this -> errors -> not_found_input('Subject ID');
																}
															}
															else {
																#NRC_c is not valid
																$this -> errors -> not_valid_format($_POST['nrc_c'],'NRC_c');
															}
														}
														else {
															#NRC_c was not input
															$this -> errors -> not_found_input('NRC_c');
														}
														
													}
													else {
														#Section is not valid
														$this -> errors -> not_valid_format($_POST['section'],'Section');
													}
												}
												else {
													#Section was not input
													$this -> errors -> not_found_input('Section');
												}
											}
											else {
												#NRC is not valid
												$this -> errors -> not_valid_format($_POST['nrc'],'NRC');
											}
										}
										else {
											#NRC was not input
											$this -> errors -> not_found_input('NRC');
										}
									}
									else {
										#Cicle_cc format is incorrect
										$this -> errors -> not_valid_format($_POST['cicle_c'],'Cicle');
									}
								}
								else {
									#Cicle_c was not input
									$this -> errors -> not_found_input('Cicle');
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
						}
					break;


				case 'list':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 2) {
						#User is allowed to execute action
						#Check if course exists
						if (isset($_POST['nrc']) && isset($_POST['ciclo'])) {
							if ($this -> validation -> validate_courseid($_POST['nrc'])) {
								#Callback to the view function
								$students_array = $this -> mdl_obj -> view_course_students($_POST['nrc'],$_POST['ciclo']);
								if (is_array($students_array)) {
									#Get the View
									$ciclo = $this -> mdl_obj -> std_obj -> get_cicle();
									$subject_array = $this -> mdl_obj -> std_obj -> obtener_curso($_POST['nrc'],$_POST['ciclo']);
									$header = file_get_contents('Views/Head.html');
									$content = file_get_contents('Views/course_listview.html');
									$footer = file_get_contents('Views/Footer.html');
									$content = $this -> templateCtrl -> procesarPlantilla_course_listview($content,$students_array,$subject_array);
									$content = $this -> templateCtrl -> get_menu($content);
									echo $header . $content . $footer;
								}
								else {
									#The course was not found
									$this -> errors -> not_valid_input($_POST['nrc']);
								}
							}
							else {
								#Course ID is not valid
								$this -> errors -> not_valid_format($_POST['nrc'],'NRC');
							}
						}
						else {
							#Course ID was not input
							$this -> errors -> not_found_input('NRC');
						}
					}
					else if ($session == false) {
						$header = file_get_contents('Views/Head.html');
						$content = file_get_contents('Views/login.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = $this-> templateCtrl -> procesarPlantilla_login($content,0);
						echo $header.$content.$footer;
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;

				case 'list_a':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 2) {
						#User is allowed to execute action
						#Check if course exists
						if (isset($_POST['nrc']) && isset($_POST['ciclo'])) {
							if ($this -> validation -> validate_courseid($_POST['nrc'])) {
								#Callback to the view function
								$attendance_array = $this -> mdl_obj -> view_course_attendance($_POST['nrc'],$_POST['ciclo']);
								if (is_array($attendance_array)) {
									#Get the View
									$ciclo = $this -> mdl_obj -> std_obj -> get_cicle();
									$band_ciclo = false;
									if ($ciclo == $_POST['ciclo']) $band_ciclo = true;
									$subject_array = $this -> mdl_obj -> std_obj -> obtener_curso($_POST['nrc'],$_POST['ciclo']);
									$hoy = $this -> mdl_obj -> std_obj -> obtener_dia();
									$header = file_get_contents('Views/Head.html');
									$content = file_get_contents('Views/attendance_listview.html');
									$footer = file_get_contents('Views/Footer.html');
									$content = $this -> templateCtrl -> procesarPlantilla_attendance_listview($content,$attendance_array,$subject_array,$hoy,$_POST['nrc'].$_POST['ciclo'],$band_ciclo,$_POST['ciclo']);
									$content = $this -> templateCtrl -> get_menu($content);
									echo $header . $content . $footer;
								}
								else {
									#The course was not found
									$this -> errors -> error_query_list($_POST['nrc']);
								}
							}
							else {
								#Course ID is not valid
								$this -> errors -> not_valid_format($_POST['nrc'],'NRC');
							}
						}
						else {
							#Course ID was not input
							$this -> errors -> not_found_input('NRC');
						}
					}
					else if ($session == false) {
						$header = file_get_contents('Views/Head.html');
						$content = file_get_contents('Views/login.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = $this-> templateCtrl -> procesarPlantilla_login($content,0);
						echo $header.$content.$footer;
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;

				case 'list_c':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 2) {
						#User is allowed to execute action
					#Check if NRC exists
					if (isset($_POST['nrc']) && isset($_POST['ciclo'])) {
						#validate NRC
						if ($this -> validation -> validate_nrc($_POST['nrc'])) {
							#Callback to the view function
							$grade_array = $this -> mdl_obj -> view_course_grade($_POST['nrc'],$_POST['ciclo']);
							if (is_array($grade_array)) {
								#Get the View
								//require('Views/grade_listview.php');
								$ciclo = $this -> mdl_obj -> std_obj -> get_cicle();
								$band_ciclo = false;
								if ($ciclo == $_POST['ciclo']) $band_ciclo = true;
								$subject_array = $this -> mdl_obj -> std_obj -> obtener_curso($_POST['nrc'],$ciclo);
								$header = file_get_contents('Views/Head.html');
								$content = file_get_contents('Views/grade_listview.html');
								$footer = file_get_contents('Views/Footer.html');
								$content = $this -> templateCtrl -> procesarPlantilla_grade_listview($content,$grade_array,$subject_array,$band_ciclo,$_POST['ciclo']);
								$content = $this -> templateCtrl -> get_menu($content);
								echo $header . $content . $footer;
							}
							else {
								#The course was not found
								$this -> errors -> error_query_list($grade_array);
							}
						}
						else {
							#NRC is not valid
							$this -> errors -> not_valid_format($_POST['nrc'],'NRC');
						}
					}
					else {
						#NRC was not input
						$this -> errors -> not_found_input('NRC');
					}
					}
					else if ($session == false) {
						//$this -> errors -> not_logged_in();
						$header = file_get_contents('Views/Head.html');
						$content = file_get_contents('Views/login.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = $this-> templateCtrl -> procesarPlantilla_login($content,0);
						echo $header.$content.$footer;
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;

				case 'addstudent':
					$session = $this -> validation -> active_session();
					if ($session >= 2) {
						if (isset($_POST['agrega_curso']) && !isset($_POST['nrc']) && isset($_POST['studentid'])) {
							$ciclo = $this -> mdl_obj -> std_obj -> get_cicle();
							$nrc = $this -> mdl_obj -> std_obj -> get_nrcs_cicle($ciclo,$_POST['studentid']);
							$cadena = '
								<tr>
					              <td><label for="txtnrc">Escribe NRC</label></td>
					            </tr><tr>
					            <td>
					              <input onkeyup="nrc_input(this)" onblur="nrc_input(this)" type="text" class="form-control" id="txtnrc"  maxlength="5">
					              </input>
					            </td>
								<td>
								<select onchange="nrc_select(this); mostrar_nrc_info(this);" id="selnrc" name="nrc" type="text" class="form-control">'
								.$this -> templateCtrl -> llena_select_nrc($nrc).
								'</select>
								</td>
								</tr>
							';
							echo $cadena;
							return;
						}
						if (isset($_POST['agrega_curso']) && isset($_POST['nrc'])) {
							$ciclo = $this -> mdl_obj -> std_obj -> get_cicle();
							$curso = $this -> mdl_obj -> std_obj -> obtener_curso($_POST['nrc'],$ciclo);
							echo $this -> templateCtrl -> procesarPlantilla_courseview(file_get_contents('Views/add_tocoursebody.html'),$curso,false,false,false);
							return;
							//return $this -> templateCtrl -> mostrar_curso($curso);
						}
						#Check if NRC exists
							if (isset($_POST['nrc'])) {
								#Validate NRC
								if ($this -> validation -> validate_nrc($_POST['nrc'])) {
									#Check if Student ID exists
									if (isset($_POST['studentid'])) {
										#Validate Student ID
										if ($this -> validation -> validate_sid($_POST['studentid'])) {
											#Send code to model
											$student =$this -> mdl_obj -> add_student_to_course($_POST['studentid'],$_POST['nrc']);
											if (is_array($student)) {
												#Get the view
												//require('Views/student_added_course.php');
												echo 'Alumno agregado al curso con exito';
											}
											else if ($student == 1) {
												#Failed to add student to course
												$this -> errors -> error_add_student_course($_POST['studentid']);
											}
											else if ($student == 2) {
												#Student is already in course
												$this -> errors -> student_in_course($_POST['studentid']);
											}
											else {
												#Could not find student
												$this -> errors -> student_not_found($_POST['studentid']);
											}
										}
										else {
											#Student ID not valid
											$this -> errors -> not_valid_format($_POST['studentid'],'Student ID');
										}
									}
									else {
										#Student ID not input
										$this -> errors -> not_found_input("Student ID");
									}
								}
								else {
									#NRC is not valid
									$this -> errors -> not_valid_format($_POST['nrc'],'NRC');
								}
							}
							else {
								#NRC was not input
								$this -> errors -> not_found_input('NRC');
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
				case 'viewfields' :
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 2) {
						if (isset($_POST['clave_curso'])) {
							$rubros = $this -> mdl_obj -> std_obj -> get_rubros($_POST['clave_curso']);
							$header = file_get_contents('Views/Head.html');
							$footer = file_get_contents('Views/Footer.html');
							$content = file_get_contents('Views/viewfields.html');
							$content = $this -> templateCtrl -> get_menu($content);
							$content = $this -> templateCtrl -> procesarPlantilla_viewfields($content,$rubros);
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

				case 'addfield':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 2) {
						if (isset($_POST['nrc_curso'])) {
							$header = file_get_contents('Views/Head.html');
							$footer = file_get_contents('Views/Footer.html');
							$content = file_get_contents('Views/addfield.html');
							$content = str_replace("{{'nrc'}}", $_POST['nrc_curso'], $content);
							$content = str_replace("{{'cicle'}}", $this -> mdl_obj -> std_obj -> get_cicle(),$content);
							$content = $this -> templateCtrl -> get_menu($content);
							echo $header.$content.$footer;
							return;
						}
						else if (sizeof($_POST) > 0) {
							#User is allowed to execute action
							#Check if NRC exists
							if (isset($_POST['nrc'])) {
								#Check Field exists
								if (isset($_POST['field'])) {
									#Check if percentage exists
									if (isset($_POST['percentage'])) {
										#Check if nocol exists
										if (isset($_POST['nocol'])) {
											#Create array to send to the add field function
											$band = 0;
											while ((list( ,$percentage) = each($_POST['percentage'])) && (list( ,$nocol) = each($_POST['nocol'])) 
												    && (list( ,$field) = each($_POST['field']))) {	
												$field_array = array ("nrc" => $_POST['nrc'],
																	"field" => $field,
																	"percentage" => $percentage,
																	"nocol" => $nocol);
												$field_array = $this -> mdl_obj -> add_field_to_course($field_array);
												if (!is_array($field_array)) {
													$band=1;
												}
											}
											$clave_curso = $_POST['nrc'].$this -> mdl_obj -> std_obj -> get_cicle();
											if (is_array($info_curso = $this -> mdl_obj -> std_obj -> crear_curso_datos($clave_curso))) {
												$band_enrolled = false;
												$band_ciclo = true;
												if ($this -> mdl_obj -> std_obj -> check_if_course_empty($clave_curso)) {
													$band_enrolled = true;
												}
												$header = file_get_contents('Views/Head.html');
												$footer = file_get_contents('Views/Footer.html');
												$content = file_get_contents('Views/courseview.html');
												$rubros = $this -> mdl_obj -> std_obj -> get_rubros($clave_curso);
												$content = $this -> templateCtrl -> get_menu($content);
												if ($band == 0) $content = str_replace("'&nbsp;'",'<div class="container"><h2>Rubros Agregados!</h2></div>', $content);
												else {
													$content = str_replace("'&nbsp;'",'<div class="container"><h2>Error al Agregar los Rubros</h2></div>', $content);
													$this -> mdl_obj -> delete_rubros($clave_curso);
												}
												$content = $this -> templateCtrl -> procesarPlantilla_courseview($content,$info_curso,$rubros,$band_ciclo,$band_enrolled);
												echo $header.$content.$footer;
											}
										}
										else {
											#Nocol was not input
											$this -> errors -> not_found_input('Number of Columns');
										}
									}
									else {
										#Percentage was not input
										$this -> errors -> not_found_input('percentage');
									}
								}
								else {
									#Field was not input
									$this -> errors -> not_found_input('Field');
								}
							}
							else {
								#NRC was not input
								$this -> errors -> not_found_input('NRC');
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

 				case 'addsheet':
					#Disabling module
					$this -> errors -> module_disabled();
					die();
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session == 2) {
						#User is allowed to execute action
	 					#Check if Course ID exists
	 					if (isset($_GET['courseid'])) {
	 						#Validate Course ID
	 						if ($this -> validation -> validate_courseid($_GET['courseid'])) {
	 							#Check if Field exists
	 							if (isset($_GET['field'])) {
	 								#Validate Field
	 								if ($this -> validation -> validate_field($_GET['field'])) {
	 									#Create array and send it to Model
	 									$sheet_array = array("field" => $_GET['field'], "courseid" => $_GET['courseid']);
	 									$sheet_array = $this -> mdl_obj -> add_sheet_to_course($sheet_array);
	 									if (is_array($sheet_array)) {
	 										#Get the view
	 										require('Views/add_sheet_course.php');
	 									}
	 									else {
	 										#Failed to add sheet
	 										$this -> errors -> error_add_sheet();
	 									}
	 								}
	 								else {
	 									#Field is not valid
	 									$this -> errors -> not_valid_format($_GET['field'],'Field');
	 								}
	 							}
	 							else {
	 								#Field was not input
				 					$this -> errors -> not_found_input('Field');
	 							}
	 						}
	 						else {
	 							#Course ID is not valid
	 							$this -> errors -> not_valid_format($_GET['courseid'],'Course ID');
	 						}
	 					}
	 					else {
	 						#Course ID was not input
		 					$this -> errors -> not_found_input('Course ID');
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

 				case 'attendance':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 2) {
						#User is allowed to execute action
	 					#Check if NRC exists
	 					if (isset($_POST['courseid'])) {
	 						#Check if Value exists
	 						if (isset($_POST['cambiar_asistencia'])) {
			 					foreach ($_POST['cambiar_asistencia'] as $key => $datos) {
			 						list($studentid,$dia, $value) = explode("_", $datos);
			 						list($month,$day,$year) = explode("/",$dia);
									$dia = "$year-$month-$day";
			 						$this -> mdl_obj -> check_studentsid($_POST['courseid'],$studentid,$dia,$value);
			 					}
	 						}
							$attendance_array = $this -> mdl_obj -> view_course_attendance($_POST['nrc'],$_POST['ciclo']);
							if (is_array($attendance_array)) {
								#Get the View
								$ciclo = $this -> mdl_obj -> std_obj -> get_cicle();
								$band_ciclo = false;
								if ($ciclo == $_POST['ciclo']) $band_ciclo = true;
								$subject_array = $this -> mdl_obj -> std_obj -> obtener_curso($_POST['nrc'],$_POST['ciclo']);
								$hoy = $this -> mdl_obj -> std_obj -> obtener_dia();
								$header = file_get_contents('Views/Head.html');
								$content = file_get_contents('Views/attendance_listview.html');
								$footer = file_get_contents('Views/Footer.html');
								$content = $this -> templateCtrl -> procesarPlantilla_attendance_listview($content,$attendance_array,$subject_array,$hoy,$_POST['nrc'].$_POST['ciclo'],$band_ciclo,$_POST['ciclo']);
								$content = $this -> templateCtrl -> get_menu($content);
								echo $header . $content . $footer;
							}
	 					}
	 					else {
	 						#Course ID was not input
	 						$this -> errors -> not_found_input('Clave Curso');
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

 				case 'capture':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 2) {
						if (isset($_POST['capture']) && isset($_POST['ciclo']) && isset($_POST['nrc']) ) {
							$subject_array = $this -> mdl_obj -> std_obj -> obtener_curso($_POST['nrc'],$_POST['ciclo']);
							$rubros = $this -> mdl_obj -> std_obj -> get_rubros($_POST['nrc'].$_POST['ciclo']);
							$students = $this -> mdl_obj -> view_course_students($_POST['nrc'],$_POST['ciclo']);
							$header = file_get_contents('Views/Head.html');
							$content = file_get_contents('Views/capture_grade.html');
							$footer = file_get_contents('Views/Footer.html');
							$content = $this -> templateCtrl -> procesarPlantilla_capture_grade($content,$subject_array,$_POST['ciclo'],$rubros,$students);
							$content = $this -> templateCtrl -> get_menu($content);
							echo $header . $content . $footer;
							return;
						}
						if (isset($_POST['details']) && isset($_POST['clave_rubro']) && isset($_POST['studentid'])) {
							$rubro = $this -> mdl_obj -> get_rubro_details($_POST['clave_rubro']);
							if ($rubro['nocol'] == 0) {
								$calificacion_rubro = $this -> mdl_obj -> get_calificacion_rubro($rubro['clave_rubro'],$_POST['studentid'],$_POST['nrc'].$_POST['ciclo']);
								$cadena = '
									<tr>
									<th>Calificacion</th>
									</tr>
									<tr>
									<td><input name="grade[]" type="text" class="form-control" value="'.$calificacion_rubro.'" required></input>
									</td></tr>
								';
								echo $cadena;
								return;
							}
							else {
								$calificacion_rubros = $this -> mdl_obj -> get_calificacion_rubros($rubro['clave_rubro'],$_POST['studentid'],$_POST['nrc'].$_POST['ciclo'],$rubro['nocol']);
								$i = 1;
								$cadena = '<tr>';
								while ($i <= $rubro['nocol']) {
									$cadena .= '<th>Columna '.$i++.'</th>'; 
								}
								$cadena .= '</tr><tr>';
								$i = 0;
								while ($i < sizeof($calificacion_rubros)) {
									$cadena .= '<td><input name="grade[]" type="text" class="form-control" value="'.$calificacion_rubros[$i++].'" required></input></td>';
								}
								$cadena .= '</tr>';
								echo $cadena;
								return;
							}
						}
						#User is allowed to execute action
	 					#Check if Student ID exists
	 					if (isset($_POST['studentid'])) {
	 						#Validate Student ID
	 						if ($this -> validation -> validate_sid($_POST['studentid'])) {
	 							#Check if field exists
	 							if (isset($_POST['field'])) {
	 								#Check if grade exists
	 								if (isset($_POST['grade'])) {
	 									#Create array to send to the add field function
	 									if (sizeof($_POST['grade']) == 1) {
											$field_array = array ("studentid" => $_POST['studentid'],
																"field" => $_POST['field'],
																"grade" => $_POST['grade']);
											$this -> mdl_obj -> save_calificacion_rubro($field_array['grade'][0],$field_array['field'],$field_array['studentid'],$_POST['nrc'].$_POST['ciclo']);
	 									}
	 									else {
	 										$clave_curso = $_POST['nrc'].$_POST['ciclo'];
	 										$studentid = $_POST['studentid'];
	 										$i = 1;
	 										$prom = 0;
	 										foreach ($_POST['grade'] as $key => $calif_columna) {
	 											$this -> mdl_obj -> save_calificacion_evsheet($studentid,$clave_curso,$_POST['field'],$calif_columna,$i++);
	 											$prom += $calif_columna;
	 										}
	 										$prom = $prom / sizeof($_POST['grade']);
	 										$this -> mdl_obj -> save_promedio_evsheet($studentid,$clave_curso,$_POST['field'],$prom);
	 										$this -> mdl_obj -> save_calificacion_rubro($prom,$_POST['field'],$studentid,$clave_curso);
	 									}
										$subject_array = $this -> mdl_obj -> std_obj -> obtener_curso($_POST['nrc'],$_POST['ciclo']);
										$rubros = $this -> mdl_obj -> std_obj -> get_rubros($_POST['nrc'].$_POST['ciclo']);
										$students = $this -> mdl_obj -> view_course_students($_POST['nrc'],$_POST['ciclo']);
										$header = file_get_contents('Views/Head.html');
										$content = file_get_contents('Views/capture_grade.html');
										$footer = file_get_contents('Views/Footer.html');
										$content = $this -> templateCtrl -> procesarPlantilla_capture_grade($content,$subject_array,$_POST['ciclo'],$rubros,$students);
										$content = $this -> templateCtrl -> get_menu($content);
										echo $header . $content . $footer;
										return;
	 								}
	 								else {
	 									#Grade was not input
	 									$this -> errors -> not_found_input('Grade');
	 								}
	 							}
	 							else {
	 								#Field was not input
	 								$this -> errors -> not_found_input('Field');
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