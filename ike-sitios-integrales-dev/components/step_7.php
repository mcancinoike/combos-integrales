<?php 
include_once "../backend/post.php";
$idCliente = $_POST['idCliente'];
?>
<input type="hidden" id="idCliente" name="idCliente" value="<?php echo $idCliente; ?>">
<section class="step show" id="step7">
	<div class="header__step">
		<div class="header__step__content">
			<a href="./" class="header__step__arrow">
				<img src="img/icons/arrow-left.svg">
			</a>
			<div class="header__step__title">
				<img src="img/ike-logo.svg" class="header__ike"> | 
				<img src="img/hsbc-logo2.svg" class="header__hsbc">
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
			<h3 class="resume__title">Seguro por Accidentes Personales</h3>
			<div class="resume__action">
				<a href="javascript:go2StepEdit(1);"><img src="img/icons/edit.svg"></a>
				<a href="javascript:;"><img src="img/icons/delete.svg"></a>
			</div>
		</div>

		<?php
                $seguro = "";
				$count = 0;
				$prima = 0;
                $query = "SELECT cl.id_prima, ah.suma_asegurada, ah.prima_mensual, ah.prima_anual FROM clientes_hsbc cl INNER JOIN hsbc_prima_ah ah ON cl.id_prima = ah.id WHERE cl.id = '$idCliente';";
                foreach ($conexion->getData($query) as $val) {
					$count++;
					$prima = $val['prima_mensual'];
					$seguro .= '<div class="tbl">';
					$seguro .= '<div class="tbl__body">';
					$seguro .= '<div class="tbl__row">';
					$seguro .= '<div class="tbl__col left">';
					$seguro .= 'Suma asegurada:<br>$ '. formatoMoneda($val['suma_asegurada']) .' MXN';
					$seguro .= '</div>';
					$seguro .= '<div class="tbl__col right">Prima anual:<br>$ '. formatoMoneda($val['prima_anual']) .' MXN';
					$seguro .= '</div></div><div class="tbl__row">';
					$seguro .= '<div class="tbl__col left subtotal">Subtotal mensual a pagar</div>';
					$seguro .= '<div class="tbl__col right subtotal2">$ '. formatoMoneda($prima) .' MXN</div>';
					$seguro .= '</div></div></div>';

				}
				if($count == 0){
					$cero = 0;
					$seguro .= '<div class="tbl">';
					$seguro .= '<div class="tbl__body">';
					$seguro .= '<div class="tbl__row">';
					$seguro .= '<div class="tbl__col left">';
					$seguro .= 'Suma asegurada:<br>$ '. formatoMoneda($cero) .' MXN';
					$seguro .= '</div>';
					$seguro .= '<div class="tbl__col right">Prima anual:<br>$ '. formatoMoneda($cero) .' MXN';
					$seguro .= '</div></div><div class="tbl__row">';
					$seguro .= '<div class="tbl__col left subtotal">Subtotal mensual a pagar</div>';
					$seguro .= '<div class="tbl__col right subtotal2">$ '. formatoMoneda($cero) .' MXN</div>';
					$seguro .= '</div></div></div>';
				}
				echo $seguro;
		?>
	</div>

	<div class="separator__line"></div>

	<div class="info">

		<div class="resume action">
			<h3 class="resume__title">Programa de Asistencias Iké</h3>
			<div class="resume__action">
				<a href="javascript:go2StepEdit(2);"><img src="img/icons/edit.svg"></a>
				<a href="javascript:;"><img src="img/icons/delete.svg"></a>
			</div>
		</div>
		
		<?php 
			$asistencias = "";
			$price = 0;
			$query = "SELECT ass.assistance, ass.price FROM hsbc_cliente_assistance ca INNER JOIN hsbc_assistance ass ON ca.id_assistance = ass.id WHERE id_cliente = '$idCliente';";
			$asistencias .= '<div class="tbl"><div class="tbl__body">';
			foreach ($conexion->getData($query) as $val) {
				$price = $price + $val['price'];				
				$asistencias .= '<div class="tbl__row">';
				$asistencias .= '<div class="tbl__col left">'. $val['assistance'] .'</div>';
				$asistencias .= '<div class="tbl__col right">+$'. formatoMoneda($val['price']) .' MXN</div></div>';				
			}
			$asistencias .= '<div class="tbl__row"><div class="tbl__col left subtotal">Subtotal mensual a pagar</div>';
			$asistencias .= '<div class="tbl__col right subtotal2">$'. formatoMoneda($price) .' MXN</div></div>';
			$asistencias .= '<div class="tbl__note"><img src="img/icons/info.svg" class="info__icon">Tu primer mes de asistencias no tiene costo.</div>';

			$asistencias .= '</div></div>';

			echo $asistencias;
		?>		
	</div>

	<div class="separator__line"></div>

	<div class="info">

		<div class="tbl">
			<div class="tbl__body">
				<div class="tbl__row">
					<div class="tbl__col left green subtotal">
						<strong>Total mensual a pagar del seguro + asistencias</strong>
					</div>
					<div class="tbl__col right green subtotal2">
						$<?php echo formatoMoneda(($prima + $price))?> MXN
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="separator__line"></div>

	<div class="info">

		<div class="resume action">
			<h3 class="resume__title">Beneficiarios para mi seguro</h3>
			<div class="resume__action">
				<a href="javascript:go2StepEdit(6);"><img src="img/icons/edit.svg"></a>
				<a href="javascript:go2StepEdit(6);"><img src="img/icons/delete.svg"></a>
			</div>
		</div>

		<?php
                $beneficiarios = "";
                $query = "SELECT * FROM beneficiaries_hsbc WHERE id_cliente = '$idCliente';";
                foreach ($conexion->getData($query) as $val) {
					$name = $val['name'] . " " . $val['middle_name'] . " " . $val['pater_surname'] . " " . $val['mater_surname'];
					$beneficiarios .= '<div class="tbl"><div class="tbl__body">';
					$beneficiarios .= '<div class="tbl__row"><div class="tbl__col left full">';
					$beneficiarios .= '<img src="img/icons/person2.svg">';
					$beneficiarios .= '<p> ' . $name . ' <br><span class="percentage">' . $val['percentage'] . '%</span></p>';
					$beneficiarios .= '</div></div></div></div>';
				}
				echo $beneficiarios;
		?>
		<div class="box__button stp">
			<button class="box__btn" id="btnStep7">Continuar</button>
			<div class="box__button__line"></div>
		</div>
	</div>
</section>