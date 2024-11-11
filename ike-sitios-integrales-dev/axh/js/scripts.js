$(document).ready( function() {

	$("#loading").hide();

	toastr.options = {
		"closeButton": false,
		"debug": false,
		"newestOnTop": false,
		"progressBar": false,
		"preventDuplicates": true,
		"onclick": null,
		"showDuration": "150",
		"hideDuration": "500",
		"timeOut": "3000",
		"extendedTimeOut": "500",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut",
		"positionClass": "toast-bottom-right"
	}
	
	let minDate;
	let maxDate;

	$(function(){
        var dtToday = new Date();
    
        var month = dtToday.getMonth() + 1;// jan=0; feb=1 .......
        var day = dtToday.getDate();
        var year = dtToday.getFullYear() - 18;
        if(month < 10)
            month = '0' + month.toString();
        if(day < 10)
            day = '0' + day.toString();
    	minDate = year + '-' + month + '-' + day;
        maxDate = year + '-' + month + '-' + day;
    	$('#frmRegister3 input[name=fechaNac]').attr('max', maxDate);
    });

	$(document).on("click", ".lightbox__close", function () {
		$('.lightbox').hide();
		$('.lightbox__bg').hide();
	});

	// home

	$(document).on("click", "#btnContinuar", function (e) {
		e.preventDefault();
		$.ajax({
			url: "components/step_1.php",
			cache: false,
			type: 'POST',
			data: {},
			beforeSend: function () {
				$("#loading").show();
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (data) {
				$("#main-content").html(data);
			},
			error: function (request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	});

	$(document).on('change', "#sexo", function () {
		let fechaNac = $('input[name=fechaNac]').val().trim();
		let sexo = $('select[name=sexo]').val();

		if (fechaNac == "") {
			toastr.error("Selecciona la fecha de nacimiento.");
			$('input[name=fechaNac]').focus();
			$('select[name=sexo]').val("");
			return false;
		}

		if (sexo == "") {
			toastr.error("Seleeciona el sexo.");
			$('select[name=sexo]').focus();
			return false;
		}

		$.ajax({
			url: "backend/querys.php",
			cache: false,
			type: 'POST',
			beforeSend: function () {
				$("#loading").show();
			},
			data: {
				action: 'getSumaAsegurada',
				fechaNac: fechaNac,
				sexo: sexo
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (data) {
				$("#loading").hide();
				$("#ajaxSumaAsegurada").html(data);
			},
			error: function (request, status, error) {
				console.error(error);
				toastr.error("Error inesperado, intente más tarde por favor");
			}
		});
	});
	

	//Step 1

	$(document).on("click", "#btnStep1", function () {
		let suma_asegurada = $('input[name=suma_asegurada]').val();
		let prima_anual = $('input[name=prima_anual]').val();
		let subtotal_mensual = $('input[name=subtotal_mensual]').val();
		let idPrima = $('input[name=prima]').val();
		let sexo = $('input[name=sexo]').val();	
		
		$.ajax({
			url: "components/step_2.php",
			cache: false,
			type: 'POST',
			data: {
				suma_asegurada: suma_asegurada,
				prima_anual: prima_anual,
				subtotal_mensual: subtotal_mensual,
				idPrima: idPrima,
				sexo: sexo
			},
			beforeSend: function () {
				$("#loading").show();
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (data) {
				$("#main-content").html(data);
				if(idPrima == 0){
					$('#resumen_seguro').hide();
					$('#line_seguro').hide();
				}
			},
			error: function (request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	});

	$(document).on('change', "#sumaAseguradaS1", function () {
		let sumaAsegurada = $(this).val();
		let sumaAseguradaFormat = formatCurrency(sumaAsegurada);

		let pagoMensual = $("#sumaAseguradaS1 option:selected").attr('data-mensual');
		let idPrima = $("#sumaAseguradaS1 option:selected").attr('data-id');

		$('#sumaAsegurada').html(sumaAseguradaFormat + ' MXN');
		
		$('input[name=suma_asegurada]').val(sumaAsegurada);
		$('input[name=prima_anual]').val();
		$('input[name=subtotal_mensual]').val(pagoMensual);
		$('input[name=prima]').val(idPrima);
		$('input[name=sexo]').val($('select[name=sexo]').val());

		let pagoMensualFormat = formatCurrency(pagoMensual, 2);

		$('#pagoMensual').html(pagoMensualFormat + ' MXN');

		$('#resumenStep1').show();
		$('#tblStep1').show();
		$('.separator__line.s1').show();
	});

	//Step 2

	$(document).on("click", "#btnStep2", function () {
		let asistencias = $("input[name='asistencia[]']").map((i, asistencia) => {
			if (asistencia.checked) return asistencia.value;
		}).get();
		let isChecked = $('input[name=clienteHsbc]').is(':checked');
		let isChecked2 = $('input[name=avisoHsbc]').is(':checked');
		let isChecked3 = $('input[name=residenteHsbc]').is(':checked');
		let suma_asegurada = $('input[name=suma_asegurada]').val();
		let prima_anual = $('input[name=prima_anual]').val();
		let subtotal_mensual = $('input[name=subtotal_mensual]').val();
		let subtotal_mensual_asistencia = $('input[name=subtotal_mensual_asistencia]').val();
		let idPrima = $('input[name=prima]').val();
		let sexo = $('input[name=sexo]').val();
		let captcha_response = document.getElementById("g-recaptcha-response").value;

		if (subtotal_mensual_asistencia == "" && subtotal_mensual == "0") {
			toastr.error("Debe seleccionar un seguro o una asistencia para poder continuar.");
			return false;
		}
		
		if (!isChecked) {
			toastr.error("Debe ser cliente HSBC para poder continuar.");
			return false;
		}

		if (!isChecked2) {
			toastr.error("Debe aceptar el aviso de privacidad para poder continuar.");
			return false;
		}

		if (!isChecked3) {
			toastr.error("Debe aceptar que se encuentra en el territorio nacional para poder continuar.");
			return false;
		}

		$.ajax({
			url: "backend/process/captcha.php",
			cache: false,
			type: 'POST',
			beforeSend: function () {
				$("#loading").show();
			},
			data: {
				captcha_response : captcha_response
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (data) {
				$("#loading").hide();				
				console.log(data);

				let datos = JSON.parse(data);
				let success = datos.success;
				
				if(success == false){
					toastr.error("Algo salio mal, error en el captcha.");
					return false;
				}

				$.ajax({
					url: "components/step_3.php",
					cache: false,
					type: 'POST',
					data: {
						suma_asegurada: suma_asegurada,
						prima_anual: prima_anual,
						subtotal_mensual: subtotal_mensual,
						subtotal_mensual_asistencia: subtotal_mensual_asistencia,
						idPrima: idPrima,
						asistencias: asistencias,
						sexo: sexo
					},
					beforeSend: function () {
						$("#loading").show();
					},
					complete: function () {
						$("#loading").hide();
					},
					success: function (data) {
						$("#main-content").html(data);
					},
					error: function (request, status, error) {
						console.log('Ha ocurrido un error!');
					}
				});	
			},
			error: function (request, status, error) {
				console.error(error);
				toastr.error("Error inesperado, intente más tarde por favor");
			}
		});		
	});

	$(document).on('click', '.chkbox2', function () {
		$('#tblStep2 .tbl__note').show();
		let checked = $(this).is(':checked');
		let id = $(this).val();
		let costo = $(this).attr('data-costo');
		let total = $('#tblStep2Total').attr('data-total');
		var totalChecked = $('#tblAsistencias').find('input[name="asistencia[]"]:checked').length;
		
		if (checked) {
			total = parseFloat(total) + parseFloat(costo);
			$('#tblStep2 .a' + id).css('display', 'flex');
		} else {
			total = parseFloat(total) - parseFloat(costo);
			$('#tblStep2 .a' + id).hide();
		}

		$('#tblStep2Total').attr('data-total', total);
		let totalFormat = total;

		$('#tblStep2Total .subtotal2').html('$' + totalFormat.toFixed(2) + ' MXN');
		$('input[name=subtotal_mensual_asistencia]').val(totalFormat.toFixed(2));
		if (totalChecked) {
			$('#tblStep2 .tbl__note').show();
			$('#tblStep2Total .subtotal').html('Total mensual a pagar del seguro + asistencias:');
		} else {
			$('#tblStep2 .tbl__note').hide();
			$('#tblStep2Total .subtotal').html('Total mensual a pagar del seguro:');
		}

	});

	$('#clienteHsbc').on('click', function() {
		if ($('#clienteHsbc').is(':checked') && $('#avisoPrivacidadA').is(':checked') && $('#territorioNacional').is(':checked')) {
			$('#btnStep2').removeClass('disabled');
		} else {
			$('#btnStep2').addClass('disabled');
		}
	});

	$('#avisoPrivacidadA').on('click', function() {
		if ($('#clienteHsbc').is(':checked') && $('#avisoPrivacidadA').is(':checked') && $('#territorioNacional').is(':checked')) {
			$('#btnStep2').removeClass('disabled');
		} else {
			$('#btnStep2').addClass('disabled');
		}
	});

	$('#territorioNacional').on('click', function() {
		if ($('#clienteHsbc').is(':checked') && $('#avisoPrivacidadA').is(':checked') && $('#territorioNacional').is(':checked')) {
			$('#btnStep2').removeClass('disabled');
		} else {
			$('#btnStep2').addClass('disabled');
		}
	});

	//Step 3

	$(document).on("click", "#btnStep3", function () {
		let idPrima = $('input[name=prima]').val();
		let sexo = $('input[name=sexo]').val();
		let asistencias = $('input[name=asistencias]').val();
		let nombre = $('#frmRegister3 input[name=nombre]').val().trim();
		let segundoNombre = $('#frmRegister3 input[name=segundoNombre]').val().trim();
		let apellidoPaterno = $('#frmRegister3 input[name=apellidoPaterno]').val().trim();
		let apellidoMaterno = $('#frmRegister3 input[name=apellidoMaterno]').val().trim();
		let fechaNac = $('#frmRegister3 input[name=fechaNac]').val().trim();
		let rfc = $('#frmRegister3 input[name=rfc]').val().trim();
		let email = $('#frmRegister3 input[name=email]').val().trim();
		let telefono = $('#frmRegister3 input[name=telefono]').val().trim();

		$('#frmErrMsg3').hide();

		if (nombre == '') {
			toastr.error("Escribe tu nombre");
			$('input[name=nombre]').focus();
			return false;
		}

		if (apellidoPaterno == '') {
			toastr.error("Escribe tu apellido paterno");
			$('input[name=apellidoPaterno]').focus();
			return false;
		}

		if (apellidoMaterno == '') {
			toastr.error("Escribe tu apellido materno");
			$('input[name=apellidoMaterno]').focus();
			return false;
		}

		if (fechaNac == '') {
			toastr.error("Selecciona la fecha de tu nacimiento");
			$('input[name=fechaNac]').focus();
			return false;
		}

		if (fechaNac > maxDate) {
			toastr.error("Debe ser mayor de 18 años");
			$('input[name=fechaNac]').focus();
			return false;
		}

		if (rfc == '') {			
			toastr.error("Escribe el RFC");
			$('input[name=rfc]').focus();
			return false;
		}

		if (rfc.length <= 11) {			
			toastr.error("El RFC debe tener entre 12 y 13 caracteres");
			$('input[name=rfc]').focus();
			return false;
		}

		if (email == '') {
			toastr.error("Escribe tu correo electrónico");
			$('input[name=email]').focus();
			return false;
		}

		if (!emailIsValid(email)) {
			toastr.error("Escribe un correo válido");
			$('input[name=email]').focus();
			return false;
		}

		if (telefono == '') {
			toastr.error("Escribe tu teléfono");
			$('input[name=telefono]').focus();
			return false;
		}

		if (telefono.length < 10) {
			toastr.error("El teléfono tiene que tener 10 dígitos");
			$('input[name=telefono]').focus();
			return false;
		}

		$.ajax({
			url: "backend/querys.php",
			cache: false,
			type: 'POST',
			beforeSend: function () {
				$("#loading").show();
			},
			data: {
				action: 'saveClient',
				idPrima: idPrima,
				asistencias: asistencias,
				nombre: nombre,
				segundoNombre: segundoNombre,
				apellidoPaterno: apellidoPaterno,
				apellidoMaterno: apellidoMaterno,
				rfc: rfc,
				fechaNac: fechaNac,
				email: email,
				telefono: telefono,
				sexo: sexo
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (data) {
				$("#loading").hide();
				console.log(data.idCliente);
				$.ajax({
					url: "components/step_4.php",
					cache: false,
					type: 'POST',
					data: {
						idCliente: data.idCliente
					},
					beforeSend: function () {
						$("#loading").show();
					},
					complete: function () {
						$("#loading").hide();
					},
					success: function (data) {
						$("#main-content").html(data);
						setTimer();
					},
					error: function (request, status, error) {
						console.error(error);
					}
				});
			},
			error: function (request, status, error) {
				console.error(error);
				toastr.error("Error inesperado, intente más tarde por favor");
			}
		});
	});

	//Step 4

	$(document).on("click", "#btnStep4", function () {
		let codigo = $('input[name=codigoSms]').val().trim();
		let idCliente = $('input[name=idCliente]').val();

		$('#frmErrMsg4').hide();

		// $.ajax({
		// 	url: "components/step_5.php",
		// 	cache: false,
		// 	type: 'POST',
		// 	data: {
		// 		idCliente: idCliente
		// 	},
		// 	beforeSend: function () {
		// 		$("#loading").show();
		// 	},
		// 	complete: function () {
		// 		$("#loading").hide();
		// 	},
		// 	success: function (data) {
		// 		$("#main-content").html(data);
		// 	},
		// 	error: function (request, status, error) {
		// 		console.log('Ha ocurrido un error!');
		// 	}
		// });

		verifyCode(idCliente, codigo, function (codeIsValid) {
			if (codeIsValid) {
				$.ajax({
					url: "components/step_5.php",
					cache: false,
					type: 'POST',
					data: {
						idCliente: idCliente
					},
					beforeSend: function () {
						$("#loading").show();
					},
					complete: function () {
						$("#loading").hide();
					},
					success: function (data) {
						$("#main-content").html(data);
					},
					error: function (request, status, error) {
						console.log('Ha ocurrido un error!');
					}
				});

			} else {
				toastr.error("El código es inválido");
				$('input[name=codigoSms]').focus();
			}
		});
	});

	//Step 5

	$(document).on("click", "#btnStep5", function () {
		let idCliente = $('input[name=idCliente]').val();
		$.ajax({
			url: "components/add_beneficiare.php",
			cache: false,
			type: 'POST',
			data: {
				idCliente: idCliente
			},
			beforeSend: function () {
				$("#loading").show();
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (data) {
				$("#main-content").html(data);
			},
			error: function (request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	});

	//Agregar Beneficiario

	$(document).on("click", "#btnStepBenef", function () {
		let idCliente = $('input[name=idCliente]').val();
		let parentesco = $('#frmBeneficiario select[name=parentesco]').val();
		let nombre = $('#frmBeneficiario input[name=nombre]').val().trim();
		let segundoNombre = $('#frmBeneficiario input[name=segundoNombre]').val().trim();
		let apellidoPaterno = $('#frmBeneficiario input[name=apellidoPaterno]').val().trim();
		let apellidoMaterno = $('#frmBeneficiario input[name=apellidoMaterno]').val().trim();
		let estadoCivil = $('#frmBeneficiario select[name=estadoCivil]').val();
		let sexo = $('#frmBeneficiario select[name=sexo]').val();
		let fechaNac = $('#frmBeneficiario input[name=fechaNac]').val().trim();
		let rfc = $('#frmBeneficiario input[name=rfc]').val().trim();
		let nacionalidad = $('#frmBeneficiario select[name=nacionalidad]').val();
		let actividad = $('#frmBeneficiario select[name=actividad]').val();
		let residencia = $('#frmBeneficiario select[name=residencia]').val();

		$('#frmErrMsgBenef').hide();

		if (parentesco == '') {
			toastr.error("Seleccione el parentesco");
			$('input[name=parentesco]').focus().select();
			return false;
		}

		if (nombre == '') {
			toastr.error("Escribe el nombre");
			$('input[name=nombre]').focus();
			return false;
		}

		if (apellidoPaterno == '') {
			toastr.error("Escribe el apellido paterno");
			$('input[name=apellidoPaterno]').focus();
			return false;
		}

		if (apellidoMaterno == '') {
			toastr.error("Escribe el apellido materno");
			$('input[name=apellidoMaterno]').focus();
			return false;
		}

		if (estadoCivil == '') {
			toastr.error("Seleccione el estado civil");
			$('input[name=estadoCivil]').focus();
			return false;
		}

		if (fechaNac == '') {
			toastr.error("Selecciona la fecha de el nacimiento");
			$('input[name=fechaNac]').focus();
			return false;
		}

		if (rfc == '') {
			toastr.error("Escribe el RFC");
			$('input[name=rfc]').focus();
			return false;
		}

		if (rfc.length <= 11) {			
			toastr.error("El RFC debe tener entre 12 y 13 caracteres");
			$('input[name=rfc]').focus();
			return false;
		}

		if (nacionalidad == '') {
			toastr.error("Selecciona la nacionalidad");
			$('input[name=nacionalidad]').focus();
			return false;
		}

		if (actividad == '') {
			toastr.error("Seleccione la actividad económica");
			$('input[name=actividad]').focus();
			return false;
		}

		if (residencia == '') {
			toastr.error("Seleccione el lugar de residencia");
			$('input[name=residencia]').focus();
			return false;
		}

		$.ajax({
			url: "backend/querys.php",
			cache: false,
			type: 'POST',
			data: {
				action: 'saveBeneficiare',
				idCliente: idCliente,
				parentesco: parentesco,
				nombre: nombre,
				segundoNombre: segundoNombre,
				apellidoPaterno: apellidoPaterno,
				apellidoMaterno: apellidoMaterno,
				estadoCivil: estadoCivil,
				sexo: sexo,
				fechaNac: fechaNac,
				rfc: rfc,
				nacionalidad: nacionalidad,
				actividad: actividad,
				residencia: residencia
			},
			success: function (data) {
				$.ajax({
					url: "components/step_6.php",
					cache: false,
					type: 'POST',
					data: {
						idCliente: idCliente
					},
					beforeSend: function () {
						$("#loading").show();
					},
					complete: function () {
						$("#loading").hide();
					},
					success: function (data) {
						$("#main-content").html(data);
					},
					error: function (request, status, error) {
						console.log('Ha ocurrido un error!');
					}
				});
			},
			error: function (request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	});

	$(document).on("click", "#btnUpdateBenef", function () {
		let idCliente = $('input[name=idCliente]').val();
		let idBeneficiario = $('input[name=idBeneficiario]').val();
		let parentesco = $('#frmEditBenef select[name=parentesco]').val();
		let nombre = $('#frmEditBenef input[name=nombre]').val().trim();
		let segundoNombre = $('#frmEditBenef input[name=segundoNombre]').val().trim();
		let apellidoPaterno = $('#frmEditBenef input[name=apellidoPaterno]').val().trim();
		let apellidoMaterno = $('#frmEditBenef input[name=apellidoMaterno]').val().trim();
		let estadoCivil = $('#frmEditBenef select[name=estadoCivil]').val();
		let sexo = $('#frmEditBenef select[name=sexo]').val();
		let fechaNac = $('#frmEditBenef input[name=fechaNac]').val().trim();
		let rfc = $('#frmEditBenef input[name=rfc]').val();
		let nacionalidad = $('#frmEditBenef select[name=nacionalidad]').val();
		let actividad = $('#frmEditBenef select[name=actividad]').val();
		let residencia = $('#frmEditBenef select[name=residencia]').val();

		$('#frmErrMsgBenef2').hide();

		if (parentesco == '') {
			toastr.error("Seleccione el parentesco");
			$('input[name=parentesco]').focus().select();
			return false;
		}

		if (nombre == '') {
			toastr.error("Escribe el nombre");
			$('input[name=nombre]').focus();
			return false;
		}

		if (apellidoPaterno == '') {
			toastr.error("Escribe el apellido paterno");
			$('input[name=apellidoPaterno]').focus();
			return false;
		}

		if (apellidoMaterno == '') {
			toastr.error("Escribe el apellido materno");
			$('input[name=apellidoMaterno]').focus();
			return false;
		}

		if (estadoCivil == '') {
			toastr.error("Seleccione el estado civil");
			$('input[name=estadoCivil]').focus();
			return false;
		}

		if (fechaNac == '') {
			toastr.error("Selecciona la fecha de el nacimiento");
			$('input[name=fechaNac]').focus();
			return false;
		}

		if (rfc == '') {
			toastr.error("Escribe el RFC");
			$('input[name=rfc]').focus();
			return false;
		}

		if (rfc.length <= 11) {			
			toastr.error("El RFC debe tener entre 12 y 13 caracteres");
			$('input[name=rfc]').focus();
			return false;
		}

		if (nacionalidad == '') {
			toastr.error("Selecciona la nacionalidad");
			$('input[name=nacionalidad]').focus();
			return false;
		}

		if (actividad == '') {
			toastr.error("Seleccione la actividad económica");
			$('input[name=actividad]').focus();
			return false;
		}

		if (residencia == '') {
			toastr.error("Seleccione el lugar de residencia");
			$('input[name=residencia]').focus();
			return false;
		}

		$.ajax({
			url: "backend/querys.php",
			cache: false,
			type: 'POST',
			data: {
				action: 'updateBeneficiare',
				idBeneficiario: idBeneficiario,
				parentesco: parentesco,
				nombre: nombre,
				segundoNombre: segundoNombre,
				apellidoPaterno: apellidoPaterno,
				apellidoMaterno: apellidoMaterno,
				estadoCivil: estadoCivil,
				sexo: sexo,
				fechaNac: fechaNac,
				rfc: rfc,
				nacionalidad: nacionalidad,
				actividad: actividad,
				residencia: residencia
			},
			success: function (data) {
				$.ajax({
					url: "components/step_6.php",
					cache: false,
					type: 'POST',
					data: {
						idCliente: idCliente,
						idBeneficiario: idBeneficiario
					},
					beforeSend: function () {
						$("#loading").show();
					},
					complete: function () {
						$("#loading").hide();
					},
					success: function (data) {
						$("#main-content").html(data);
					},
					error: function (request, status, error) {
						console.log('Ha ocurrido un error!');
					}
				});
			},
			error: function (request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	});

	$(document).on("click", "#btnNewBenef", function () {		
		let idCliente = $('input[name=idCliente]').val();

		$.ajax({
			url: "components/add_beneficiare.php",
			cache: false,
			type: 'POST',
			data: {
				idCliente: idCliente
			},
			beforeSend: function () {
				$("#loading").show();
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (data) {
				$("#main-content").html(data);
			},
			error: function (request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	});


	$(document).on("click", "#btnStep6", function () {
		let idCliente = $('input[name=idCliente]').val();
		let porcentaje = $('#listBenef input[name=porcentaje]').val().trim();

		if (porcentaje == '') {
			toastr.error("Ingrese el porcentaje");
			$('#listBenef input[name=porcentaje]').focus();
			return false;
		}
		if (porcentaje >= 0 && porcentaje <= 100) {

			$.ajax({
				url: "components/step_7.php",
				cache: false,
				type: 'POST',
				data: {
					idCliente: idCliente
				},
				beforeSend: function () {
					$("#loading").show();
				},
				complete: function () {
					$("#loading").hide();
				},
				success: function (data) {
					$("#main-content").html(data);
				},
				error: function (request, status, error) {
					console.log('Ha ocurrido un error!');
				}
			});
		} else {
			toastr.error("Ingrese un porcentaje entre 0 y 100");
			$('input[name=porcentaje]').focus();
			return false;
		}


	});

	$(document).on("click", "#btnStep7", function () {
		let idCliente = $('input[name=idCliente]').val();
		$.ajax({
			url: "components/step_8.php",
			cache: false,
			type: 'POST',
			data: {
				idCliente: idCliente
			},
			beforeSend: function () {
				$("#loading").show();
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (data) {
				$("#main-content").html(data);
			},
			error: function (request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	});

	$(document).on("click", "#btnStep8", function () {
		let idCliente = $('input[name=idCliente]').val();
		let numeroTarjeta = $('#frmCard input[name=numeroTarjeta]').val().trim();
		let condiciones = $('#frmCard input[name=condiciones]').is(':checked');
		let envio = $('#frmCard input[name=envio]').is(':checked');

		$('#frmErrMsg8').hide();

		if (numeroTarjeta == '') {
			toastr.error("Escriba el número de la tarjeta.");
			$('input[name=numeroTarjeta]').focus();
			return false;
		}

		if (numeroTarjeta.length < 16) {
			toastr.error("El número de la tarjeta debe de tener 16 dígitos.");
			$('input[name=numeroTarjeta]').focus();
			return false;
		}

		if (condiciones == '') {
			toastr.error("Debe aceptar las condiciones generales.");
			return false;
		}

		if (envio == '') {
			toastr.error("Debe aceptar el envió de pólizas y condiciones generales.");
			return false;
		}

		$.ajax({
			url: "backend/querys.php",
			cache: false,
			type: 'POST',
			data: {
				action: 'verifyCard',
				idCliente: idCliente,
				numeroTarjeta: numeroTarjeta
			},
			success: function (data) {
				console.log(data.mensaje);
				if(data.mensaje != 'Tarjeta incorrecta'){
					$.ajax({
						url: "components/step_final.php",
						cache: false,
						type: 'POST',
						data: {},
						beforeSend: function () {
							$("#loading").show();
						},
						complete: function () {
							$("#loading").hide();
						},
						success: function (data) {
							$("#main-content").html(data);
						},
						error: function (request, status, error) {
							console.log('Ha ocurrido un error!');
						}
					});
				}else{
					toastr.error("Ingresa una tarjeta HSBC válida.");
					$('input[name=numeroTarjeta]').focus();
					return false;
				}				
			},
			error: function (request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	});

});

function sendNewCode(idCliente) {
	$.ajax({
		url: "backend/querys.php",
		cache: false,
		type: 'POST',
		dataType: 'JSON',
		beforeSend: function () {
			$("#loading").show();
		},
		data: {
			action: 'sendNewCode',
			idCliente: idCliente
		},
		complete: function () {
			$("#loading").hide();
		},
		success: function (response) {
			if (response.status === "ok") {
				$('#sendNewCode').hide();
				$('#btnStep4').show();
				setTimer();
			} else {
				toastr.error(response.status);
			}

		},
		error: function (request, status, error) {
			console.error(error);
			toastr.error("Error inesperado al reenviar código, intente nuevamente por favor");
		}
	});
}


function showAsistencia(id) {
	$('.lightbox__bg').show();
	$('#' + id).show();
}

function editBenef(id) {
	let idBeneficiario = id;
	let idCliente = $('input[name=idCliente]').val();
	$.ajax({
		url: "components/edit_beneficiare.php",
		cache: false,
		type: 'POST',
		data: {
			idCliente: idCliente,
			idBeneficiario: idBeneficiario
		},
		beforeSend: function () {
			$("#loading").show();
		},
		complete: function () {
			$("#loading").hide();
		},
		success: function (data) {
			$("#main-content").html(data);
		},
		error: function (request, status, error) {
			console.log('Ha ocurrido un error!');
		}
	});
}

function deleteBenef(id) {
	let idBeneficiario = id;
	let idCliente = $('input[name=idCliente]').val();
	let resp = confirm('¿Está seguro de eliminar este beneficiario?');

	if (resp) {
		$.ajax({
			url: "backend/querys.php",
			cache: false,
			type: 'POST',
			data: {
				action: 'deleteBeneficiare',
				idBeneficiario: idBeneficiario
			},
			success: function (data) {
				$.ajax({
					url: "components/step_6.php",
					cache: false,
					type: 'POST',
					data: {
						idCliente: idCliente
					},
					beforeSend: function () {
						$("#loading").show();
					},
					complete: function () {
						$("#loading").hide();
					},
					success: function (data) {
						$("#main-content").html(data);
					},
					error: function (request, status, error) {
						console.log('Ha ocurrido un error!');
					}
				});
			},
			error: function (request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	}
}

function go2Step(id) {
	$('.step').hide();
	$('#step' + id).show();
	$("html, body").animate({ scrollTop: 0 }, "slow");
  	return false;
}

function go2StepEdit(id) {
	$('.step').hide();
	$('#step' + id).show();
	$('#isBack2Edit').val(1);
	$("html, body").animate({ scrollTop: 0 }, "slow");
  	return false;
}

function emailIsValid (email) {
  	return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
}

function validateString(string) {
  	var letters = /^[a-zA-Za-zñáéíóúÑÁÉÍÓÚüÜ, ]+$/;
  	return string.match(letters);
}

function isNumber (value) {
    return /^[0-9]+$/.test(value)
}

function formatCurrency(monto, decimales = 0) {

	if (decimales > 0) {
		return Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN'}).format(monto);
	} else {
		return Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN',minimumFractionDigits:0,maximumFractionDigits:0}).format(monto);
	}
}

function setTimer() {

	var countDownDate = new Date();
	countDownDate.setSeconds(countDownDate.getSeconds() + 116);

	countDownDate.getTime();

	// Update the count down every 1 second
	var x = setInterval(function () {

		// Get today's date and time
		var now = new Date().getTime();

		// Find the distance between now and the count down date
		var distance = countDownDate - now;

		// Time calculations for days, hours, minutes and seconds
		var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		var seconds = Math.floor((distance % (1000 * 60)) / 1000);

		if (seconds < 10) {
			seconds = "0" + seconds;
		}

		$('#timer i').html(minutes + ":" + seconds);

		// If the count down is finished, write some text
		if (distance < 0) {
			clearInterval(x);
			$('#timer i').html("0:00");
			$('#sendNewCode').show();
			$('#btnStep4').hide();

		}
	}, 1000);

}

// function sendNewCode() {

// 	let resp = confirm('¿Está seguro de generar un nuevo código?');

// 	if (resp) {
// 		alert('El nuevo código se ha enviado a tu celular.');
// 		clearInterval(xTimer);
// 		setTimer();
// 	}

// }