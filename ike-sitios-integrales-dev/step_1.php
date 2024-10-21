<?php
    require_once "backend/conexion/conexion.php";
    $conexion = new conexion;
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
	<input type="hidden" id="suma_asegurada" name="suma_asegurada" value="">
	<input type="hidden" id="prima_anual" name="prima_anual" value="">
	<input type="hidden" id="subtotal_mensual" name="subtotal_mensual" value="">
	<input type="hidden" id="prima" name="prima" value="">
	<section class="step show" id="step1">
		<div class="header__step">
			<div class="header__step__content">
				<a href="./" class="header__step__arrow">
					<img src="img/icons/arrow-left.svg">
				</a>
				<div class="header__step__title">HSBC Seguros | Iké&#174; Asistencias</div>
			</div>
		</div>

		<div class="progress">
			<div>Paso 1 de 8 | Elige la suma asegurada</div>
			<div class="progress__line">
				<div class="progress__done"></div>
			</div>
		</div>

		<div class="info">
			<div class="box">
				<div class="box__title">
					Primero, solicita tu Seguro por Accidentes Personales
				</div>
				<p class="box__txt2">Si no quieres este seguro puedes dar clic en "Continuar". Al hacerlo, pasarás directo a las asistencias.</p>
			</div>

			<div class="tbl" id="tblSuma1">
				<div class="tbl__header">
					<div class="tbl__header__col">Suma asegurada</div>
					<div class="tbl__header__col">Pago mensual</div>
				</div>
				<div class="tbl__body">
                    <?php  
                        $primas = "";              
                        $query = "SELECT * FROM hsbc_prima_ap WHERE active = 1 ORDER BY suma_asegurada ASC;";
                        foreach ($conexion->getData($query) as $val) {
                            $primas .='<div class="tbl__row">';
                            $primas .= '<div class="tbl__col">';
                            $primas .= '<input type="radio" name="seguro" value="'. $val['id'] .'" data-id-prima="'. $val['id'] .'" data-suma-asegurada="'. $val['suma_asegurada'] .'" data-pago-mensual="'. $val['prima_mensual'] .'"> $'. number_format($val['suma_asegurada']) .'</div>';
                            $primas .= '<div class="tbl__col price">$'. $val['prima_mensual'] .'</div>';
                            $primas .= '</div>';
                        }
                        echo $primas;
                    ?>
				</div>
			</div>
		</div>

		<div class="separator__line s1"></div>

		<div class="info">

			<div class="resume" id="resumenStep1">
				<div class="resume__title">Resumen de solicitud del seguro</div>
			</div>

			<div class="tbl" id="tblStep1">
				<div class="tbl__body">
					<div class="tbl__row">
						<div class="tbl__col left suma">
							<div>Suma asegurada:</div>
							<div id="sumaAsegurada">$750,000 MXN</div>
						</div>
						<div class="tbl__col right suma">
							<div>Prima anual:</div>
							<div id="totalAnual">$1,740.00 MXN</div>
						</div>
					</div>
					<div class="tbl__row">
						<div class="tbl__col left green subtotal">
							Subtotal mensual a pagar:
						</div>
						<div class="tbl__col right green subtotal2" id="pagoMensual">
							$145.00 MXN
						</div>
					</div>
				</div>
			</div>
			
			<div class="box__button stp">
				<button class="box__btn" id="btnStep1">Continuar</button>
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