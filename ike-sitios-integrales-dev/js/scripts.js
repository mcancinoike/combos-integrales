
let session = {
	cliente: {
		clientType: app,
		name: '',
		middle_name: '',
		pater_surname: '',
		mater_surname: '',
		cell_phone: 0,
		confirm_cell: 0,
		email: '',
		date_birth: '',
		rfc: '',
		id_prima: 0,
		sexo: ''
	},
	asistencias: [],
	step: 0,
	seguro: {
		sumaAsegurada: 0,
		pagoAnual: 0,
		pagoMensual: 0
	},
	pagoTotalMensual: 0,
	beneficiarios: [],
	id_cliente: 0,
	check: {
		clienteHsbc: false,
		avisoHsbc: false,
		residenteHsbc: false
	},
	card: '',
	captcha: false
}

$(document).ready(function () {

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

	$(function () {
		var dtToday = new Date();

		var month = dtToday.getMonth() + 1; // jan=0; feb=1 .......
		var day = dtToday.getDate();
		var year = dtToday.getFullYear() - 18;
		if (month < 10)
			month = '0' + month.toString();
		if (day < 10)
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

	$(document).on("click", "#step0", function (e) {
		e.preventDefault();
		goStep(1);
	});

	// evento exclusivo ah

	$(document).on('change', "input[name=fechaNac]", function () {
		const year = new Date($(this).val()).getFullYear(),
		      yearNow = new Date().getFullYear();

		if ( year > 1900 && year < yearNow && $('select[name=sexo]').val() !== '' && session.step === 1)
			$("#sexo").change();
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
		session.cliente.sexo = sexo;
		session.cliente.date_birth = fechaNac;
		$.ajax({
			url: relativePath + "backend/querys.php",
			cache: false,
			type: 'POST',
			dataType: "JSON",
			beforeSend: function () {
				$("#loading").show();
				$("#btnStep1").hide();
			},
			data: {
				action: 'getSumaAsegurada',
				fechaNac: fechaNac,
				sexo: sexo
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (response) {
				$("#loading").hide();

				if (response.code == 200) {
					$("#ajaxSumaAsegurada").html(response.data);
					$("input[name=sexo]").val(sexo);

					if (session.cliente.id_prima !== 0){
						$("#sumaAseguradaS1").val(session.cliente.id_prima).change();
					}

				} else {
					toastr.error(response.msg);
				}
			},
			error: function (request, status, error) {
				console.error(error);
				toastr.error("Error inesperado, intente más tarde por favor");
			}
		});
	});

	$(document).on("change", "#sumaAseguradaS1", function () {
		if ($(this).val() !== '') {
			$("#btnStep1").show();
			addSeguro($("option:selected", this));
		} else
			$("#btnStep1").hide();
	});

	// evneto exclusivo ap

	$(document).on('click', "input[name=seguro]", function () {
		addSeguro($(this));
	});

	//Step 1

	$(document).on("click", "#btnStep1", function (e) {
		e.preventDefault();
		goStep(2);
	});



	//Step 2

	$(document).on('click', '.chkbox2', function () {
		checkAsistencias($(this));
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

	$(document).on("click", "#btnStep2", function () {
		let asistencias = $("input[name='asistencia[]']").map((i, asistencia) => {
			if (asistencia.checked) return asistencia.value;
		}).get();

		const captcha_response = session.captcha ? '' : $(".g-recaptcha-response").last().val();

		session.asistencias = asistencias;
		session.check.clienteHsbc = $('input[name=clienteHsbc]').is(':checked');
		session.check.avisoHsbc = $('input[name=avisoHsbc]').is(':checked');
		session.check.residenteHsbc = $('input[name=residenteHsbc]').is(':checked');

		if (session.pagoTotalMensual == 0) {
			toastr.error("Debe seleccionar un seguro o una asistencia para poder continuar.");
			return false;
		}
		
		if (!session.check.clienteHsbc) {
			toastr.error("Debe ser cliente HSBC para poder continuar.");
			return false;
		}

		if (!session.check.avisoHsbc) {
			toastr.error("Debe aceptar el aviso de privacidad para poder continuar.");
			return false;
		}

		if (!session.check.residenteHsbc) {
			toastr.error("Debe aceptar que se encuentra en el territorio nacional para poder continuar.");
			return false;
		}

		if (!session.captcha) {
			$.ajax({
				url: relativePath + "backend/process/captcha.php",
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
					session.captcha = true;
					goStep(3);
				},
				error: function (request, status, error) {
					console.error(error);
					toastr.error("Error inesperado, intente más tarde por favor");
				}
			});
		} else {
			goStep(3);
		}

	});

	//Step 3

	$(document).on("click", "#btnStep3", function () {

		session.cliente.name = $('#frmRegister3 input[name=nombre]').val().trim();
		session.cliente.middle_name = $('#frmRegister3 input[name=segundoNombre]').val().trim();
		session.cliente.pater_surname = $('#frmRegister3 input[name=apellidoPaterno]').val().trim();
		session.cliente.mater_surname = $('#frmRegister3 input[name=apellidoMaterno]').val().trim();
		session.cliente.date_birth = $('#frmRegister3 input[name=fechaNac]').val().trim();
		session.cliente.rfc = $('#frmRegister3 input[name=rfc]').val().trim();
		session.cliente.email = $('#frmRegister3 input[name=email]').val().trim();
		session.cliente.cell_phone = $('#frmRegister3 input[name=telefono]').val().trim();

		$('#frmErrMsg3').hide();

		if (session.cliente.name == '') {
			toastr.error("Escribe tu nombre");
			$('input[name=nombre]').focus();
			return false;
		}

		if (session.cliente.pater_surname == '') {
			toastr.error("Escribe tu apellido paterno");
			$('input[name=apellidoPaterno]').focus();
			return false;
		}

		if (session.cliente.mater_surname == '') {
			toastr.error("Escribe tu apellido materno");
			$('input[name=apellidoMaterno]').focus();
			return false;
		}

		if (session.cliente.date_birth == '') {
			toastr.error("Selecciona la fecha de tu nacimiento");
			$('input[name=fechaNac]').focus();
			return false;
		}

		if (session.cliente.date_birth > maxDate) {
			toastr.error("Debe ser mayor de 18 años");
			$('input[name=fechaNac]').focus();
			return false;
		}

		if (session.cliente.rfc == '') {
			toastr.error("Escribe el RFC");
			$('input[name=rfc]').focus();
			return false;
		}

		if (session.cliente.rfc.length <= 11) {
			toastr.error("El RFC debe tener entre 12 y 13 caracteres");
			$('input[name=rfc]').focus();
			return false;
		}

		if (session.cliente.email == '') {
			toastr.error("Escribe tu correo electrónico");
			$('input[name=email]').focus();
			return false;
		}

		if (!emailIsValid(session.cliente.email)) {
			toastr.error("Escribe un correo válido");
			$('input[name=email]').focus();
			return false;
		}

		if (session.cliente.cell_phone == '') {
			toastr.error("Escribe tu teléfono");
			$('input[name=telefono]').focus();
			return false;
		}

		if (session.cliente.cell_phone.length < 10) {
			toastr.error("El teléfono tiene que tener 10 dígitos");
			$('input[name=telefono]').focus();
			return false;
		}
		const action = session.id_cliente == 0 ? 'saveClient' : 'updateClient';

		$.ajax({
			url: relativePath + "backend/querys.php",
			cache: false,
			type: 'POST',
			dataType: "JSON",
			beforeSend: function () {
				$("#loading").show();
			},
			data: {
				idCliente: session.id_cliente,
				action: action,
				idPrima: session.cliente.id_prima,
				asistencias: session.asistencias,
				nombre: session.cliente.name,
				segundoNombre: session.cliente.middle_name,
				apellidoPaterno: session.cliente.pater_surname,
				apellidoMaterno: session.cliente.mater_surname,
				rfc: session.cliente.rfc,
				fechaNac: session.cliente.date_birth,
				email: session.cliente.email,
				telefono: session.cliente.cell_phone,
				sexo: session.cliente.sexo,
				clientType: app,
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (response) {
				$("#loading").hide();

				if (response.code === 200) {
					const step = session.id_cliente === 0 || session.cliente.confirm_cell === 0 ? 4 :
										session.beneficiarios.length === 0 && session.cliente.id_prima !== 0 ? 5 :
										session.beneficiarios.length !== 0 && session.cliente.id_prima !== 0 ? 6 : 7;

					if (session.id_cliente === 0)
						session.id_cliente = response.idCliente;

						goStep(step);
				} else
					toastr.error(response.msg);

			},
			error: function (request, status, error) {
				console.error(error);
				toastr.error("Error inesperado, intente más tarde por favor");
			}
		});
	});

	//Step 4

	$(document).on("click", "#btnStep4", function () {
		const codigo = $('input[name=codigoSms]').val().trim(),
		 		step = session.cliente.id_prima == 0 ? '7' : '5';

		$('#frmErrMsg4').hide();

		if (session.cliente.confirm_cell == 1)
			goStep(step);
		else {
			verifyCode(session.id_cliente, codigo, function (codeIsValid) {
				if (codeIsValid){
					session.cliente.confirm_cell = 1;
					goStep(step);
				} else {
					toastr.error("El código es inválido");
					$('input[name=codigoSms]').focus();
				}
			});
		}
	});

	$(document).on("click", "#sendNewCode", function () {
		sendNewCode(session.id_cliente);
	});
	//Step 5

	$(document).on("click", "#btnStep5", function () {
		goStep("5-2");
	});

	//Agregar Beneficiario

	$(document).on("click", "#btnStepBenef", function () {


		const beneficiarios = {
			parentesco: $('#frmBeneficiario select[name=parentesco]').val(),
			nombre: $('#frmBeneficiario input[name=nombre]').val().trim(),
			segundoNombre: $('#frmBeneficiario input[name=segundoNombre]').val().trim(),
			apellidoPaterno: $('#frmBeneficiario input[name=apellidoPaterno]').val().trim(),
			apellidoMaterno: $('#frmBeneficiario input[name=apellidoMaterno]').val().trim(),
			estadoCivil: $('#frmBeneficiario select[name=estadoCivil]').val(),
			sexo: $('#frmBeneficiario select[name=sexo]').val(),
			fechaNac: $('#frmBeneficiario input[name=fechaNac]').val().trim(),
			rfc: $('#frmBeneficiario input[name=rfc]').val().trim(),
			nacionalidad: $('#frmBeneficiario select[name=nacionalidad]').val(),
			actividad: $('#frmBeneficiario select[name=actividad]').val(),
			residencia: $('#frmBeneficiario select[name=residencia]').val()
		};


		$('#frmErrMsgBenef').hide();

		if (beneficiarios.parentesco == '') {
			toastr.error("Seleccione el parentesco");
			$('input[name=parentesco]').focus().select();
			return false;
		}

		if (beneficiarios.nombre == '') {
			toastr.error("Escribe el nombre");
			$('input[name=nombre]').focus();
			return false;
		}

		if (beneficiarios.apellidoPaterno == '') {
			toastr.error("Escribe el apellido paterno");
			$('input[name=apellidoPaterno]').focus();
			return false;
		}

		if (beneficiarios.apellidoMaterno == '') {
			toastr.error("Escribe el apellido materno");
			$('input[name=apellidoMaterno]').focus();
			return false;
		}

		if (beneficiarios.estadoCivil == '') {
			toastr.error("Seleccione el estado civil");
			$('input[name=estadoCivil]').focus();
			return false;
		}

		if (beneficiarios.fechaNac == '') {
			toastr.error("Selecciona la fecha de el nacimiento");
			$('input[name=fechaNac]').focus();
			return false;
		}

		if (beneficiarios.rfc == '') {
			toastr.error("Escribe el RFC");
			$('input[name=rfc]').focus();
			return false;
		}

		if (beneficiarios.rfc.length <= 11) {
			toastr.error("El RFC debe tener entre 12 y 13 caracteres");
			$('input[name=rfc]').focus();
			return false;
		}

		if (beneficiarios.nacionalidad == '') {
			toastr.error("Selecciona la nacionalidad");
			$('input[name=nacionalidad]').focus();
			return false;
		}

		if (beneficiarios.actividad == '') {
			toastr.error("Seleccione la actividad económica");
			$('input[name=actividad]').focus();
			return false;
		}

		if (beneficiarios.residencia == '') {
			toastr.error("Seleccione el lugar de residencia");
			$('input[name=residencia]').focus();
			return false;
		}

		session.beneficiarios.push(beneficiarios);
		let dataSend = beneficiarios;

		dataSend.action = 'saveBeneficiare';
		dataSend.idCliente = session.id_cliente;


		$.ajax({
			url: relativePath + "backend/querys.php",
			cache: false,
			type: 'POST',
			data: dataSend,
			success: function (response) {
				if (response.idBeneficiario !== undefined){
					session.beneficiarios[session.beneficiarios.length - 1].id = response.idBeneficiario;
					goStep(6);
				} else {
					toastr.error(response.msg);
				}

			},
			error: function (request, status, error) {
				console.log(error);
				toastr.error('Ha ocurrido un error!');
			}
		});
	});

	$(document).on("click", "#btnUpdateBenef", function () {
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
			url: relativePath + "backend/querys.php",
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
				goStep(6);
			},
			error: function (request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	});


	$(document).on("click", "#btnNewBenef", function () {		
		goStep("5-2");
	});


	$(document).on("click", "#btnStep6", function () {
		let porcentajes = 0,
			pBeneficiarios = [];

		$("input[name='porcentaje[]']").each((i, porcentaje) => {
			 porcentajes += parseInt(porcentaje.value);
			pBeneficiarios.push({idBeneficiario: porcentaje.getAttribute("data-idb"), porcentaje: porcentaje.value});
		});

		if (porcentajes === 100) {
			updatePercentage(pBeneficiarios, function (response) {
				if (response.status){
					goStep(7);
				} else {
					toastr.error(response.msg);
					return false;
				}
			});

		} else {
			toastr.error("La suma de los porcentajes debe ser 100");
			$('input[name=porcentaje]').focus();
			return false;
		}


	});

	$(document).on("click", "#btnStep7", function () {
		goStep(8);
	});


	$(document).on("click", "#btnStep8", function () {
		let numeroTarjeta = $('#frmCard input[name=numeroTarjeta]').val().trim();
		let condiciones = $('#frmCard input[name=condiciones]').is(':checked');
		let envio = $('#frmCard input[name=envio]').is(':checked');

		$('#frmErrMsg8').hide();

		if (numeroTarjeta == '') {
			toastr.error("Escriba un número de tarjeta válida.");
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
			url: relativePath + "backend/querys.php",
			cache: false,
			type: 'POST',
			dataType: "JSON",
			data: {
				action: 'verifyCard',
				idCliente: session.id_cliente,
				numeroTarjeta: numeroTarjeta,
				clientType: session.cliente.clientType
			},
			beforeSend: function () {
				$("#loading").show();
			},
			success: function (data) {
				console.log(data.mensaje);
				$("#loading").hide();
				if(data.code === 200){
					goStep("final");
				}else{
					toastr.error(data.msg);
					$('input[name=numeroTarjeta]').focus();
					return false;
				}				
			},
			error: function (request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	});

	$(document).on("keyup", "#cardChange", function (e) {
		e.preventDefault();

		const card = $(this).val();

		if (card.length === 1 || session.card.length > 16)
			session.card = '';

		if (!isNaN(card))
			session.card = card;
		else if (isNaN(card) && session.card.length <= 16)
			session.card += card.slice(-1);

		let last = card.slice(-4);
		$(this).val(last.padStart(card.length, "*"));

	    if (session.card.length === 16 && !isNaN(session.card))
			$("input[name=numeroTarjeta]").val(session.card);
	});

	$(document).on("keypress", ".onlyNumbers", function(e) {
		return onlyNumbers(e);
	} );


});


function checkAsistencias(nodo) {

	$('#tblStep2 .tbl__note').show();
	let checked = nodo.is(':checked');
	let id = nodo.val();
	let costo = nodo.attr('data-costo');
	let total = $('#tblStep2Total').attr('data-total');
	var totalChecked = $('#tblAsistencias').find('input[name="asistencia[]"]:checked').length;

	if (checked) {
		total = parseFloat(total) + parseFloat(costo);
		$('#tblStep2 .a' + id).css('display', 'flex');
	} else {
		total = parseFloat(total) - parseFloat(costo);
		$('#tblStep2 .a' + id).hide();
	}
	session.pagoTotalMensual = total;

	$('#tblStep2Total').attr('data-total', total);
	let totalFormat = total;

	$('#tblStep2Total .subtotal2').html('$' + totalFormat.toFixed(2) + ' MXN');
	$('input[name=subtotal_mensual_asistencia]').val(totalFormat.toFixed(2));
	if (totalChecked) {
		$('#tblStep2 .tbl__note').show();
		$('#tblStep2Total .subtotal').text('Total mensual a pagar del seguro + asistencias:');
	} else {
		$('#tblStep2 .tbl__note').hide();
		$('#tblStep2Total .subtotal').text('Total mensual a pagar del seguro:');
	}

}

function goStep(step, stepActual = null) {

	if (session.cliente.id_prima === 0 && step === 6 && stepActual === 7)
		step = 3;

	$("#loading").show();
	$.ajax({
		url: relativePath + "components/step_" + step + ".php",
		type: 'POST',
		beforeSend: function () {
			$("#loading").show();
		},
		success: function (data) {
			$("#loading").hide();
			session.step = step;
			$("#main-content").html(data);
			saveDataSession();
			loadValues(step);
		},
		error: function (request, status, error) {
			$("#loading").hide();
			console.error(error);
			toastr.error('Ha ocurrido un error al cargar el paso '+ step +'!');
		}
	});
}

function loadValues(step) {
	step = step === "5-2" || step === "final" ? step : parseInt(step);
	switch (step) {
		case 1:
				if (session.cliente.id_prima !== 0) {
					if (app == "ap") {
						$("input[name=seguro]").each(function() {
							if ($(this).val() == session.cliente.id_prima){
								$(this).attr("checked", true);
								addSeguro($(this));
							}
						});

					} else {
						$('input[name=fechaNac]').val(session.cliente.date_birth);
						$('select[name=sexo]').val(session.cliente.sexo);
						$("#sexo").change();
					}

				}
			break;
		case 2:
			$("#step2SumaAsegurada").text(formatCurrency(session.seguro.sumaAsegurada, 2) + " MXN");
			$("#step2PagoAnual").text(formatCurrency(session.seguro.pagoAnual, 2) + " MXN");
			$(".subtotal2").text(formatCurrency(session.seguro.pagoMensual, 2) + " MXN");
			$("#tblStep2Total").attr("data-total", session.seguro.pagoMensual);

			if(session.cliente.id_prima == 0){
				$('#resumen_seguro').hide();
				$('#line_seguro').hide();
			}

			if (session.asistencias.length !== 0) {

				session.asistencias.forEach(function (saveAsis) {

					$(".chkbox2").each(function () {
						const asistencia = $(this);

						if (asistencia.val() == saveAsis) {
							asistencia.attr("checked", true);
							checkAsistencias(asistencia);
							return false;
						}

					});
				});
			}

			$("input[name=clienteHsbc]").attr("checked", session.check.clienteHsbc);
			$("input[name=avisoHsbc]").attr("checked", session.check.avisoHsbc);
			$("input[name=residenteHsbc]").attr("checked", session.check.residenteHsbc);

			break;
		case 3:
			if (session.id_cliente !== 0){
			  $('#frmRegister3 input[name=nombre]').val(session.cliente.name);
			  $('#frmRegister3 input[name=segundoNombre]').val(session.cliente.middle_name);
			  $('#frmRegister3 input[name=apellidoPaterno]').val(session.cliente.pater_surname);
			  $('#frmRegister3 input[name=apellidoMaterno]').val(session.cliente.mater_surname);
			  $('#frmRegister3 input[name=fechaNac]').val(session.cliente.date_birth);
			  $('#frmRegister3 input[name=rfc]').val(session.cliente.rfc);
			  $('#frmRegister3 input[name=email]').val(session.cliente.email);
			  $('#frmRegister3 input[name=telefono]').val(session.cliente.cell_phone);
			}
			break;
		case 4:
			setTimer();
			break;
		case 5:
			break;
		case "5-2":
			$('select[name=nacionalidad], select[name=actividad], select[name=residencia]').select2();
			break;
		case 6:
			getBeneficiaries();
			break;
		case 7:
			getResumSol();
			$(".subtotal2").text(formatCurrency(session.pagoTotalMensual, 2) + " MXN");
			if (session.cliente.id_prima === 0){
				$(".section-ben, .icon-seguro-del, .icon-seguro-edit").hide();
				$(".icon-seguro-add").show();
			}
			else {
				$(".section-ben, .icon-seguro-del, .icon-seguro-edit").show();
				$(".icon-seguro-add").hide();
			}

			if (session.asistencias.length === 0){
				$(".icon-asistencias-del, .icon-asistencias-edit").hide();
				$(".icon-asistencias-add").show();
			}
			else {
				$(".icon-asistencias-del, .icon-asistencias-edit").show();
				$(".icon-asistencias-add").hide();
			}
			break;
		case "final":
			localStorage.clear();
			break;
	}
}
function goStepSave() {
	if (localStorage.getItem("saveData")){
		session = JSON.parse(localStorage.getItem("saveData"));
		goStep(session.step);
	} else
		$("#loading").hide();
}
function addSeguro(nodo) {

	let sumaAsegurada = nodo.attr('data-suma-asegurada');
	let idPrima = nodo.attr('data-id-prima');
	let sumaAseguradaFormat = formatCurrency(sumaAsegurada);

	$('#sumaAsegurada').html(sumaAseguradaFormat + ' MXN');

	let pagoMensual = nodo.attr('data-pago-mensual');
	let pagoMensualFormat = pagoMensual;

	$('#pagoMensual').html('$' + pagoMensualFormat + ' MXN');

	let totalAnual = pagoMensual * 12;
	let totalAnualFormat = formatCurrency(totalAnual, 2);

	$('#totalAnual').html(totalAnualFormat + ' MXN');

	session.seguro.sumaAsegurada = sumaAsegurada;
	session.seguro.pagoAnual = totalAnual;
	session.seguro.pagoMensual = session.pagoTotalMensual = pagoMensual;
	session.cliente.id_prima = idPrima;

	$('#resumenStep1').show();
	$('#tblStep1').show();
	$('.separator__line.s1').show();
}

function saveDataSession() {
	localStorage.setItem("saveData", JSON.stringify(session));
}
function verifyCode(idCliente, code, rollback) {
	$.ajax({
		url: relativePath + "backend/querys.php",
		cache: false,
		type: 'POST',
		dataType: 'JSON',
		beforeSend: function () {
			$("#loading").show();
		},
		data: {
			action: 'verifyCode',
			idCliente: idCliente,
			code: code
		},
		complete: function () {
			$("#loading").hide();
		},
		success: function (response) {
			rollback(response.isValid);
		},
		error: function (request, status, error) {
			console.error(error);
			toastr.error("Error inesperado al verificar código, intente nuevamente por favor");
			rollback(false);
		}
	});
}

function updatePercentage(pBeneficiarios, rollback) {
	$.ajax({
		url: relativePath + "backend/querys.php",
		cache: false,
		type: 'POST',
		dataType: 'JSON',
		beforeSend: function () {
			$("#loading").show();
		},
		data: {
			action: 'updatePercentage',
			pBeneficiarios: pBeneficiarios
		},
		complete: function () {
			$("#loading").hide();
		},
		success: function (response) {
			rollback(response);
		},
		error: function (request, status, error) {
			console.error(error);
			toastr.error("Error inesperado al guardar porcentajes, intente nuevamente por favor");
			rollback(false);
		}
	});
}

function sendNewCode(idCliente) {
	$.ajax({
		url: relativePath + "backend/querys.php",
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

	$.ajax({
		url: relativePath + "components/edit_beneficiare.php",
		cache: false,
		type: 'POST',
		data: {
			idCliente: session.id_cliente,
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
	let resp = confirm('¿Está seguro de eliminar este beneficiario?');

	if (resp) {
		$.ajax({
			url: relativePath + "backend/querys.php",
			cache: false,
			type: 'POST',
			data: {
				action: 'deleteBeneficiare',
				idBeneficiario: idBeneficiario
			},
			success: function (data) {
				goStep(6);
			},
			error: function (request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	}
}

function getBeneficiaries() {
	$.ajax({
		url: relativePath + "backend/querys.php",
		type: 'POST',
		dataType: "JSON",
		beforeSend: function () {
			$("#loading").show();
		},
		data: {
			action: 'getBeneficiaries',
			idCliente: session.id_cliente,
			path: relativePath
		},
		complete: function () {
			$("#loading").hide();
		},
		success: function (response) {
			if (response.code == 200)
				$("#listBenef").html(response.data);
			else
				toastr.error(response.msg);
		},
		error: function (request, status, error) {
			console.error(error);
			toastr.error("Error inesperado al obtener beneficiarios");
		}
	});
}

function deleteSeguro() {
	const resp = confirm('¿Realmente desea eliminar el seguro?');

	if (resp) {
		$.ajax({
			url: relativePath + "backend/querys.php",
			type: 'POST',
			dataType: "JSON",
			beforeSend: function () {
				$("#loading").show();
			},
			data: {
				action: 'deleteSeguro',
				idCliente: session.id_cliente
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (response) {
				if (response.code === 200) {
					session.pagoTotalMensual = parseFloat(session.pagoTotalMensual) - parseFloat(session.seguro.pagoMensual);
					session.cliente.id_prima = session.seguro.pagoMensual = session.seguro.pagoAnual = session.seguro.sumaAsegurada = 0;
					session.cliente.sexo = '';
					const step = session.asistencias.length === 0 ? 3 : 7;
					goStep(step);
				} else
					toastr.error(response.msg);
			},
			error: function (request, status, error) {
				console.error(error);
				toastr.error("Error inesperado al intener borrar seguro, intente nuevamente por favor");
			}
		});
	}
}

function getResumSol() {
	$.ajax({
		url: relativePath + "backend/querys.php",
		type: 'POST',
		dataType: "JSON",
		beforeSend: function () {
			$("#loading").show();
		},
		data: {
			action: 'resumSoli',
			idCliente: session.id_cliente,
			app: app
		},
		complete: function () {
			$("#loading").hide();
		},
		success: function (response) {
			if (response.code == 200){
				const data = response.data.split("___")
				$("#resumSoli").html(data[0]);
				$("#resumAsitencias").html(data[1]);
				$("#resumBenef").html(data[2]);
			}
			else
				toastr.error(response.msg);
		},
		error: function (request, status, error) {
			console.error(error);
			toastr.error("Error inesperado al resumen de datos");
		}
	});
}
function emailIsValid(email) {
	return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
}

function validateString(string) {
	var letters = /^[a-zA-Za-zñáéíóúÑÁÉÍÓÚüÜ, ]+$/;
	return string.match(letters);
}

function formatCurrency(monto, decimales = 0) {

	if (decimales > 0) {
		return Intl.NumberFormat('es-MX', {
			style: 'currency',
			currency: 'MXN'
		}).format(monto);
	} else {
		return Intl.NumberFormat('es-MX', {
			style: 'currency',
			currency: 'MXN',
			minimumFractionDigits: 0,
			maximumFractionDigits: 0
		}).format(monto);
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


/* funcion para ingresar solo números en input text */

function onlyNumbers(e){
	const key = e.charCode;
	return key >= 48 && key <= 57;
}

goStepSave();