<?php
    session_start();
    $_SESSION["relativePath"] = "../";
    $_SESSION["app"] = "ah";
?>
<!DOCTYPE html>
<html lang="es-Mx">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Seguro para Apoyo por Hospitalización HSBC</title>
	<meta name="description" content="¡Solicita tu Seguro por Accidentes Personales en minutos!">
	<link rel="shortcut icon" href="../img/favicon.ico">
    <?php
        include_once '../components/css.php';
    ?>
</head>
<!-- Page Header-->
<?php include '../components/header.php' ?>

<body>
	<div id="main-content">
		<section class="step show" id="home">
			<div class="header__step home line">
				<div class="header__step__content">
					<div class="header__step__title">HSBC Seguros</div>
				</div>
			</div>

			<div class="atf">
				<div class="atf__picture">
					<img src="../img/hsbc-seguro.png" class="mobile">
					<img src="../img/hsbc-seguro-desktop.png" class="desktop">
				</div>
				<div class="atf__txt">
					<h1>¡Solicita hoy tu Seguro para Apoyo por Hospitalización HSBC + Asistencias Iké!</h1>
				</div>
			</div>

			<div class="info home">
				<div class="breadcrumb">
					Productos <img src="../img/icons/arrow-red.svg"> Programa Seguros + Asistencia
				</div>
				<div class="box">
					<div class="box__title">
						<img src="../img/icons/realiza.svg">
						Realiza tu solicitud y obtén:
					</div>
					<ul class="box__list">
						<li>Seguro para Apoyo por Hospitalización</li>
					</ul>
					<p class="box__txt">Te apoyamos por cada día que tú te encuentres hospitalizado.</p>
					<ul class="box__list">
						<li>Programa de Asistencias Iké</li>
					</ul>
					<p class="box__txt">Amplía la protección de tu Seguro al contratar las Asistencias Iké que más te convengan <span class="bold">¡y llévate el primer mes sin costo!</span></p>
				</div>
				<div class="box last">
					<div class="box__title">
						<img src="../img/icons/haz.svg">
						Haz tu solicitud hoy mismo y obtén la mayor protección al menor precio.
					</div>
                    <p class="box__txt">También podrás contratar tu Seguro Apoyo por Hospitalización o el Programa de Asistencias Iké por separado.</p>
                    <p class="box__txt"><a href="https://www.hsbc.com.mx/content/dam/hsbc/mx/documents/seguros/hospitalizacion/cg_apoyo_hospitalizacion_nov.pdf" target="_blank" class="text-cg">Consulta las Condiciones Generales del Seguro <img src="<?php echo $_SESSION["relativePath"]?>img/icons/arrow-red.svg"></a></p>
				</div><br>
				<button class="box__btn" name="btnContinuar" id="step0">Continuar</button>
			</div>
		</section>
	</div>
	<div id="loading">
		<img src="../img/loading.svg" alt="cargando">
	</div>
	<footer>
		<div class="footer__content">
			<a href="../docs/tyc.pdf" target="_blank">Términos y condiciones</a>
			<a href="https://ikeasistencia.com/aviso-legal-y-de-privacidad" target="_blank">Aviso de Privacidad</a>
		</div>
	</footer>

    <?php
        include_once '../components/js.php';
    ?>

</body>
</html>