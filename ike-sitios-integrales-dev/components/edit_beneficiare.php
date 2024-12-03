<?php
include_once "../backend/post.php";
$idBeneficiario = $_POST['idBeneficiario'];

$query = "SELECT * FROM beneficiaries_hsbc WHERE active = 1 AND id = '$idBeneficiario';";
foreach ($conexion->getData($query) as $val) {
	$parentesco = $val['relationship'];
	$nombre = $val['name'];
	$nombre2 = $val['middle_name'];
	$paterno = $val['pater_surname'];
	$materno = $val['mater_surname'];
	$fechaNac = $val['date_birth'];
	$civil = $val['marital_status'];
	$sexo = $val['sex'];
	$rfc = $val['rfc'];
	$nacionalidad = $val['nationality'];
	$actividad = $val['economic_activity'];
	$residencia = $val['residence'];
}
?>
<input type="hidden" id="idBeneficiario" name="idBeneficiario" value="<?php echo htmlspecialchars($idBeneficiario, ENT_NOQUOTES, 'UTF-8'); ?>">
<section class="step show" id="editBeneficiario">
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

	<div class="info">
		<div class="box">
			<div class="box__title">
				Ingresa la información de tu beneficiario
			</div>
			<p class="box__txt2">Los datos marcados con * son obligatorios</p>

			<form id="frmEditBenef" autocomplete="on">
				<div class="frm">
					<div class="frm__group">
						<label>Parentesco*</label>
						<select name="parentesco" class="frm__control">
							<option value="">Seleccione</option>
							<option value="Esposo" <?php echo ($parentesco == 'Esposo' ? 'selected' : ''); ?>>Esposo(a)</option>
							<option value="Cónyuge" <?php echo ($parentesco == 'Cónyuge' ? 'selected' : ''); ?>>Cónyuge</option>
							<option value="Hijo(a)" <?php echo ($parentesco == 'Hijo(a)' ? 'selected' : ''); ?>>Hijo(a)</option>
							<option value="Padre" <?php echo ($parentesco == 'Padre' ? 'selected' : ''); ?>>Padre</option>
							<option value="Madre" <?php echo ($parentesco == 'Madre' ? 'selected' : ''); ?>>Madre</option>
							<option value="Empleador" <?php echo ($parentesco == 'Empleador' ? 'selected' : ''); ?>>Empleador</option>
							<option value="Otros" <?php echo ($parentesco == 'Otros' ? 'selected' : ''); ?>>Otros</option>
							<option value="Irrevocable" <?php echo ($parentesco == 'Irrevocable' ? 'selected' : ''); ?>>Irrevocable</option>
							<option value="Hermano(a)" <?php echo ($parentesco == 'Hermano(a)' ? 'selected' : ''); ?>>Hermano(a)</option>
						</select>
					</div>
					<div class="frm__group">
						<label>Nombre*</label>
						<input type="text" name="nombre" class="frm__control" value="<?php echo htmlspecialchars($nombre, ENT_NOQUOTES, 'UTF-8'); ?>">
					</div>
					<div class="frm__group">
						<label>Segundo nombre</label>
						<input type="text" name="segundoNombre" class="frm__control" value="<?php echo htmlspecialchars($nombre2, ENT_NOQUOTES, 'UTF-8'); ?>">
					</div>
					<div class="frm__group">
						<label>Apellido paterno*</label>
						<input type="text" name="apellidoPaterno" class="frm__control" value="<?php echo htmlspecialchars($paterno, ENT_NOQUOTES, 'UTF-8'); ?>">
					</div>
					<div class="frm__group">
						<label>Apellido materno*</label>
						<input type="text" name="apellidoMaterno" class="frm__control" value="<?php echo htmlspecialchars($materno, ENT_NOQUOTES, 'UTF-8'); ?>">
					</div>
					<div class="frm__group">
						<label>Estado civil*</label>
						<select name="estadoCivil" class="frm__control">
							<option value="">Seleccione</option>
							<option value="No Aplica" <?php echo ($civil == 'No Aplica' ? 'selected' : ''); ?>>No Aplica</option>
							<option value="soltero" <?php echo ($civil == 'soltero' ? 'selected' : ''); ?>>Soltero</option>
							<option value="casado" <?php echo ($civil == 'casado' ? 'selected' : ''); ?>>Casado</option>
							<option value="Divorciado" <?php echo ($civil == 'Divorciado' ? 'selected' : ''); ?>>Divorciado</option>
							<option value="Viudo" <?php echo ($civil == 'Viudo' ? 'selected' : ''); ?>>Viudo</option>
							<option value="Unión Libre" <?php echo ($civil == 'Unión Libre' ? 'selected' : ''); ?>>Unión Libre</option>
							<option value="Separado" <?php echo ($civil == 'Separado' ? 'selected' : ''); ?>>Separado</option>
						</select>
					</div>
					<div class="frm__group">
						<label>Sexo</label>
						<select name="sexo" class="frm__control">
							<option value="">Seleccione</option>
							<option value="masculino" <?php echo ($sexo == 'masculino' ? 'selected' : ''); ?>>Masculino</option>
							<option value="femenino" <?php echo ($sexo == 'femenino' ? 'selected' : ''); ?>>Femenino</option>
						</select>
					</div>
					<div class="frm__group">
						<label>Fecha de nacimiento*</label>
						<input type="date" name="fechaNac" class="frm__control" value="<?php echo htmlspecialchars($fechaNac, ENT_NOQUOTES, 'UTF-8'); ?>">
					</div>
					<div class="frm__group">
						<label>RFC</label>
						<input type="text" name="rfc" class="frm__control" maxlength="13" value="<?php echo htmlspecialchars($rfc, ENT_NOQUOTES, 'UTF-8'); ?>">
						<span class="desktop">El RFC no debe llevar guiones</span>
					</div>
					<div class="frm__group">
						<label>Nacionalidad*</label>
						<select name="nacionalidad" class="frm__control">
							<option value="">Seleccione</option>
							<?php
								$query = "SELECT * FROM hsbc_cat_nacionalidades WHERE active = 1 ORDER BY description ASC;";
								foreach ($conexion->getData($query) as $val) {
									echo '<option value="' . $val['description'] . '" '. ($nacionalidad == $val['description']  ? 'selected' : '') .'>' . $val['description'] . '</option>';
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
									echo '<option value="' . $val['description'] . '" '. ($actividad == $val['description']  ? 'selected' : '') .'>' . $val['description'] . '</option>';
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
									echo '<option value="' . $val['description'] . '" '. ($residencia == $val['description']  ? 'selected' : '') .'>' . $val['description'] . '</option>';
								}
							?>
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