<?php
include_once "../backend/post.php";
?>
<section class="step show" id="step4">
	<div class="header__step">
		<div class="header__step__content">
			<a  class="header__step__arrow">
				<img src="<?php echo $_SESSION["relativePath"]?>img/icons/arrow-left.svg">
			</a>
			<div class="header__step__title">
				<img src="<?php echo $_SESSION["relativePath"]?>img/ike-logo.svg" class="header__ike"> | 
				<img src="<?php echo $_SESSION["relativePath"]?>img/hsbc-logo2.svg" class="header__hsbc">
			</div>
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
				Te estaremos enviando un código por SMS al número de celular registrado. Al ingresar el código, estarás aceptando la solicitud de contratación del seguro contratación del seguro y el método de pago.
			</div>
			<p class="box__txt2" id="timer">Vence en <i>1:55</i> minutos</p

				<form id="frmRegister">
			<div class="frm code">
				<div class="frm__group">
					<label>Ingresa el código</label>
					<input type="text" name="codigoSms" class="frm__control" autocomplete="off">
					<a id="sendNewCode" class="frm__generate cursor-pointer">
						<img src="<?php echo $_SESSION["relativePath"]?>img/icons/refresh.svg">
						Generar un código nuevo</a>
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