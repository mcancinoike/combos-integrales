<?php
    include_once "../backend/post.php";
?>
<section class="step show" id="step3">
	<div class="header__step">
		<div class="header__step__content">
			<a class="header__step__arrow gostep cursor-pointer" data-step="2">
				<img src="<?php echo $_SESSION["relativePath"]?>img/icons/arrow-left.svg">
			</a>
			<div class="header__step__title">
				<img src="<?php echo $_SESSION["relativePath"]?>img/ike-logo.svg" class="header__ike"> |
				<img src="<?php echo $_SESSION["relativePath"]?>img/hsbc-logo2.svg" class="header__hsbc">
			</div>
		</div>
	</div>

	<div class="progress">
		<div>Paso 3 de 8 | Ingresa tus datos de contacto</div>
		<div class="progress__line">
			<div class="progress__done s3"></div>
		</div>
	</div>

	<div class="info">
		<div class="box">
			<div class="box__title">
				¡Queremos saber más de ti! Por favor, ingresa tus datos
			</div>
			<p class="box__txt2">Los datos marcados con * son obligatorios</p>

			<form id="frmRegister3" autocomplete="off">
				<div class="frm">
					<div class="frm__group">
						<label>Nombre*</label>
						<input type="text" name="nombre" class="frm__control onlyLetters" maxlength="60">
					</div>
					<div class="frm__group">
						<label>Segundo nombre</label>
						<input type="text" name="segundoNombre" class="frm__control onlyLetters" maxlength="60">
					</div>
					<div class="frm__group">
						<label>Apellido paterno*</label>
						<input type="text" name="apellidoPaterno" class="frm__control onlyLetters" maxlength="60">
					</div>
					<div class="frm__group">
						<label>Apellido materno*</label>
						<input type="text" name="apellidoMaterno" class="frm__control onlyLetters" maxlength="60">
					</div>
					<div class="frm__group">
						<label>Fecha de nacimiento*</label>
						<input type="date" name="fechaNac" class="frm__control">
					</div>
					<div class="frm__group">
						<label>RFC</label>
                        <span class="mobile">Verifica que tu RFC se haya calculado correctamente</span>
						<input type="text" name="rfc" class="frm__control" maxlength="13">
						<span class="desktop">Verifica que tu RFC se haya calculado correctamente</span>
					</div>
					<div class="frm__group">
						<label>Correo electrónico*</label>
						<span class="mobile">Enviaremos la póliza de tus productos a este correo electrónico</span>
						<input type="text" name="email" class="frm__control" maxlength="160">
						<span class="desktop">Enviaremos la poliza de tus productos a este correo electrónico</span>
					</div>
					<div class="frm__group">
						<label>Número celular*</label>
						<span class="mobile">Enviaremos un código de validación a este número. Por favor, ten tu dispositivo a la mano.</span>
						<input type="text" name="telefono" class="frm__control onlyNumbers" maxlength="10">
						<span class="desktop">Enviaremos un código de validación a este número. Por favor, ten tu dispositivo a la mano.</span>
					</div>
				</div>
			</form>

			<div class="separator__line s3"></div>

			<div class="frm__errmsg" id="frmErrMsg3"></div>

		</div>

		<div class="box__button stp b3">
			<button class="box__btn" id="btnStep3">Continuar</button>
			<div class="box__button__line"></div>
		</div>
	</div>
</section>