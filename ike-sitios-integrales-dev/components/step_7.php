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
                $query = "SELECT cl.id_prima, ah.suma_asegurada, ah.prima_mensual, ah.prima_anual FROM clientes_hsbc cl INNER JOIN hsbc_prima_ah ah ON cl.id_prima = ah.id WHERE cl.id = '$idCliente';";
                foreach ($conexion->getData($query) as $val) {
					$seguro .= '<div class="tbl">';
					$seguro .= '<div class="tbl__body">';
					$seguro .= '<div class="tbl__row">';
					$seguro .= '<div class="tbl__col left">';
					$seguro .= 'Suma asegurada:<br>$ '. formatoMoneda($val['suma_asegurada']) .' MXN';
					$seguro .= '</div>';
					$seguro .= '<div class="tbl__col right">Prima anual:<br>$ '. formatoMoneda($val['prima_anual']) .' MXN';
					$seguro .= '</div></div><div class="tbl__row">';
					$seguro .= '<div class="tbl__col left subtotal">Subtotal mensual a pagar</div>';
					$seguro .= '<div class="tbl__col right subtotal2">$ '. formatoMoneda($val['prima_mensual']) .' MXN</div>';
					$seguro .= '</div></div></div>';

				}
				echo $seguro;
		?>
		<!-- <div class="tbl">
			<div class="tbl__body">
				<div class="tbl__row">
					<div class="tbl__col left">
						Suma asegurada:
						<br>
						$750,000 MXN
					</div>
					<div class="tbl__col right">
						Prima anual:
						<br>
						$1,740.00 MXN
					</div>
				</div>
				<div class="tbl__row">
					<div class="tbl__col left subtotal">
						Subtotal mensual a pagar
					</div>
					<div class="tbl__col right subtotal2">
						$145.00 MXN
					</div>
				</div>

			</div>
		</div> -->
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
			$price = 0.00;
			$query = "SELECT ass.assistance, ass.price FROM hsbc_cliente_assistance ca INNER JOIN hsbc_assistance ass ON ca.id_assistance = ass.id WHERE id_cliente = '$idCliente';";
			$asistencias .= '<div class="tbl"><div class="tbl__body">';
			foreach ($conexion->getData($query) as $val) {
				$price = $price + $val['price'];				
				$asistencias .= '<div class="tbl__row">';
				$asistencias .= '<div class="tbl__col left">'. $val['assistance'] .'</div>';
				$asistencias .= '<div class="tbl__col right">+$'. $val['price'] .' MXN</div></div>';				
			}
			$asistencias .= '<div class="tbl__row"><div class="tbl__col left subtotal">Subtotal mensual a pagar</div>';
			$asistencias .= '<div class="tbl__col right subtotal2">$'. $price .' MXN</div></div>';
			$asistencias .= '<div class="tbl__note"><img src="img/icons/info.svg" class="info__icon">Tu primer mes de asistencias no tiene costo.</div>';

			$asistencias .= '</div></div>';

			echo $asistencias;
		?>
		<!-- <div class="tbl">
			<div class="tbl__body">
				<div class="tbl__row">
					<div class="tbl__col left">
						Asistencia médica
					</div>
					<div class="tbl__col right">
						+$99.00 MXN
					</div>
				</div>
				<div class="tbl__row">
					<div class="tbl__col left">
						Asistencia para padres
					</div>
					<div class="tbl__col right">
						+$99.00 MXN
					</div>
				</div>
				<div class="tbl__row">
					<div class="tbl__col left subtotal">
						Subtotal mensual a pagar
					</div>
					<div class="tbl__col right subtotal2">
						$297.00 MXN
					</div>
				</div>
				<div class="tbl__note">
					<img src="img/icons/info.svg" class="info__icon">
					Tu primer mes de asistencias no tiene costo.
				</div>
			</div>
		</div> -->
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
						$0.00 MXN
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="separator__line"></div>

	<div class="info">

		<!-- <div class="resume action">
			<h3 class="resume__title">Beneficiarios para mi seguro</h3>
			<div class="resume__action">
				<a href="javascript:go2StepEdit(6);"><img src="img/icons/edit.svg"></a>
				<a href="javascript:;"><img src="img/icons/delete.svg"></a>
			</div>
		</div>

		<div class="tbl">
			<div class="tbl__body">
				<div class="tbl__row">
					<div class="tbl__col left full">
						<img src="img/icons/person2.svg">
						<p>
							Benjamín Torres Sánchez
							<br>
							<span class="percentage">100%</span>
						</p>
					</div>
				</div>
			</div>
		</div> -->

		<div class="box__button stp">
			<button class="box__btn" id="btnStep7">Continuar</button>
			<div class="box__button__line"></div>
		</div>
	</div>
</section>