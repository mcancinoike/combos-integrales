<?php
include_once "backend/post.php";

?>

	<input type="hidden" id="idCliente" name="idCliente" value="<?php echo $idCliente; ?>">
	<section class="step show" id="addBeneficiario">
		<div class="header__step">
			<div class="header__step__content">
				<a href="javascript:go2Step(6);" class="header__step__arrow">
					<img src="img/icons/arrow-left.svg">
				</a>
				<div class="header__step__title">Añadir beneficiario</div>
			</div>
		</div>

		<div class="info">
			<div class="box">
				<div class="box__title">
					Ingresa la información de tu beneficiario
				</div>
				<p class="box__txt2">Los datos marcados con * son obligatorios</p>

				<form id="frmBeneficiario">
					<div class="frm">
						<div class="frm__group">
							<label>Parentesco*</label>
							<select name="parentesco" class="frm__control">
								<option value="">Seleccione</option>
								<option value="Esposo(a)">Esposo(a)</option>
								<option value="Cónyuge">Cónyuge</option>
								<option value="Hijo(a)">Hijo(a)</option>
								<option value="Padre">Padre</option>
								<option value="Madre">Madre</option>
								<option value="Empleador">Empleador</option>
								<option value="Otros">Otros</option>
								<option value="Irrevocable">Irrevocable</option>
								<option value="Hermano(a)">Hermano(a)</option>
							</select>
						</div>
						<div class="frm__group">
							<label>Nombre*</label>
							<input type="text" name="nombre" class="frm__control">
						</div>
						<div class="frm__group">
							<label>Segundo nombre</label>
							<input type="text" name="segundoNombre" class="frm__control">
						</div>
						<div class="frm__group">
							<label>Apellido paterno*</label>
							<input type="text" name="apellidoPaterno" class="frm__control">
						</div>
						<div class="frm__group">
							<label>Apellido materno*</label>
							<input type="text" name="apellidoMaterno" class="frm__control">
						</div>
						<div class="frm__group">
							<label>Estado civil*</label>
							<select name="estadoCivil" class="frm__control">
								<option value="">Seleccione</option>
								<option value="No Aplica">No Aplica</option>
								<option value="soltero">Soltero</option>
								<option value="casado">Casado</option>
								<option value="Divorciado">Divorciado</option>
								<option value="Viudo">Viudo</option>
								<option value="Unión Libre">Unión Libre</option>
								<option value="Separado">Separado</option>
							</select>
						</div>
						<div class="frm__group">
							<label>Sexo</label>
							<select name="sexo" class="frm__control">
								<option value="">Seleccione</option>
								<option value="masculino">Masculino</option>
								<option value="femenino">Femenino</option>
							</select>
						</div>
						<div class="frm__group">
							<label>Fecha de nacimiento*</label>
							<input type="date" name="fechaNac" class="frm__control">
						</div>
						<div class="frm__group">
							<label>Nacionalidad*</label>
							<select name="nacionalidad" class="frm__control">
								<option value="">Seleccione</option>
								<?php                      
									$query = "SELECT * FROM hsbc_cat_nacionalidades WHERE active = 1 ORDER BY description ASC;";
									foreach ($conexion->getData($query) as $val) {
										echo '<option value="'. $val['description'] .'">'. $val['description'] .'</option>';
									}
								?>								
							</select>
						</div>
						<div class="frm__group">
							<label>Actividad económica*</label>
							<select name="actividad" class="frm__control">
								<option value="">Seleccione</option>
								<?php                      
									$query = "SELECT * FROM hsbc_cat_actividades_economicas WHERE active = 1 ORDER BY description ASC;";
									foreach ($conexion->getData($query) as $val) {
										echo '<option value="'. $val['description'] .'">'. $val['description'] .'</option>';
									}
								?>
							</select>
						</div>
						<div class="frm__group">
							<label>Residencia*</label>
							<select name="residencia" class="frm__control">
								<option value="">Seleccione</option>
								<?php                      
									$query = "SELECT * FROM hsbc_cat_residencias WHERE active = 1 ORDER BY description ASC;";
									foreach ($conexion->getData($query) as $val) {
										echo '<option value="'. $val['description'] .'">'. $val['description'] .'</option>';
									}
								?>
							</select>
						</div>
					</div>
				</form>

				<div class="frm__errmsg" id="frmErrMsgBenef"></div>

			</div>

			<div class="box__button stp">
				<button class="box__btn" id="btnStepBenef">Agregar</button>
				<div class="box__button__line"></div>
			</div>
		</div>
	</section>