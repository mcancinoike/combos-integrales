<?php
include_once "backend/post.php";

?>
	<input type="hidden" id="idCliente" name="idCliente" value="<?php echo $idCliente; ?>">
	<section class="step show" id="step5">
		<div class="header__step">
			<div class="header__step__content">
				<a href="javascript:go2Step(4);" class="header__step__arrow">
					<img src="img/icons/arrow-left.svg">
				</a>
				<div class="header__step__title">HSBC Seguros | Iké&#174; Asistencias</div>
			</div>
		</div>

		<div class="progress">
			<div>Paso 5 de 8 | Elige a tus beneficiarios</div>
			<div class="progress__line">
				<div class="progress__done s5"></div>
			</div>
		</div>

		<div class="info">
			<div class="box">
				<div class="box__title">
					¡Perfecto! Ya puedes definir quienes serán los beneficiarios de tu seguro
				</div>
				<div class="box__beneficiarios">
					<img src="img/icons/person.svg">
					<p>Puedes elegir hasta 5 beneficiarios</p>
				</div>

			</div>

			<div class="box__button2">
				<a href="javascript:;" class="box__btn2" id="btnStep5">
					<img src="img/icons/add.svg">
					Agregar beneficiario
				</a>
			</div>
		</div>
	</section>