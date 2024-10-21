<?php 
	$suma_asegurada = ($_POST['suma_asegurada'] == "" ? "0.00" : $_POST['suma_asegurada']);
    $prima_anual = ($_POST['prima_anual'] == "" ? "0.00" : $_POST['prima_anual']);
    $subtotal_mensual = ($_POST['subtotal_mensual'] == "" ? "0.00" : $_POST['subtotal_mensual']);
	$subtotal_mensual_asistencia = ($_POST['subtotal_mensual_asistencia'] == "" ? $_POST['subtotal_mensual'] : $_POST['subtotal_mensual_asistencia']);
	$idPrima = $_POST['idPrima'];
	$asistencias = $_POST['asistencias'];
	$textAsistencia = "";
	foreach ($asistencias as $val) {
		$textAsistencia .= $val . "|";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>HSBC Seguros | Iké&#174; Asistencias</title>
	<meta name="description" content="¡Solicita tu Seguro por Accidentes Personales HSBC en minutos!">
	<link rel="shortcut icon" href="img/favicon.ico">
	<link rel="stylesheet" type="text/css" href="css/style.css?v=1.4">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">
</head>
<body>
	<input type="hidden" id="suma_asegurada" name="suma_asegurada" value="<?php echo $suma_asegurada; ?>">
	<input type="hidden" id="prima_anual" name="prima_anual" value="<?php echo $prima_anual; ?>">
	<input type="hidden" id="subtotal_mensual" name="subtotal_mensual" value="<?php echo $subtotal_mensual; ?>">
    <input type="hidden" id="subtotal_mensual_asistencia" name="subtotal_mensual_asistencia" value="<?php echo $subtotal_mensual_asistencia; ?>">
	<input type="hidden" id="prima" name="prima" value="<?php echo $idPrima; ?>">
	<input type="hidden" id="asistencias" name="asistencias" value="<?php echo $textAsistencia; ?>">
	<section class="step show" id="step3">
		<div class="header__step">
			<div class="header__step__content">
				<a href="javascript:go2Step(2);" class="header__step__arrow">
					<img src="img/icons/arrow-left.svg">
				</a>
				<div class="header__step__title">HSBC Seguros | Iké&#174; Asistencias</div>
			</div>
		</div>

		<div class="progress">
			<div>Paso 3 de 8 | Ingresa tus datos de contacto</div>
			<div class="progress__line">
				<div class="progress__done s3"></div>
			</div>
		</div>

		<div class="info">
			<div class="box">
				<div class="box__title">
					¡Queremos saber más de ti! Por favor, ingresa tus datos
				</div>
				<p class="box__txt2">Los datos marcados con * son obligatorios</p>

				<form id="frmRegister3">
					<div class="frm">
						<div class="frm__group">
							<label>Nombre*</label>
							<input type="text" name="nombre" class="frm__control" maxlength="60" value="">
						</div>
						<div class="frm__group">
							<label>Segundo nombre</label>
							<input type="text" name="segundoNombre" class="frm__control" maxlength="60" value="">
						</div>
						<div class="frm__group">
							<label>Apellido paterno*</label>
							<input type="text" name="apellidoPaterno" class="frm__control" maxlength="60" value="">
						</div>
						<div class="frm__group">
							<label>Apellido materno*</label>
							<input type="text" name="apellidoMaterno" class="frm__control" maxlength="60" value="">
						</div>
						<div class="frm__group">
							<label>Fecha de nacimiento*</label>
							<input type="date" name="fechaNac" class="frm__control" value="">
						</div>
						<div class="frm__group">
							<label>Correo electrónico*</label>
							<span class="mobile">Enviaremos la póliza de tus productos a este correo electrónico</span>
							<input type="text" name="email" class="frm__control" maxlength="160" value="">
							<span class="desktop">Enviaremos la poliza de tus productos a este correo electrónico</span>
						</div>
						<div class="frm__group">
							<label>Número celular*</label>
							<span class="mobile">Enviaremos un código de validación a este número. Por favor, ten tu dispositivo a la mano.</span>
							<input type="text" name="telefono" class="frm__control" maxlength="10" value="">
							<span class="desktop">Enviaremos un código de validación a este número. Por favor, ten tu dispositivo a la mano.</span>
						</div>
					</div>
				</form>

				<div class="separator__line s3"></div>

				<div class="frm__errmsg" id="frmErrMsg3"></div>

			</div>

			<div class="box__button stp b3">
				<button class="box__btn" id="btnStep3">Continuar</button>
				<div class="box__button__line"></div>
			</div>
		</div>
	</section>	

	<footer>
		<div class="footer__content">
			<a href="docs/tyc.pdf" target="_blank">Términos y condiciones</a>
			<a href="https://ikeasistencia.com/aviso-legal-y-de-privacidad" target="_blank">Aviso de Privacidad</a>
		</div>
	</footer>

	<script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
	<script type="text/javascript" src="js/scripts.js?v=1.2"></script>

</body>
</html>