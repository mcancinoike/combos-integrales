<?php
require_once "../backend/conexion/conexion.php";
$conexion = new conexion;
?>

<input type="hidden" id="suma_asegurada" name="suma_asegurada" value="">
<input type="hidden" id="prima_anual" name="prima_anual" value="">
<input type="hidden" id="subtotal_mensual" name="subtotal_mensual" value="">
<input type="hidden" id="prima" name="prima" value="">
<section class="step show" id="step1">
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

    <div class="info">
        <div class="box">
            <div class="box__title">
                Primero, solicita tu Seguro por Hospitalización
            </div>
            <p class="box__txt2">Los datos marcados con * son obligatorios.</p>
            <p class="box__txt2">Si no quieres este seguro puedes dar clic en "Continuar". Al hacerlo, pasarás directo a las asistencias.</p>
        </div>

        <form id="frmRegister1">
            <div class="frm">
                <div class="frm__group">
                    <label>Fecha de nacimiento*</label>
                    <input type="date" name="fechaNac" class="frm__control">
                </div>
                <div class="frm__group">
                    <label>Sexo</label>
                    <select name="sexo" class="frm__control">
                        <option value="">Seleccione</option>
                        <option value="mujer">Mujer</option>
                        <option value="hombre">Hombre</option>
                    </select>
                </div>
                <div class="frm__group">
                    <label>Elige una suma asegurada</label>
                    <select name="sumaAsegurada" class="frm__control" id="sumaAseguradaS1">
                        <option value="">Seleccione</option>  
                        <option value="1000">$1,000 MXN</option>
                        <option value="2000">$2,000 MXN</option>
                        <option value="3000">$3,000 MXN</option>                      
                    </select>
                </div>
            </div>
        </form>
    </div>

    <div class="separator__line s1"></div>

    <div class="info">

        <div class="resume" id="resumenStep1">
            <div class="resume__title">Resumen de solicitud del seguro</div>
        </div>

        <div class="tbl" id="tblStep1">
            <div class="tbl__body">
                <div class="tbl__row">
                    <div class="tbl__col left suma">
                        <div>Suma asegurada:</div>
                        <div id="sumaAsegurada">$1,000 MXN</div>
                    </div>
                    <div class="tbl__col right suma">
                    </div>
                </div>
                <div class="tbl__row">
                    <div class="tbl__col left subtotal">
                        Subtotal mensual a pagar:
                    </div>
                    <div class="tbl__col right subtotal2" id="pagoMensual">
                        $193.33 MXN
                    </div>
                </div>
            </div>
        </div>

        <div class="box__button stp">
            <button class="box__btn" id="btnStep1">Continuar</button>
            <div class="box__button__line"></div>
        </div>
    </div>
</section>