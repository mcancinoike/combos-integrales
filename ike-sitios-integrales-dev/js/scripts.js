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

	$(document).on("click", ".lightbox__close", function() {
    	$('.lightbox').hide();
    	$('.lightbox__bg').hide();
    });

	// home

	$(document).on("click", "#btnContinuar", function(e) {
		e.preventDefault();
		$.ajax({
			url: "step_1.php",
			cache: false,
			type: 'POST',
			data: {},
			beforeSend: function() {
				$("#loading").show();
			},
			complete: function() {
				$("#loading").hide();
			},
			success: function(data) {
				$("#main-content").html(data);
			},
			error: function(request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
	});

	//Step 1

	$(document).on("click", "#btnStep1", function() {
		// e.preventDefault();
		let suma_asegurada =$('input[name=suma_asegurada]').val();
		let prima_anual =$('input[name=prima_anual]').val();
		let subtotal_mensual =$('input[name=subtotal_mensual]').val();
		let idPrima =$('input[name=prima]').val();

		$.ajax({
			url: "step_2.php",
			cache: false,
			type: 'POST',
			data: {
				suma_asegurada: suma_asegurada,
				prima_anual: prima_anual,
				subtotal_mensual: subtotal_mensual,
				idPrima: idPrima
			},
			beforeSend: function() {
				$("#loading").show();
			},
			complete: function() {
				$("#loading").hide();
			},
			success: function(data) {
				$("#main-content").html(data);
			},
			error: function(request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});

		// if ($('#isBack2Edit').val() == 1) {
		// 	$('#isBack2Edit').val(0);
		// 	go2Step(7);
		// 	return false;
		// }

		
		// $('.step').hide();
		// $('#step2').show();
		// $("html, body").animate({ scrollTop: 0 }, "slow");

	});

	$(document).on('click',"input[name=seguro]", function() {
		let sumaAsegurada = $(this).attr('data-suma-asegurada');
		let idPrima = $(this).attr('data-id-prima');
		let sumaAseguradaFormat = formatCurrency(sumaAsegurada);

		$('#sumaAsegurada').html(sumaAseguradaFormat + ' MXN');

		let pagoMensual = $(this).attr('data-pago-mensual');
		let pagoMensualFormat = pagoMensual;

		$('#pagoMensual').html('$' + pagoMensualFormat + ' MXN');

		let totalAnual = pagoMensual * 12;
		let totalAnualFormat = totalAnual;

		$('#totalAnual').html('$' + totalAnualFormat + ' MXN');
		
		$('input[name=suma_asegurada]').val(sumaAsegurada);
		$('input[name=prima_anual]').val(totalAnual);
		$('input[name=subtotal_mensual]').val(pagoMensual);
		$('input[name=prima]').val(idPrima);

		$('#resumenStep1').show();
		$('#tblStep1').show();
		$('.separator__line.s1').show();
	});

	$(document).on('click','.chkbox', function() {
		$('#tblStep2').show();
	});

	//Step 2

	$(document).on("click", "#btnStep2", function() {
		let asistencias = $("input[name='asistencia[]']").map((i, asistencia) => {    if (asistencia.checked)       return asistencia.value;}).get();			
		let isChecked = $('input[name=clienteHsbc]').is(':checked');
		let isChecked2 = $('input[name=avisoHsbc]').is(':checked');
		let isChecked3 = $('input[name=residenteHsbc]').is(':checked');
		let suma_asegurada =$('input[name=suma_asegurada]').val();
		let prima_anual =$('input[name=prima_anual]').val();
		let subtotal_mensual =$('input[name=subtotal_mensual]').val();
		let subtotal_mensual_asistencia =$('input[name=subtotal_mensual_asistencia]').val();
		let idPrima =$('input[name=prima]').val();
		
		// $('#frmErrMsg2').hide();		
		if(subtotal_mensual_asistencia == "" && subtotal_mensual == "0.00"){
			toastr.error("Debe seleccionar un seguro o una asistencia para poder continuar.");
			return false;
		}

		if (isChecked && isChecked2 && isChecked3) {
			$.ajax({
				url: "step_3.php",
				cache: false,
				type: 'POST',
				data: {
					suma_asegurada: suma_asegurada,
					prima_anual: prima_anual,
					subtotal_mensual: subtotal_mensual,
					subtotal_mensual_asistencia: subtotal_mensual_asistencia,
					idPrima: idPrima,
					asistencias: asistencias
				},
				beforeSend: function() {
					$("#loading").show();
				},
				complete: function() {
					$("#loading").hide();
				},
				success: function(data) {
					$("#main-content").html(data);
				},
				error: function(request, status, error) {
					console.log('Ha ocurrido un error!');
				}
			});
			// if ($('#isBack2Edit').val() == 1) {
			// 	$('#isBack2Edit').val(0);
			// 	go2Step(7);
			// 	return false;
			// }

			// $('.step').hide();
			// $('#step3').show();
			// $("html, body").animate({ scrollTop: 0 }, "slow");
		} else {				
			if(!isChecked){
				toastr.error("Debe ser cliente HSBC para poder continuar.");
				return false;
			}	
	
			if(!isChecked2){
				toastr.error("Debe aceptar el aviso de privacidad para poder continuar.");
				return false;
			}	
	
			if(!isChecked3){
				toastr.error("Debe aceptar que se encuentra en el territorio nacional para poder continuar.");
				return false;
			}								
		}
		
	});

	$(document).on('click','.chkbox2', function() {
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
		// let totalFormat = formatCurrency(total, 2);
		let totalFormat = total;

		$('#tblStep2Total .subtotal2').html(totalFormat.toFixed(2) + ' MXN');
		$('input[name=subtotal_mensual_asistencia]').val(totalFormat.toFixed(2));
		if (totalChecked) {
			$('#tblStep2 .tbl__note').show();
			$('#tblStep2Total .subtotal').html('Total mensual a pagar del seguro + asistencias:');
		} else {
			$('#tblStep2 .tbl__note').hide();
			$('#tblStep2Total .subtotal').html('Total mensual a pagar del seguro:');
		}

	});

	//Step 3

	$(document).on("click", "#btnStep3", function() {
		let idPrima =$('input[name=prima]').val();
		let asistencias =$('input[name=asistencias]').val();
		let nombre = $('#frmRegister3 input[name=nombre]').val().trim();
		let segundoNombre = $('#frmRegister3 input[name=segundoNombre]').val().trim();
		let apellidoPaterno = $('#frmRegister3 input[name=apellidoPaterno]').val().trim();
		let apellidoMaterno = $('#frmRegister3 input[name=apellidoMaterno]').val().trim();
		let fechaNac = $('#frmRegister3 input[name=fechaNac]').val().trim();
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
			data: {
				action: 'saveClient',
				idPrima: idPrima,
				asistencias: asistencias,
				nombre: nombre,
				segundoNombre: segundoNombre,
				apellidoPaterno: apellidoPaterno,
				apellidoMaterno: apellidoMaterno,
				fechaNac: fechaNac,
				email: email,
				telefono: telefono
			},
			success: function(data) {
				console.log(data.idCliente);
				$.ajax({
					url: "step_4.php",
					cache: false,
					type: 'POST',
					data: {
						idCliente: data.idCliente
					},
					beforeSend: function() {
						$("#loading").show();
					},
					complete: function() {
						$("#loading").hide();
					},
					success: function(data) {
						$("#main-content").html(data);
					},
					error: function(request, status, error) {
						console.log('Ha ocurrido un error!');
					}
				});			
			},
			error: function(request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});

		
		// $('.step').hide();
		// $('#step4').show();
		// $("html, body").animate({ scrollTop: 0 }, "slow");
		// setTimer();
		
	});

	//Step 4

	$(document).on("click", "#btnStep4", function() {
		let codigo = $('input[name=codigoSms]').val().trim();
		let idCliente = $('input[name=idCliente]').val();

		$('#frmErrMsg4').hide();

		if (codigo != "123456") {
			toastr.error("El código es inválido");
			$('input[name=codigoSms]').focus();
			return false;
		}
		
		$.ajax({
			url: "step_5.php",
			cache: false,
			type: 'POST',
			data: {
				idCliente: idCliente
			},
			beforeSend: function() {
				$("#loading").show();
			},
			complete: function() {
				$("#loading").hide();
			},
			success: function(data) {
				$("#main-content").html(data);
			},
			error: function(request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
		// $('.step').hide();
		// $('#step5').show();
	});

	//Step 5

	$(document).on("click", "#btnStep5", function() {
		let idCliente = $('input[name=idCliente]').val();
		$.ajax({
			url: "add_beneficiare.php",
			cache: false,
			type: 'POST',
			data: {
				idCliente: idCliente
			},
			beforeSend: function() {
				$("#loading").show();
			},
			complete: function() {
				$("#loading").hide();
			},
			success: function(data) {
				$("#main-content").html(data);
			},
			error: function(request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
		// $('.step').hide();
		// $('#addBeneficiario').show();
	});

	//Agregar Beneficiario

	$(document).on("click", "#btnStepBenef", function() {
		let idCliente = $('input[name=idCliente]').val();
		let parentesco = $('#frmBeneficiario select[name=parentesco]').val();
		let nombre = $('#frmBeneficiario input[name=nombre]').val().trim();
		let segundoNombre = $('#frmBeneficiario input[name=segundoNombre]').val().trim();
		let apellidoPaterno = $('#frmBeneficiario input[name=apellidoPaterno]').val().trim();
		let apellidoMaterno = $('#frmBeneficiario input[name=apellidoMaterno]').val().trim();
		let estadoCivil = $('#frmBeneficiario select[name=estadoCivil]').val();
		let sexo = $('#frmBeneficiario select[name=sexo]').val();
		let fechaNac = $('#frmBeneficiario input[name=fechaNac]').val().trim();
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
				nacionalidad: nacionalidad,
				actividad: actividad,
				residencia: residencia
			},
			success: function(data) {
				// console.log(data);	
				$.ajax({
					url: "step_6.php",
					cache: false,
					type: 'POST',
					data: {
						idCliente: idCliente
					},
					beforeSend: function() {
						$("#loading").show();
					},
					complete: function() {
						$("#loading").hide();
					},
					success: function(data) {
						$("#main-content").html(data);
					},
					error: function(request, status, error) {
						console.log('Ha ocurrido un error!');
					}
				});		
			},
			error: function(request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});

		
		// $('.step').hide();
		// $('#step6').show();

		// $("html, body").animate({ scrollTop: 0 }, "slow");
	});

	$(document).on("click", "#btnUpdateBenef", function() {

		let parentesco = $('#frmEditBenef select[name=parentesco]').val();
		let nombre = $('#frmEditBenef input[name=nombre]').val().trim();
		let apellidoPaterno = $('#frmEditBenef input[name=apellidoPaterno]').val().trim();
		let apellidoMaterno = $('#frmEditBenef input[name=apellidoMaterno]').val().trim();
		let estadoCivil = $('#frmEditBenef select[name=estadoCivil]').val();
		let sexo = $('#frmEditBenef select[name=sexo]').val();
		let fechaNac = $('#frmEditBenef input[name=fechaNac]').val().trim();
		let nacionalidad = $('#frmEditBenef select[name=nacionalidad]').val();
		let actividad = $('#frmEditBenef select[name=actividad]').val();
		let residencia = $('#frmBeneficiario select[name=residencia]').val();

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
			url: "step_6.php",
			cache: false,
			type: 'POST',
			data: {},
			beforeSend: function() {
				$("#loading").show();
			},
			complete: function() {
				$("#loading").hide();
			},
			success: function(data) {
				$("#main-content").html(data);
			},
			error: function(request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});

		// $('.step').hide();
		// $('#step6').show();
		// $("html, body").animate({ scrollTop: 0 }, "slow");
	});


	$(document).on("click", "#btnNewBenef", function() {
		// $('.step').hide();		
		// $('#frmBeneficiario').trigger("reset");
		let idCliente = $('input[name=idCliente]').val();

		$.ajax({
			url: "add_beneficiare.php",
			cache: false,
			type: 'POST',
			data: {
				idCliente: idCliente
			},
			beforeSend: function() {
				$("#loading").show();
			},
			complete: function() {
				$("#loading").hide();
			},
			success: function(data) {
				$("#main-content").html(data);
			},
			error: function(request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
		// $('#addBeneficiario').show();
	});


	$(document).on("click", "#btnStep6", function() {

		let porcentaje = $('#listBenef input[name=porcentaje]').val().trim();

		if (porcentaje == '') {
			toastr.error("Ingrese el porcentaje");
			$('#listBenef input[name=porcentaje]').focus();
			return false;
		}
		if (porcentaje >= 0 && porcentaje <= 100) {

			$.ajax({
				url: "step_7.php",
				cache: false,
				type: 'POST',
				data: {},
				beforeSend: function() {
					$("#loading").show();
				},
				complete: function() {
					$("#loading").hide();
				},
				success: function(data) {
					$("#main-content").html(data);
				},
				error: function(request, status, error) {
					console.log('Ha ocurrido un error!');
				}
			});

			// if ($('#isBack2Edit').val() == 1) {
			// 	$('#isBack2Edit').val(0);
			// 	go2Step(7);
			// 	return false;
			// }

			// $('.step').hide();
			// $('#step7').show();
			// $("html, body").animate({ scrollTop: 0 }, "slow");
		} else {
			toastr.error("Ingrese un porcentaje entre 0 y 100");
			$('input[name=porcentaje]').focus();
			return false;
		}

		
	});

	$(document).on("click", "#btnStep7", function() {
		$.ajax({
			url: "step_8.php",
			cache: false,
			type: 'POST',
			data: {},
			beforeSend: function() {
				$("#loading").show();
			},
			complete: function() {
				$("#loading").hide();
			},
			success: function(data) {
				$("#main-content").html(data);
			},
			error: function(request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
		// $('.step').hide();
		// $('#step8').show();
		// $("html, body").animate({ scrollTop: 0 }, "slow");
	});


	$(document).on("click", "#btnStep8", function() {
		let numeroTarjeta = $('#frmCard input[name=numeroTarjeta]').val().trim();
		let condiciones = $('#frmCard input[name=condiciones]').is(':checked');
		// let aviso = $('#frmCard input[name=aviso]').is(':checked');

		$('#frmErrMsg8').hide();

		if (numeroTarjeta == '') {
			$('#frmErrMsg8').html('Escriba el número de la tarjeta').show();
			return false;
		}

		if (condiciones == '') {
			$('#frmErrMsg8').html('Debe aceptar las condiciones generales').show();
			return false;
		}

		$.ajax({
			url: "step_final.php",
			cache: false,
			type: 'POST',
			data: {},
			beforeSend: function() {
				$("#loading").show();
			},
			complete: function() {
				$("#loading").hide();
			},
			success: function(data) {
				$("#main-content").html(data);
			},
			error: function(request, status, error) {
				console.log('Ha ocurrido un error!');
			}
		});
		// if (aviso == '') {
		// 	$('#frmErrMsg8').html('Debe aceptar el aviso de privacidad').show();
		// 	return false;
		// }

		// $('.step').hide();
		// $('#ty').show();
		// $("html, body").animate({ scrollTop: 0 }, "slow");
	});


	$(document).on("keyup", "#frmCard input[name=numeroTarjeta]", function() {
		validateDataCard();
	});

	$(document).on("click", "#frmCard input[name=condiciones]", function() {
		validateDataCard();
	});


	$(document).on("click", "#frmCard input[name=aviso]", function() {
		validateDataCard();
	});


	$(document).on("click", "#frmCard input[name=envio]", function() {
		validateDataCard();
	});

});

function showAsistencia(id) {
	$('.lightbox__bg').show();
	$('#' + id).show();
}

function validateDataCard() {

	let valido = true;

	if (!$('#frmCard input[name=condiciones]').is(':checked')) {
		valido = false;
	} else if (!$('#frmCard input[name=aviso]').is(':checked')) {
		valido = false;
	} else if (!$('#frmCard input[name=envio]').is(':checked')) {
		valido = false;
	} else if ($('#frmCard input[name=numeroTarjeta]').val().trim() == '') {
		valido = false;
	}

	if (valido) {
		$('#btnStep8').removeClass('disabled');
	} else {
		$('#btnStep8').addClass('disabled');
	}

}

function editBenef(id) {

	$('.step').hide();
	$('#editBeneficiario').show();

}

function deleteBenef(id) {

	let resp = confirm('¿Está seguro de eliminar este beneficiario?');

	if(resp) {
		$('#listBenef .b' + id).remove();
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
	var x = setInterval(function() {

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
	  }
	}, 1000);

}