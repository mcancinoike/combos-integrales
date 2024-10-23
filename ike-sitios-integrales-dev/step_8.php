
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
					Para finalizar, ingresa el número de tu Tarjeta de Crédito o Débito HSBC. Todos tus datos se encuentran protegidos.
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
							<!-- <div class="frm__group check">
								<input type="checkbox" name="aviso" value="1" class="frm__control check">
								<div>Acepto el aviso de privacidad de &nbsp;<a href="https://ikeasistencia.com/aviso-legal-y-de-privacidad" target="_blank">Iké</a> y <a href="https://www.hsbc.com.mx/terminos-y-condiciones/" target="_blank">HSBC</a></div>
							</div> -->
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
				<button class="box__btn disabled" id="btnStep8">Enviar solicitud</button>
				<div class="box__button__line"></div>
			</div>
		</div>
	</section>