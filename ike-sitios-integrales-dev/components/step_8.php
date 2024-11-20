<?php 
    include_once "../backend/post.php";
?>
<section class="step show" id="step8">
	<div class="header__step">
		<div class="header__step__content">
			<a href="javascript:go2Step(7);" class="header__step__arrow">
				<img src="img/icons/arrow-left.svg">
			</a>
			<div class="header__step__title">HSBC Seguros | Iké&#174; Asistencias</div>
		</div>
	</div>

	<div class="progress">
		<div>Paso 8 de 8 | Ingresa tu método de pago</div>
		<div class="progress__line">
			<div class="progress__done s8"></div>
		</div>
	</div>

	<div class="info">
		<div class="box">
			<div class="box__title">
				Para finalizar, ingresa el número de Tarjeta de Crédito HSBC donde se domiciliará el pago o Débito HSBC donde se realizarán los cargos recurrentes. Todos los datos se encuentran protegidos
			</div>
		</div>

		<div class="note">
			<img src="img/icons/info.svg">
			<p>Tu solicitud se enviará a revisión. No haremos ningún cobro a tu tarjeta hasta que tu solicitud sea aceptada en un máximo de 48 horas.</p>
		</div>

		<div class="info">
			<div class="box">
				<form id="frmCard">
					<div class="frm pay">
						<div class="frm__group">
							<label>Número de tarjeta*</label>
							<p class="frm__note">Solo son válidas Tarjetas de Débito o Crédito HSBC.</p>
							<input type="text" name="numeroTarjeta" class="frm__control" maxlength="16">
						</div>
						<div class="frm__group check">
							<input type="checkbox" name="condiciones" value="1" class="frm__control check">
							<a href="docs/tyc.pdf" target="_blank">Aceptar Condiciones Generales del producto</a>
						</div>
						<div class="frm__group check">
							<input type="checkbox" name="envio" class="frm__control check">
							<div>Acepto el envío de la Póliza y Condiciones Generales al correo que registré previamente.</div>
						</div>
					</div>
				</form>

				<div class="frm__errmsg" id="frmErrMsg8"></div>

			</div>

		</div>

		<div class="box__button stp pay">
			<button class="box__btn" id="btnStep8">Enviar solicitud</button>
			<div class="box__button__line"></div>
		</div>
	</div>
</section>