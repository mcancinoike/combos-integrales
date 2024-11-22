<?php
    session_start();
    $_SESSION["relativePath"] = "";
    $_SESSION["app"] = "ap";
?>
<!DOCTYPE html>
<html lang="es-Mx">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Seguro por Accidentes Personales HSBC</title>
	<meta name="description" content="¡Solicita tu Seguro por Accidentes Personales HSBC en minutos!">
	<link rel="shortcut icon" href="img/favicon.ico">
    <?php include_once 'components/css.php'; ?>
</head>
<!-- Page Header-->
<?php include 'components/header.php' ?>

<body>
	<div id="main-content">
		<section class="step show" id="home">
			<div class="header__step home line">
				<div class="header__step__content">
					<a href="javascript:;" class="header__step__arrow">
						<img src="<?php echo $_SESSION["relativePath"]?>img/icons/arrow-left.svg">
					</a>
					<div class="header__step__title">HSBC Seguros</div>
				</div>
			</div>

			<div class="atf">
				<div class="atf__picture">
					<img src="<?php echo $_SESSION["relativePath"]?>img/hsbc-seguro.png" class="mobile">
					<img src="<?php echo $_SESSION["relativePath"]?>img/hsbc-seguro-desktop.png" class="desktop">
				</div>
				<div class="atf__txt">
					<h1>¡Solicita tu Seguro por Accidentes Personales HSBC en minutos!</h1>
				</div>
			</div>

			<div class="info home">
				<div class="breadcrumb">
					Productos <img src="<?php echo $_SESSION["relativePath"]?>img/icons/arrow-red.svg"> Programa Seguros + Asistencia
				</div>
				<div class="box">
					<div class="box__title">
						<img src="<?php echo $_SESSION["relativePath"]?>img/icons/realiza.svg">
						Realiza tu solicitud y obtén:
					</div>
					<ul class="box__list">
						<li>Seguro por Accidentes Personales HSBC</li>
					</ul>
					<p class="box__txt">Te protegemos en caso de muerte accidental, pérdidas orgánicas y fractura de huesos, entre otros, con un costo mensual accesible.</p>
					<ul class="box__list">
						<li>Programa de Asistencias Iké</li>
					</ul>
					<p class="box__txt">Amplía la protección de tu seguro al contratar las Asistencias Iké que más te convengan <span class="bold">¡y llévate el primer mes sin costo!</span> Después paga solo $99.00 MXN al mes por cada asistencia que elijas.</p>
					<p class="box__txt">Comienza tu solicitud para conocer más.</p>
				</div>
				<div class="box">
					<div class="box__title">
						<img src="<?php echo $_SESSION["relativePath"]?>img/icons/haz.svg">
						Haz tu solicitud hoy mismo y obtén la mayor protección al menor precio.
					</div>
					<p class="box__txt">También podrás contratar tu Seguro por Accidentes Personales o el Programa de Asistencias Iké por separado.</p>
				</div><br>
				<button class="box__btn" name="btnContinuar" id="step0">Continuar</button>
			</div>
		</section>
	</div>
	<div id="loading">
		<img src="<?php echo $_SESSION["relativePath"]?>img/loading.svg" alt="cargando" style="width: 150px">
	</div>
	<footer>
		<div class="footer__content">
			<a href="docs/tyc.pdf" target="_blank">Términos y condiciones</a>
			<a href="https://ikeasistencia.com/aviso-legal-y-de-privacidad" target="_blank">Aviso de Privacidad</a>
		</div>
	</footer>
    <?php
        include_once 'components/js.php';
    ?>
</body>

</html>