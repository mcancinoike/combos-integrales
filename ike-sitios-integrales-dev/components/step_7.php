<?php 
    include_once "../backend/post.php";
?>
<section class="step show" id="step7">
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
		<div>Paso 7 de 8 | Verifica tu solicitud</div>
		<div class="progress__line">
			<div class="progress__done s7"></div>
		</div>
	</div>

	<div class="info">
		<div class="box">
			<div class="box__title">
				Verifica los detalles de tu solicitud. Puedes editarlos en caso de que lo necesites.
			</div>
		</div>
	</div>

	<div class="separator__line"></div>

	<div class="info">

		<input type="hidden" id="isBack2Edit" value="0">

		<div class="resume action">
			<h3 class="resume__title">Seguro por <?php $_SESSION["app"] === "ap" ? "Accidentes Personales" : "Hospitalización"?> </h3>
			<div class="resume__action">
				<a class="icon-seguro-edi gostep-1 cursor-pointer"><img src="<?php echo $_SESSION["relativePath"]?>img/icons/edit.svg"></a>
				<a class="icon-seguro-del del-seguro cursor-pointer"><img src="<?php echo $_SESSION["relativePath"]?>img/icons/delete.svg"></a>
				<a class="icon-seguro-add gostep-1 cursor-pointer"><img src="<?php echo $_SESSION["relativePath"]?>img/icons/add.svg"></a>
			</div>
		</div>
        <span id="resumSoli"></span>
	</div>

	<div class="separator__line"></div>

	<div class="info">

		<div class="resume action">
			<h3 class="resume__title">Programa de Asistencias Iké</h3>
			<div class="resume__action">
				<a class="icon-asistencias-edit gostep-2 cursor-pointer"><img src="<?php echo $_SESSION["relativePath"]?>img/icons/edit.svg"></a>
				<a class="icon-asistencias-del gostep-2 cursor-pointer"><img src="<?php echo $_SESSION["relativePath"]?>img/icons/delete.svg"></a>
                <a class="icon-asistencias-add gostep-2 cursor-pointer"><img src="<?php echo $_SESSION["relativePath"]?>img/icons/add.svg"></a>
			</div>
		</div>
		<span id="resumAsitencias"></span>
	</div>

	<div class="separator__line"></div>

	<div class="info">

		<div class="tbl">
			<div class="tbl__body">
				<div class="tbl__row">
					<div class="tbl__col left green subtotal">
						<strong>Total mensual a pagar del seguro + asistencias</strong>
					</div>
					<div class="tbl__col right green subtotal2"></div>
				</div>
			</div>
		</div>
	</div>

    <div class="separator__line section-ben"></div>

    <div class="info section-ben"">
        <div class="resume action">
            <h3 class="resume__title">Beneficiarios para mi seguro</h3>
            <div class="resume__action">
                <a class="cursor-pointer gostep-6"><img src="<?php echo $_SESSION["relativePath"]?>img/icons/edit.svg"></a>
                <a class="cursor-pointer gostep-6"><img src="<?php echo $_SESSION["relativePath"]?>img/icons/delete.svg"></a>
            </div>
        </div>
        <span id="resumBenef"></span>
    </div>

    <div class="box__button stp">
        <button class="box__btn" id="btnStep7">Continuar</button>
        <div class="box__button__line"></div>
    </div>
</section>