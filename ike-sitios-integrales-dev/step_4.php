<?php
include_once "backend/post.php";

?>

	<input type="hidden" id="idCliente" name="idCliente" value="<?php echo $idCliente; ?>">
	<section class="step show" id="step4">
		<div class="header__step">
			<div class="header__step__content">
				<a href="javascript:go2Step(3);" class="header__step__arrow">
					<img src="img/icons/arrow-left.svg">
				</a>
				<div class="header__step__title">HSBC Seguros | Iké&#174; Asistencias</div>
			</div>
		</div>

		<div class="progress">
			<div>Paso 4 de 8 | Confirma tu solicitud</div>
			<div class="progress__line">
				<div class="progress__done s4"></div>
			</div>
		</div>

		<div class="info">
			<div class="box">
				<div class="box__title">
                    Te estaremos enviando un código por SMS al número de celular registrado. Al ingresar el código, estarás aceptando la solicitud de contratación del seguro.
				</div>
				<!-- <p class="box__txt2" id="timer">Vence en <i>1:55</i> minutos</p> -->

				<form id="frmRegister">
					<div class="frm code">
						<div class="frm__group">
							<label>Ingresa el código</label>
							<input type="text" name="codigoSms" value="123456" class="frm__control" autocomplete="off">
						</div>
					</div>
				</form>

			</div>

			<div class="frm__errmsg" id="frmErrMsg4"></div>

			<div class="box__button code">
				<button class="box__btn" id="btnStep4">Continuar</button>
				<div class="box__button__line"></div>
			</div>
		</div>
	</section>