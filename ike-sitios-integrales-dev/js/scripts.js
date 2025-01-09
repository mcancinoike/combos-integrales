import RfcFacil from "./RFC/rfc-facil";

let session = {
	cliente: {
		client_type: app,
		name: '',
		middle_name: '',
		pater_surname: '',
		mater_surname: '',
		cell_phone: 0,
		code_cell: 0,
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
	check: {
		clienteHsbc: false,
		avisoHsbc: false,
		residenteHsbc: false,
		seguro: false
	},
	card: '',
	captcha: false,
	version: version
}, minDate, maxDate;

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

	$(function () {
		var dtToday = new Date();

		var month = dtToday.getMonth() + 1; // jan=0; feb=1 .......
		var day = dtToday.getDate();
		var yearMin = dtToday.getFullYear() - 18;
		var yearMax = dtToday.getFullYear() - 65;
		if (month < 10)
			month = '0' + month.toString();
		if (day < 10)
			day = '0' + day.toString();
		minDate = yearMin + '-' + month + '-' + day;
		maxDate = yearMax + '-' + month + '-' + day;
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

	$(document).on('change', "input[name=fechaNac], .onlyLetters", function () {
		const dateBirth = new Date($("input[name=fechaNac]").val());
		dateBirth.setMinutes(dateBirth.getMinutes() + dateBirth.getTimezoneOffset());

		const day = dateBirth.getDate(),
			  month = dateBirth.getMonth() + 1,
			  year = dateBirth.getFullYear(),
		      yearNow = new Date().getFullYear(),
			  nombre = $("input[name=nombre]").val() + ' ' + $("input[name=segundoNombre]").val(),
			  paterSur = $("input[name=apellidoPaterno]").val(),
			  materSur = $("input[name=apellidoMaterno]").val();
		$("input[name=rfc]").prop("disabled", true);
		if ($("input[name=nombre]").val() !== '' && !isNaN(day) && !isNaN(month) && !isNaN(year) && year > 1900 && paterSur !== '' && materSur !== '') {

			// generar RFC

			setTimeout(function () {
				const RFC = RfcFacil.forNaturalPerson({
					name: nombre,
					firstLastName: paterSur,
					secondLastName: materSur,
					day: day,
					month: month,
					year: year
				});

				$("input[name=rfc]").val(RFC).prop("disabled", false);

			}, 500);

			// validacion para mandar modal cuando el beneficiario es menor de edad
			if ($("select[name=parentesco]").length && $(this).val() > minDate) {
				if (!$("#modalMenorEdad").length){
					$("body").append('<div class="modal" id="modalMenorEdad" tabindex="-1">\n' +
						'  <div class="modal-dialog">\n' +
						'    <div class="modal-content alert alert-warning" role="alert">\n' +
						'      <div class="modal-header">\n' +
						'        <h5 class="modal-title">Advertencia</h5>\n' +
						'        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>\n' +
						'      </div>\n' +
						'      <div class="modal-body text-justify">\n' +
						'        <p>En el caso de que se desee nombrar beneficiarios a menores de edad, no se debe señalar a un mayor de edad como representante de los menores para efecto de que, en su representación, cobre la indemnización. Lo anterior porque las legislaciones civiles previenen la forma en que deben designarse tutores, albaceas, representantes de herederos u otros cargos similares y no consideran al Contrato de Seguro como el instrumento adecuado para tales designaciones. La designación que se hiciera de un mayor de edad como representante de menores beneficiarios, durante la minoría de edad de ellos, legalmente puede implicar que se nombra beneficiario al mayor de edad, quien en todo caso solo tendría una obligación moral, pues la designación que se hace de beneficiarios en un Contrato de Seguro le concede el derecho incondicionado de disponer de la Suma Asegurada.</p>\n' +
						'      </div>\n' +
						'      <div class="modal-footer">\n' +
						'        <button type="button" class="btn btn-danger bg-red"  data-bs-dismiss="modal">Aceptar</button>\n' +
						'      </div>\n' +
						'    </div>\n' +
						'  </div>\n' +
						'</div>');
				}
				$("#modalMenorEdad").modal("show");
			}

		}

		// validacion paso uno AH
		if ($('select[name=sexo]').val() !== '' && session.step === 1){
			deleteSeguro();
			$("#sexo").change();
		}

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
				$("#sumaAseguradaS1").val('');
				$("#resumenStep1").hide();

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
					$("#ajaxSumaAsegurada").html(DOMPurify.sanitize(response.data));
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
		$("#delSeguro").parent().show();

		if ($(this).val() !== '') {
			$("#delSeguro").prop("checked", false);
			session.check.seguro = false;
			$("#btnStep1").show();
			addSeguro($("option:selected", this));
		} else {
			$("#btnStep1").hide();
			$('#resumenStep1').hide();
			$('#tblStep1').hide();
			$('.separator__line.s1').hide();
		}
	});

	// evneto exclusivo ap

	$(document).on('click', "input[name=seguro]", function () {

		if ($(this).attr("data-id-prima") != session.cliente.id_prima)
			addSeguro($(this));
		else {
			deleteSeguro();
		}
	});

	//Step 1

	$(document).on("click", "#btnStep1", function (e) {
		e.preventDefault();
		goStep(2);
	});



	//Step 2

	$(document).on('click', '.chkbox2', function () {

		if ($(this).is(":checked")){
			//if (session.asistencias.find((asistencia) => asistencia == $(this).val()) === undefined)
				session.asistencias.push({
					                      id_assistance: parseInt($(this).val()),
										  name: $(this).parent().find(".tbl__asistencia h3").text(),
										  price: parseFloat($(this).attr("data-costo"))
										 });
		} else
			session.asistencias = session.asistencias.filter(asistencia => asistencia.id_assistance != $(this).val());
		checkAsistencias($(this));
		saveDataSession();
	});

	$(document).on("click", ".asistencia .more", function () {
		showAsistencia($(this).attr("data-modal"));
	});

	$(document).on("click", 'input[name=clienteHsbc]', function() {
		session.check.clienteHsbc = $(this).is(":checked");
	});

	$(document).on("click", 'input[name=residenteHsbc]', function() {
		session.check.residenteHsbc = $(this).is(":checked");
	});

	$(document).on("click", 'input[name=avisoHsbc]', function() {
		session.check.avisoHsbc = $(this).is(":checked");
	});

	$(document).on("click", "#btnStep2", function () {
/*		$("input[name='asistencia[]']").each((i, asistencia) => {
			if (asistencia.checked)
				session.asistencias[i].id_assistance = parseInt(asistencia.value);
		}).get();*/

		const captcha_response = session.captcha ? '' : $(".g-recaptcha-response").last().val();

		session.check.clienteHsbc = $('input[name=clienteHsbc]').is(':checked');
		session.check.avisoHsbc = $('input[name=avisoHsbc]').is(':checked');
		session.check.residenteHsbc = $('input[name=residenteHsbc]').is(':checked');

		if (session.pagoTotalMensual == 0) {
			toastr.error("Debe seleccionar un Seguro o una Asistencia para poder continuar.");
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

		$('.frm__group').find("small").remove();
		$('input, select').removeClass("border-red");

		session.cliente.name = $('#frmRegister3 input[name=nombre]').val().trim();
		session.cliente.middle_name = $('#frmRegister3 input[name=segundoNombre]').val().trim();
		session.cliente.pater_surname = $('#frmRegister3 input[name=apellidoPaterno]').val().trim();
		session.cliente.mater_surname = $('#frmRegister3 input[name=apellidoMaterno]').val().trim();
		session.cliente.date_birth = $('#frmRegister3 input[name=fechaNac]').val().trim();
		session.cliente.rfc = $('#frmRegister3 input[name=rfc]').val().trim();
		session.cliente.email = $('#frmRegister3 input[name=email]').val().trim();
		session.cliente.cell_phone = $('#frmRegister3 input[name=telefono]').val().trim();

		$('#frmErrMsg3').hide();

		let msg = [], nodo = [];

		if (session.cliente.name === '') {
			msg.push("Escribe tu nombre.");
			nodo.push($('input[name=nombre]'));
		}

		if (session.cliente.pater_surname === '') {
			msg.push("Escribe tu apellido paterno.");
			nodo.push($('input[name=apellidoPaterno]'));
		}

		if (session.cliente.mater_surname === '') {
			msg.push("Escribe tu apellido materno.");
			nodo.push($('input[name=apellidoMaterno]'));
		}

		if (session.cliente.date_birth > minDate || session.cliente.date_birth < maxDate) {
			msg.push("Debe ser mayor de 18 años y menor de 65 años.");
			nodo.push($('input[name=fechaNac]'));
		}

		if (session.cliente.date_birth === '') {
			msg.push("Selecciona la fecha de tu nacimiento.");
			nodo.push($('input[name=fechaNac]'));
		}

		if (session.cliente.rfc.length < 10) {
			msg.push("El RFC debe tener entre 10 y 13 caracteres.");
			nodo.push($('input[name=rfc]'));
		}

		if (!validRFC(session.cliente.rfc)) {
			msg.push("Verifica que tu RFC esté escrito correctamente.");
			nodo.push($('input[name=rfc]'));
		}

		if (session.cliente.rfc === '') {
			msg.push("Escribe el RFC.");
			nodo.push($('input[name=rfc]'));
		}

		if (!emailIsValid(session.cliente.email)) {
			msg.push("Escribe un correo válido.");
			nodo.push($('input[name=email]'));
		}

		if (session.cliente.email === '') {
			msg.push("Escribe tu correo electrónico.");
			nodo.push($('input[name=email]'));
		}

		if (session.cliente.cell_phone === '') {
			msg.push("Escribe tu teléfono.");
			nodo.push($('input[name=telefono]'));
		}

		if (session.cliente.cell_phone.length < 10) {
			msg.push("El teléfono tiene que tener 10 dígitos.");
			nodo.push($('input[name=telefono]'));
		}

		if (msg.length !== 0) {
			for (let i = (msg.length -1); i >= 0; i--)
				nodo[i].focus().addClass("border-red").after('<small class="text-danger">' + msg[i] + '</small>');
			return false;
		}

		const step = session.cliente.confirm_cell === 0 ? 4 :
							  session.beneficiarios.length === 0 && session.cliente.id_prima !== 0 ? 5 :
							  session.beneficiarios.length !== 0 && session.cliente.id_prima !== 0 ? 6 : 7;

		goStep(step);

	});

	//Step 4

	$(document).on("click", "#btnStep4", function () {
		const codigo = $('input[name=codigoSms]').val().trim(),
		 		step = session.cliente.id_prima === 0 ? '7' : '5';

		$('#frmErrMsg4').hide();

		if (session.cliente.confirm_cell === 1)
			goStep(step);
		else {
				if (codigo === session.cliente.code_cell){
					session.cliente.confirm_cell = 1;
					goStep(step);
				} else {
					toastr.error("El código es inválido");
					$('input[name=codigoSms]').focus();
				}
		}
	});

	$(document).on("click", "#sendNewCode", function () {
		sendCode();
	});
	//Step 5

	$(document).on("click", "#btnStep5", function () {
		goStep("5-2");
	});

	//Agregar Beneficiario

	$(document).on("click", "#btnStepBenef", function () {
		addUpdateBeneficiary();
	});

	$(document).on("click", "#btnUpdateBenef", function () {
		addUpdateBeneficiary(parseInt($(this).attr("data-preId")));
	});

	$(document).on("click", ".btn-e-ben", function () {
		editBenef(parseInt($(this).attr("data-id")));
	});

	$(document).on("click", ".btn-del-ben", function () {
		deleteBenef($(this).attr("data-id"));
	});

	$(document).on("click", "#btnNewBenef", function () {
		goStep("5-2");
	});


	$(document).on("click", "#btnStep6", function () {
		let porcentajes = 0;

		$("input[name='porcentaje[]']").each((i, porcentaje) => {
			 porcentajes += parseInt(porcentaje.value);
		});

		if (porcentajes === 100)
			 goStep(7);
		else {
			toastr.error("La suma de los porcentajes debe ser 100");
			$('input[name=porcentaje]').focus();
			return false;
		}

	});

	$(document).on("click", "#btnStep7", function () {
		goStep(8);
	});

	$(document).on("click", ".gostep-0", function () {
		session.step = 0;
		saveDataSession();
		location.reload();
	});

	$(document).on("click", ".gostep", function () {
		const step = parseInt($(this).attr("data-step"));
		goStep(step, $(this).hasClass("back") !== undefined ? 7 : null);
	});

	$(document).on("keyup", "input[name='porcentaje[]']", function () {
		session.beneficiarios[parseInt($(this).attr("data-idb")) - 1].percentage = parseInt($(this).val());
		saveDataSession();
	});

	$(document).on("click", ".del-seguro", function () {
		const resp = confirm('¿Realmente desea eliminar el Seguro?');
		if (resp) {
			deleteSeguro();
			const step = session.asistencias.length === 0 ? 2 : 7;
			goStep(step);
		}
	});

	$(document).on("click", "#delSeguro", function () {
		$(this).parent().attr("style", "display: none !important");
		deleteSeguro();
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

		session.card = numeroTarjeta;
		$.ajax({
			url: relativePath + "backend/querys.php",
			cache: false,
			type: 'POST',
			dataType: "JSON",
			data: {
				action: 'insertAllData',
				allData: session
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

	$(document).on("click", "#eye", function () {
		if ($(this).attr("data-type") === "off") {
			$(this).attr("data-type", "on");
			$(this).attr("src", relativePath + "img/icons/eye.svg");
			$("#card").removeClass("text-security-on").addClass("text-security-off");
		} else {
			$(this).attr("data-type", "off");
			$(this).attr("src", relativePath + "img/icons/eye-off.svg");
			$("#card").removeClass("text-security-off").addClass("text-security-on");
		}
	})

	$(document).on("keyup", ".onlyNumbers", function(e) {
		$(this).val(filterNumbers($(this).val()));
	} );

	$(document).on("keyup", ".onlyLetters", function(e) {
			$(this).val(filterLetters($(this).val()));
	} );

	$(document).on("change", ".onlyLetters", function() {
		$(".onlyLetters").each(function () {
			$(this).val(delTildesEspeciales($(this).val()));
		});
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
		$('#tblStep2Total .subtotal').text('Total mensual a pagar del Seguro + Asistencias:');
	} else {
		$('#tblStep2 .tbl__note').hide();
		$('#tblStep2Total .subtotal').text('Total mensual a pagar del Seguro:');
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
			if (stepActual !== -1)
				window.history.pushState(null, '', "?step=" + step);

			$("#loading").hide();
			session.step = step;
			$("#main-content").html(DOMPurify.sanitize(data, { ADD_ATTR: ['target'] }));
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

				} else
					$("#delSeguro").prop("checked", session.check.seguro);

			break;
		case 2:
			$("#main-content").append('<script src="https://www.google.com/recaptcha/api.js" async defer></script>');
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

						if (asistencia.val() == saveAsis.id_assistance) {
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

			if (session.cliente.rfc !== ''){
			  $('#frmRegister3 input[name=nombre]').val(session.cliente.name);
			  $('#frmRegister3 input[name=segundoNombre]').val(session.cliente.middle_name);
			  $('#frmRegister3 input[name=apellidoPaterno]').val(session.cliente.pater_surname);
			  $('#frmRegister3 input[name=apellidoMaterno]').val(session.cliente.mater_surname);
			  $('#frmRegister3 input[name=fechaNac]').val(session.cliente.date_birth);
			  $('#frmRegister3 input[name=rfc]').val(session.cliente.rfc);
			  $('#frmRegister3 input[name=email]').val(session.cliente.email);
			  $('#frmRegister3 input[name=telefono]').val(session.cliente.cell_phone);

		    if (session.cliente.confirm_cell === 1)
			  $('#frmRegister3 input[name=telefono]').prop("disabled", true);

		    if (app === "ah" && session.seguro.pagoMensual !== 0)
			  $('#frmRegister3 input[name=fechaNac]').prop("disabled", true);

			} else if (session.cliente.date_birth !== '' && session.seguro.pagoMensual !== 0) { // Seguro AH
				$('#frmRegister3 input[name=fechaNac]').val(session.cliente.date_birth).prop("disabled", true);
			}
			break;
		case 4:
			sendCode();
			break;
		case 5:
			break;
		case "5-2":
			$('select[name=nacionalidad], select[name=actividad], select[name=residencia]').select2();
			break;
		case 6:
			if (session.beneficiarios.length === 5)
				$("#btnNewBenef").hide();
			else
				$("#btnNewBenef").show();

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
		case 8:
			break;
		case "final":
			let msgHead = '',
				msgSeguro = '';

			if (session.cliente.id_prima === 0) {
				msgHead = "<h3>¡Felicidades! Ahora cuentas con tu(s) programa(s) de Asistencia y podrás hacer uso de tus beneficios en un lapso de 48 horas.</h3>";

			} else {
				msgHead = "<h2>¡Solicitud enviada con éxito!</h2>";

				msgSeguro = "                <ul>\n" +
							"                    <li>Tu solicitud de contratación de Seguro se encuentra en evaluación.</li>\n" +
							"                    <li>De ser aceptada, haremos el cargo a la cuenta que capturaste y enviaremos tu póliza al correo registrado en un lapso de 48 horas.</li>\n" +
							"                    <li>Deberás recibir la póliza de confirmación en tu correo electrónico. Si no la recibes, llama al <a href='tel:5557213322'>55 5721 3322</a> para confirmar la contratación.</li>\n";


				if (session.asistencias.length !== 0)
					msgSeguro += "                    <li>Podrás hacer uso de tu(s) programa(s) de Asistencia en un lapso de 48 hrs.</li>\n";

					msgSeguro += "                </ul>";
			}
			$("#msg-head").html(msgHead);
			$("#msg-seguro").html(msgSeguro);

			localStorage.clear();
			break;
	}
}
function goStepSave() {
	if (localStorage.getItem("saveData")){
		const sessionSave = JSON.parse(localStorage.getItem("saveData"));

		if (session.cliente.client_type === sessionSave.cliente.client_type && sessionSave.version === session.version){
			session = sessionSave;
			if (sessionSave.step !== 0)
				goStep(session.step);
			else
				$("#loading").hide();

		} else
			$("#loading").hide();
	} else
		$("#loading").hide();
}
function addSeguro(nodo) {

	let sumaAsegurada = nodo.attr('data-suma-asegurada');
	let idPrima = nodo.attr('data-id-prima');
	let sumaAseguradaFormat = formatCurrency(sumaAsegurada, 2);

	$('#sumaAsegurada').html(sumaAseguradaFormat + ' MXN');

	let pagoMensual = nodo.attr('data-pago-mensual');
	let pagoMensualFormat = pagoMensual;

	$('#pagoMensual').html('$' + pagoMensualFormat + ' MXN');

	let totalAnual = nodo.attr("data-prima-anual") === undefined ? Math.round(pagoMensual * 12) : parseInt(nodo.attr("data-prima-anual"));
	let totalAnualFormat = formatCurrency(totalAnual, 2);

	$('#totalAnual').html(totalAnualFormat + ' MXN');

	session.seguro.sumaAsegurada = sumaAsegurada;
	session.seguro.pagoAnual = totalAnual;
	session.seguro.pagoMensual = session.pagoTotalMensual = parseFloat(pagoMensual);
	session.cliente.id_prima = parseInt(idPrima);

	$('#resumenStep1').show();
	$('#tblStep1').show();
	$('.separator__line.s1').show();
}

function saveDataSession() {
	localStorage.setItem("saveData", JSON.stringify(session));
}
function sendCode() {
	$.ajax({
			url: relativePath + "backend/querys.php",
			cache: false,
			type: 'POST',
			dataType: 'JSON',
			beforeSend: function () {
				$("#loading").show();
			},
			data: {
				action: 'sendCode',
				cell_phone: session.cliente.cell_phone
			},
			complete: function () {
				$("#loading").hide();
			},
			success: function (response) {
				if (response.code === 200) {
					$('#sendNewCode').hide();
					$('#btnStep4').show();
					session.cliente.code_cell = response.codeCell;
					setTimer();
				} else {
					toastr.error(response.msg);
				}
			},
			error: function (request, status, error) {
				console.error(error);
				toastr.error("Error inesperado al enviar código, intente nuevamente por favor");
			}
	 });
}

function showAsistencia(id) {
	$('.lightbox__bg').show();
	$('#' + id).show();
}

function editBenef(preId) {

	$.ajax({
		url: relativePath + "components/edit_beneficiare.php",
		cache: false,
		type: 'POST',
		beforeSend: function () {
			$("#loading").show();
		},
		complete: function () {
			$("#loading").hide();
		},
		success: function (data) {
			$("#main-content").html(DOMPurify.sanitize(data));

			const ben = session.beneficiarios.filter(benef => benef.preId === preId)[0];

			$("select[name=parentesco]").val(ben.relationship);
			$("input[name=nombre]").val(ben.name);
			$("input[name=segundoNombre]").val(ben.middle_name);
			$("input[name=apellidoPaterno]").val(ben.pater_surname);
			$("input[name=apellidoMaterno]").val(ben.mater_surname);
			$("select[name=estadoCivil]").val(ben.marital_status);
			$("select[name=sexo]").val(ben.sex);
			$("input[name=fechaNac]").val(ben.date_birth);
			$("input[name=rfc]").val(ben.rfc);
			$("select[name=nacionalidad]").val(ben.nationality);
			$("select[name=actividad]").val(ben.economic_activity);
			$("select[name=residencia]").val(ben.residence);
			$("#btnUpdateBenef").attr("data-preId", preId);

		},
		error: function (request, status, error) {
			console.log('Ha ocurrido un error!');
		}
	});
}

function deleteBenef(preId) {

	let resp = confirm('¿Está seguro de eliminar este beneficiario?');
	if (resp) {
		session.beneficiarios = session.beneficiarios.filter((data) => {return data.preId !== parseInt(preId)});
		goStep(6);
	}
}

function getBeneficiaries() {
	let beneficiarios= '';

	for (let i = 0; i < session.beneficiarios.length; i++) {
		const ben = session.beneficiarios[i];
		session.beneficiarios[i].preId = (i + 1);

		beneficiarios += '<div class="box__row b1">' +
			             '  <div class="box__info"><div class="box__name">' + ben.name + ' ' + ben.middle_name+ ' ' + ben.pater_surname + ' ' + ben.mater_surname + '</div>' +
						 '		<div class="box__action">' +
						 '			<a class="cursor-pointer btn-e-ben" data-id="' + (i + 1) + '"><img src="'+ relativePath +'img/icons/edit.svg"></a>' +
		                 '			<a class="cursor-pointer btn-del-ben" data-id="' +  (i + 1) + '"><img src="'+ relativePath +'img/icons/delete.svg"></a>' +
						 '		</div></div>' +
						 '<div class="box__percentage">Porcentaje' +
						 '<div class="box__percentage__input"><input type="text" data-idb="' +  (i + 1) + '" value="' + ben.percentage + '" class="onlyNumbers" maxlength="3" name="porcentaje[]"> %' +
						 '</div></div></div><br>';
	}

    $("#listBenef").html(beneficiarios);
}

function deleteSeguro() {
	session.pagoTotalMensual = parseFloat(session.pagoTotalMensual) - parseFloat(session.seguro.pagoMensual);
	session.cliente.id_prima = session.seguro.pagoMensual = session.seguro.pagoAnual = session.seguro.sumaAsegurada = 0;
	session.cliente.sexo = '';
	session.beneficiarios = [];
	$("input[name=seguro]").prop("checked", false);
	$("input[name=fechaNac]").val('');
	$("#sexo").val('');
	$("#sumaAseguradaS1").val('');
	$('#resumenStep1').hide();
	$('#tblStep1').hide();
	$('.separator__line.s1').hide();
	session.check.seguro = true;
	$("#ajaxSumaAsegurada").empty();
}

function getResumSol() {

	let seguro = '', asistencias = '', beneficiarios = '', count = 0;

		seguro = '<div class="tbl">' +
				 '	<div class="tbl__body fs-13px">' +
				 '		<div class="tbl__row">' +
				 '			<div class="tbl__col left">' +
				 '				Suma asegurada:<br>' + formatCurrency(session.seguro.sumaAsegurada, 2) + ' MXN' +
				 '			</div>';

		if (app === "ap")
			seguro += '		<div class="tbl__col right">Prima anual:<br>' + formatCurrency(session.seguro.pagoAnual, 2) + ' MXN';

		seguro += '</div></div><div class="tbl__row">' +
					'<div class="tbl__col left font-family-univers">Subtotal mensual a pagar</div>' +
					'<div class="tbl__col right font-family-univers">' + formatCurrency(session.seguro.pagoMensual, 2) + ' MXN</div>' +
					'</div></div></div>';



	let price = 0;
	asistencias = '<div class="tbl"><div class="tbl__body fs-13px">';
	for (let i in session.asistencias) {
		price += session.asistencias[i].price;
		asistencias += '<div class="tbl__row">' +
						'<div class="tbl__col left">' + session.asistencias[i].name + '</div>' +
						'<div class="tbl__col right">+$' + session.asistencias[i].price + ' MXN</div></div>';

	}

	asistencias += '<div class="tbl__row"><div class="tbl__col left font-family-univers">Subtotal mensual a pagar</div>' +
					'<div class="tbl__col right font-family-univers">' + formatCurrency(price, 2)+ ' MXN</div></div>' +
					'<div class="tbl__note"><img src="' + relativePath+ 'img/icons/info.svg" class="info__icon">Tu primer mes de Asistencias no tiene costo.</div>' +
					'</div></div>';



	for (let i = 0; i < session.beneficiarios.length; i++) {
		const ben = session.beneficiarios[i];

		beneficiarios += '<div class="tbl"><div class="tbl__body">' +
						'<div class="tbl__row"><div class="tbl__col left full">' +
						'<img src="' + relativePath + 'img/icons/person2.svg">' +
						'<p> ' + ben.name + ' ' + ben.middle_name+ ' ' + ben.pater_surname + ' ' + ben.mater_surname +
						'<br><span class="percentage">' + ben.percentage + '%</span></p>';
						'</div></div></div></div>';
	}

	$("#resumSoli").html(seguro);
	$("#resumAsitencias").html(asistencias);
	$("#resumBenef").html(beneficiarios);

}
function emailIsValid(email) {
	return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
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

let timeConfirmCell;
function setTimer() {

	var countDownDate = new Date();
	countDownDate.setSeconds(countDownDate.getSeconds() + 116);
	countDownDate.getTime();
	clearInterval(timeConfirmCell);
	// Update the count down every 1 second
	timeConfirmCell = setInterval(function () {

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
			clearInterval(timeConfirmCell);
			$('#timer i').html("0:00");
			$('#sendNewCode').show();
			$('#btnStep4').hide();

		}
	}, 1000);

}

/* funcion para ingresar solo números en input text */

function onlyNumbers(e){
	return /[0-9]+|Backspace+$/i.test(e.key);
}

function onlyLetters(e) {
	return /[ A-Zñ]+$/i.test(e);//\u00C0-\u017F
	//return /^[a-zA-Z]*((?!Dead)[a-zA-Z])*[a-zA-Z]*$/i.test(e.key);
}
function delTildesEspeciales(text) {
	return text.normalize('NFD').replace(/[\u0300-\u036f]/g,"").replace(/[^a-zA-Z0-9 ]/g,"");
}

function filterNumbers(text) {
	return text.replace(/[^0-9]/g,"");
}

function filterLetters(text) {
	return text.replace(/[^a-zA-Z Ññ]/g,"").trimStart();
}

function addUpdateBeneficiary(preId = undefined) {

	$('.frm__group').find("small").remove();
	$('input, select').removeClass("border-red");

	const beneficiarios = {
		relationship: $('select[name=parentesco]').val(),
		name: $('input[name=nombre]').val().trim(),
		middle_name: $('input[name=segundoNombre]').val().trim(),
		pater_surname: $('input[name=apellidoPaterno]').val().trim(),
		mater_surname: $('input[name=apellidoMaterno]').val().trim(),
		marital_status: $('select[name=estadoCivil]').val(),
		sex: $('select[name=sexo]').val(),
		date_birth: $('input[name=fechaNac]').val().trim(),
		rfc: $('input[name=rfc]').val().trim(),
		nationality: $('select[name=nacionalidad]').val(),
		economic_activity: $('select[name=actividad]').val(),
		residence: $('select[name=residencia]').val()
	};

	let msg = [], nodo = [];

	$('#frmErrMsgBenef').hide();

	if (beneficiarios.relationship === '') {
		msg.push("Seleccione el parentesco.");
		nodo.push($('select[name=parentesco]'));
	}

	if (beneficiarios.name === '') {
		msg.push("Escribe el nombre.");
		nodo.push($('input[name=nombre]'));
	}

	if (beneficiarios.pater_surname === '') {
		msg.push("Escribe el apellido paterno.");
		nodo.push($('input[name=apellidoPaterno]'));
	}

	if (beneficiarios.mater_surname === '') {
		msg.push("Escribe el apellido materno.");
		nodo.push($('input[name=apellidoMaterno]'));
	}

	if (beneficiarios.marital_status === '') {
		msg.push("Seleccione el estado civil.");
		nodo.push($('select[name=estadoCivil]'));
	}

	if (beneficiarios.sex === '') {
		msg.push("Seleccione sexo.");
		nodo.push($('select[name=sexo]'));
	}

	if (beneficiarios.date_birth < maxDate) {
		msg.push("El beneficiario debe ser menor de 65 años.");
		nodo.push($('input[name=fechaNac]'));
	}

	if (beneficiarios.date_birth === '') {
		msg.push("Selecciona la fecha de el nacimiento.");
		nodo.push($('input[name=fechaNac]'));
	}

	if (beneficiarios.rfc.length < 10) {
		msg.push("El RFC debe tener entre 10 y 13 caracteres.");
		nodo.push($('input[name=rfc]'));
	}

	if (!validRFC(beneficiarios.rfc)) {
		msg.push("Verifica que el RFC esté escrito correctamente.");
		nodo.push($('input[name=rfc]'));
	}

	if (beneficiarios.rfc === '') {
		msg.push("Escribe el RFC.");
		nodo.push($('input[name=rfc]'));
	}


	if (beneficiarios.rfc === session.cliente.rfc) {
		msg.push("El asegurado no puede agregarse como beneficiario.");
		nodo.push($('input[name=rfc]'));
	}

	if (beneficiarios.nationality === '') {
		msg.push("Selecciona la nacionalidad.");
		nodo.push($('select[name=nacionalidad]'));
	}

	if (beneficiarios.economic_activity === '') {
		msg.push("Seleccione la actividad económica.");
		nodo.push($('select[name=actividad]'));
	}

	if (beneficiarios.residence === '') {
		msg.push("Seleccione el lugar de residencia.");
		nodo.push($('select[name=residencia]'));
	}

	if (msg.length !== 0) {
		for (let i = (msg.length -1); i >= 0; i--)
			nodo[i].focus().addClass("border-red").after('<small class="text-danger">' + msg[i] + '</small>');
		return false;
	}

	if (session.beneficiarios.length > 0) {
		let existBen = false;

		for(let i= 0; i < session.beneficiarios.length; i++) {
			if (
				(session.beneficiarios[i].rfc === beneficiarios.rfc && preId !== session.beneficiarios[i].preId) ||
				(session.beneficiarios[i].rfc === beneficiarios.rfc && preId === undefined)
			){
				toastr.error("Anteriormente ya has registrado este beneficiario");
				existBen = true;
				return false;
			}
		}

		if (existBen)
			return false;
	}

	if (preId === undefined) {
		beneficiarios.percentage = '';
		beneficiarios.preId = session.beneficiarios.length + 1;
		session.beneficiarios.push(beneficiarios);
	} else {
		beneficiarios.preId = preId;
		beneficiarios.percentage = session.beneficiarios[preId - 1].percentage;
		session.beneficiarios[preId - 1] = beneficiarios;
	}

	goStep(6);
}

function validRFC(rfc, aceptarGenerico = true) {
	const re= rfc.length === 10 ? /^([A-ZÑa-zñ&]{3,4})(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]))$/ :
			                               /^([A-ZÑa-zñ&]{3,4})(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]))([A-Za-z\d]{2})([A\d])$/,
	      validado = rfc.match(re);

	//if (!validado)  //Coincide con el formato general del regex?
		return validado;

	//Separar el dígito verificador del resto del RFC
	const digitoVerificador = validado.pop(),
		rfcSinDigito      = validado.slice(1).join(''),
		len               = rfcSinDigito.length,

		//Obtener el digito esperado
		diccionario       = "0123456789ABCDEFGHIJKLMN&OPQRSTUVWXYZ Ñ",
		indice            = len + 1;
	var   suma,
		digitoEsperado;

	if (len == 12) suma = 0
	else suma = 481; //Ajuste para persona moral

	for(let i=0; i<len; i++)
		suma += diccionario.indexOf(rfcSinDigito.charAt(i)) * (indice - i);
	digitoEsperado = 11 - suma % 11;
	if (digitoEsperado == 11) digitoEsperado = 0;
	else if (digitoEsperado == 10) digitoEsperado = "A";

	//El dígito verificador coincide con el esperado?
	// o es un RFC Genérico (ventas a público general)?
	if ((digitoVerificador != digitoEsperado)
		&& (!aceptarGenerico || rfcSinDigito + digitoVerificador != "XAXX010101000"))
		return false;
	else if (!aceptarGenerico && rfcSinDigito + digitoVerificador == "XEXX010101000")
		return false;
	return rfcSinDigito + digitoVerificador;
}

function getQueryVariable(variable) {
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0; i < vars.length; i++) {
		var pair = vars[i].split("=");
		if(pair[0] == variable) {
			return pair[1];
		}
	}
	return false;
}

window.addEventListener('popstate', function (e) {
	goStep(getQueryVariable("step"), -1);
});

goStepSave();