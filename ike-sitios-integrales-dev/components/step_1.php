<?php
    include_once "../backend/post.php";
?>
<section class="step show" id="step1">	
	<div class="header__step">
		<div class="header__step__content">
			<a href="./" class="header__step__arrow">
				<img src="img/icons/arrow-left.svg">
			</a>
			<div class="header__step__title">
				<img src="img/ike-logo.svg" class="header__ike"> | 
				<img src="img/hsbc-logo2.svg" class="header__hsbc">
			</div>
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
				Primero, solicita tu Seguro por <?php $_SESSION["app"] === "ap" ? "Accidentes Personales" : "Hospitalización"?>
			</div>
			<p class="box__txt2">Si no quieres este seguro puedes dar clic en "Continuar". Al hacerlo, pasarás directo a las asistencias.</p>
		</div>

        <?php if ($_SESSION["app"] === "ap"): ?>
		<div class="tbl" id="tblSuma1">
			<div class="tbl__header">
				<div class="tbl__header__col">Suma asegurada</div>
				<div class="tbl__header__col">Pago mensual con IVA incluido</div>
			</div>
			<div class="tbl__body">
				<?php
				$primas = "";
				$query = "SELECT * FROM hsbc_prima_ap WHERE active = 1 ORDER BY suma_asegurada ASC;";
				foreach ($conexion->getData($query) as $val) {
					$primas .= '<div class="tbl__row">';
					$primas .= '<div class="tbl__col">';
					$primas .= '<input type="radio" name="seguro" value="' . $val['id'] . '" data-id-prima="' . $val['id'] . '" data-suma-asegurada="' . $val['suma_asegurada'] . '" data-pago-mensual="' . $val['prima_mensual'] . '"> $' . number_format($val['suma_asegurada']) . '</div>';
					$primas .= '<div class="tbl__col price">$' . $val['prima_mensual'] . '</div>';
					$primas .= '</div>';
				}
				echo $primas;
				?>
			</div>
		</div>
        <?php else: ?>
            <form id="frmRegister1">
                <div class="frm">
                    <div class="frm__group">
                        <label>Fecha de nacimiento*</label>
                        <input type="date" name="fechaNac" class="frm__control">
                    </div>
                    <div class="frm__group">
                        <label>Sexo</label>
                        <select id="sexo" name="sexo" class="frm__control">
                            <option value="">Seleccione</option>
                            <option value="m">Mujer</option>
                            <option value="h">Hombre</option>
                        </select>
                    </div>
                    <div class="frm__group">
                        <div id="ajaxSumaAsegurada"></div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
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