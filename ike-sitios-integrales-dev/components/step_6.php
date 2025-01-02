<?php
    include_once "../backend/post.php";
?>
<section class="step show" id="step6">
	<div class="header__step">
		<div class="header__step__content">
			<a class="header__step__arrow gostep cursor-pointer" data-step="3">
				<img src="<?php echo $_SESSION["relativePath"]?>img/icons/arrow-left.svg">
			</a>
			<div class="header__step__title">
				<img src="<?php echo $_SESSION["relativePath"]?>img/ike-logo.svg" class="header__ike"> | 
				<img src="<?php echo $_SESSION["relativePath"]?>img/hsbc-logo2.svg" class="header__hsbc">
			</div>
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
				¡Perfecto! Ya puedes definir quienes serán los beneficiarios de tu Seguro, puedes agregar hasta 5.
			</div>
		</div>

		<div class="box__lista" id="listBenef"></div>

		<div class="box__button2">
			<a class="box__btn2 cursor-pointer" id="btnNewBenef">
				<img src="<?php echo $_SESSION["relativePath"]?>img/icons/add.svg">
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