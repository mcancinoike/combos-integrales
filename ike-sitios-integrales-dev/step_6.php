<?php
    require_once "backend/conexion/conexion.php";
    $conexion = new conexion;   
	$idCliente = $_POST['idCliente'];
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
	<input type="hidden" id="idCliente" name="idCliente" value="<?php echo $idCliente; ?>">
	<section class="step show" id="step6">
		<div class="header__step">
			<div class="header__step__content">
				<a href="javascript:go2Step(5);" class="header__step__arrow">
					<img src="img/icons/arrow-left.svg">
				</a>
				<div class="header__step__title">HSBC Seguros | Iké&#174; Asistencias</div>
			</div>
		</div>

		<div class="progress">
			<div>Paso 6 de 8 | Elige a tus beneficiarios</div>
			<div class="progress__line">
				<div class="progress__done s6"></div>
			</div>
		</div>

		<div class="info">
			<div class="box">
				<div class="box__title">
					¡Perfecto! Ya puedes definir quienes serán los beneficiarios de tu seguro, puedes agregar hasta 5.
				</div>
			</div>

			<div class="box__lista" id="listBenef">
				<?php  
                    $beneficiarios = "";                   
                    $query = "SELECT * FROM beneficiaries_hsbc WHERE active = 1 AND id_cliente = '$idCliente' ORDER BY id ASC;";
                    foreach ($conexion->getData($query) as $val) {
						$nombre = $val['name'] . " " . $val['middle_name'] . " " . $val['pater_surname'] . " " . $val['mater_surname'];
						$beneficiarios .= '<div class="box__row b1">';
						$beneficiarios .= '<div class="box__info"><div class="box__name">'. $nombre .'</div>';
						$beneficiarios .= '<div class="box__action">';
						$beneficiarios .= '<a href="javascript:editBenef('. $val['id'] .');"><img src="img/icons/edit.svg"></a>';
						$beneficiarios .= '<a href="javascript:deleteBenef('. $val['id'] .');"><img src="img/icons/delete.svg"></a>';
						$beneficiarios .= '</div></div>';
						$beneficiarios .= '<div class="box__percentage">Porcentaje';
						$beneficiarios .= '<div class="box__percentage__input"><input type="text" name="porcentaje"> %';
						$beneficiarios .= '</div></div></div><br>';					
					}
					echo $beneficiarios;
				?>
				<!-- <div class="box__row b1">
					<div class="box__info">
						<div class="box__name">
							Benjamín Torres Sánchez
						</div>
						<div class="box__action">
							<a href="javascript:editBenef(1);"><img src="img/icons/edit.svg"></a>
							<a href="javascript:deleteBenef(1);"><img src="img/icons/delete.svg"></a>
						</div>
					</div>
					<div class="box__percentage">
						Porcentaje
						<div class="box__percentage__input">
							<input type="text" name="porcentaje"> %
						</div>
					</div>
				</div> -->
			</div>

			<div class="box__button2">
				<a href="javascript:;" class="box__btn2" id="btnNewBenef">
					<img src="img/icons/add.svg">
					Agregar otro beneficiario
				</a>
			</div>

			<div class="frm__errmsg" id="frmErrMsg6"></div>

			<div class="box__button code">
				<button class="box__btn" id="btnStep6">Continuar</button>
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