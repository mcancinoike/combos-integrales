<?php
include_once "backend/post.php";

?>

<section class="step show" id="editBeneficiario">
		<div class="header__step">
			<div class="header__step__content">
				<a href="javascript:go2Step(6);" class="header__step__arrow">
					<img src="img/icons/arrow-left.svg">
				</a>
				<div class="header__step__title">Editar beneficiario</div>
			</div>
		</div>

		<div class="info">
			<div class="box">
				<div class="box__title">
					Ingresa la información de tu beneficiario
				</div>
				<p class="box__txt2">Los datos marcados con * son obligatorios</p>

				<form id="frmEditBenef">
					<div class="frm">
						<div class="frm__group">
							<label>Parentesco*</label>
							<select name="parentesco" class="frm__control">
								<option value="">Seleccione</option>
								<option value="hijo" selected />Hijo</option>
								<option value="hija">Hija</option>
							</select>
						</div>
						<div class="frm__group">
							<label>Nombre*</label>
							<input type="text" name="nombre" class="frm__control" value="Benjamín">
						</div>
						<div class="frm__group">
							<label>Segundo nombre</label>
							<input type="text" name="segundoNombre" class="frm__control" value="">
						</div>
						<div class="frm__group">
							<label>Apellido paterno*</label>
							<input type="text" name="apellidoPaterno" class="frm__control" value="Torres">
						</div>
						<div class="frm__group">
							<label>Apellido materno*</label>
							<input type="text" name="apellidoMaterno" class="frm__control" value="Sánchez">
						</div>
						<div class="frm__group">
							<label>Estado civil*</label>
							<select name="estadoCivil" class="frm__control">
								<option value="">Seleccione</option>
								<option value="casado" selected />Casado</option>
								<option value="soltero">Soltero</option>
							</select>
						</div>
						<div class="frm__group">
							<label>Sexo*</label>
							<select name="sexo" class="frm__control">
								<option value="">Seleccione</option>
								<option value="masculino" selected />Masculino</option>
								<option value="femenino">Femenino</option>
							</select>
						</div>
						<div class="frm__group">
							<label>Fecha de nacimiento*</label>
							<input type="date" name="fechaNac" class="frm__control" value="1980-09-10">
						</div>
						<div class="frm__group">
							<label>Nacionalidad*</label>
							<select name="nacionalidad" class="frm__control">
								<option value="">Seleccione</option>
								<option value="mexicana" selected />Mexicano</option>
								<option value="extranjero">Extranjero</option>
							</select>
						</div>
						<div class="frm__group">
							<label>Actividad económica*</label>
							<select name="actividad" class="frm__control">
								<option value="">Seleccione</option>
								<option value="estudiante" selected />Estudiante</option>
								<option value="empresario">Empresario</option>
							</select>
						</div>
						<div class="frm__group">
							<label>Residencia*</label>
							<select name="residencia" class="frm__control">
								<option value="">Seleccione</option>
								<option value="estudiante">Estudiante</option>
								<option value="empresario">Empresario</option>
							</select>
						</div>
					</div>
				</form>

				<div class="frm__errmsg" id="frmErrMsgBenef2"></div>

			</div>

			<div class="box__button stp">
				<button class="box__btn" id="btnUpdateBenef">Actualizar</button>
				<div class="box__button__line"></div>
			</div>
		</div>
	</section>