<?php
include_once "backend/post.php";

?>

    <input type="hidden" id="suma_asegurada" name="suma_asegurada" value="<?php echo $suma_asegurada; ?>">
	<input type="hidden" id="prima_anual" name="prima_anual" value="<?php echo $prima_anual; ?>">
	<input type="hidden" id="subtotal_mensual" name="subtotal_mensual" value="<?php echo $subtotal_mensual; ?>">
    <input type="hidden" id="subtotal_mensual_asistencia" name="subtotal_mensual_asistencia" value="">
    <input type="hidden" id="prima" name="prima" value="<?php echo $idPrima; ?>">
    <section class="step show" id="step2">
        <div class="header__step">
            <div class="header__step__content">
                <a href="javascript:go2Step(1);" class="header__step__arrow">
                    <img src="img/icons/arrow-left.svg">
                </a>
                <div class="header__step__title">HSBC Seguros | Iké&#174; Asistencias</div>
            </div>
        </div>

        <div class="progress">
            <div>Paso 2 de 8 | Selecciona tus asistencias</div>
            <div class="progress__line">
                <div class="progress__done s2"></div>
            </div>
        </div>

        <div class="info">
            <div class="box">
                <div class="box__title">
                    Ahora, elige las Asistencias Iké que más te convengan
                </div>
                <p class="box__txt2">Este paso es opcional y no es obligatorio que elijas alguna de las asistencias.</p>
            </div>

            <div class="tbl" id="tblAsistencias">
                <div class="tbl__header2">
                    <div class="tbl__header__col2"></div>
                    <div class="tbl__header__col2">Pago mensual</div>
                </div>
                <div class="tbl__body">
                <?php  
                    $asistencias = "";                   
                    $query = "SELECT * FROM hsbc_assistance WHERE active = 1 ORDER BY id ASC;";
                    foreach ($conexion->getData($query) as $val) {
                        $mAsistencia = $val['modal'];
                        $asistencias .= '<div class="tbl__row2">';
                        $asistencias .= '<div class="tbl__col2 asistencia">';
                        $asistencias .= '<input type="checkbox" class="chkbox" name="asistencia[]" value="'. $val['id'] .'" data-costo="'. $val['price'] .'">';
                        $asistencias .= ' <div class="tbl__asistencia">';
                        $asistencias .= '<h3>'. $val['assistance'] .'</h3>';
                        $asistencias .= '<p>'. $val['description'] .'</p>';
                        $asistencias .= '<a href="javascript:showAsistencia(\''. $mAsistencia .'\');" class="more"> Conoce más <img src="img/icons/arrow-more.svg"></a>';
                        $asistencias .= '</div></div>';
                        $asistencias .= '<div class="tbl__col2 price">+ $'. $val['price'] .'</div></div>';
                    }
                    echo $asistencias
                ?>                    
                </div>
            </div>
        </div>

        <div class="separator__line"></div>

        <div class="info">
            <div class="resume">
                <h3 class="resume__title">Resumen de solicitud del seguro</h3>
            </div>

            <div class="tbl">
                <div class="tbl__body">
                    <div class="tbl__row">
                        <div class="tbl__col left">
                            Suma asegurada:
                            <br>
                            <!-- $750,000 MXN -->
                            <?php echo "$" . $suma_asegurada . "MXN";?>
                        </div>
                        <div class="tbl__col right">
                            Suma total anual:
                            <br>
                            <!-- $1,740.00 MXN -->
                            <?php echo "$" . $prima_anual . "MXN";?>
                        </div>
                    </div>
                    <div class="tbl__row">
                        <div class="tbl__col left subtotal">
                            Subtotal mensual a pagar
                        </div>
                        <div class="tbl__col right subtotal2">
                            <!-- $145.00 MXN -->
                            <?php echo "$" . $subtotal_mensual . "MXN";?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="separator__line"></div>

        <div class="info">

            <div class="resume">
                <h3 class="resume__title asistencias">Resumen de solicitud de asistencias</h3>
            </div>

            <div class="tbl" id="tblStep2">
                <div class="tbl__body">
                <?php  
                    $asistencias2 = "";
                    $query = "SELECT * FROM hsbc_assistance WHERE active = 1 ORDER BY id ASC;";
                    foreach ($conexion->getData($query) as $val) {
                        $asistencias2 .= '<div class="tbl__row a'. $val['id'] .'">';
                        $asistencias2 .= '<div class="tbl__col left">'. $val['assistance'] .'</div>';
                        $asistencias2 .= '<div class="tbl__col right">+$'. $val['price'] .' MXN</div></div>';                    
                    }
                    echo $asistencias2
                ?>                    
                </div>
            </div>
        </div>

        <div class="separator__line"></div>

        <div class="info">

            <div class="tbl" id="tblStep2Total" data-total="<?php echo $subtotal_mensual; ?>">
                <div class="tbl__body">
                    <div class="tbl__row">
                        <div class="tbl__col left green subtotal">
                            Total mensual a pagar del seguro:
                        </div>
                        <div class="tbl__col right green subtotal2">
                            <!-- $145.00 MXN -->
                            <?php echo "$" . $subtotal_mensual . "MXN";?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="separator__line"></div>

        <div class="info">

            <div class="continue">
                <p>Para continuar con la solicitud, confirma que eres Cliente HSBC (es decir, que tienes una cuenta de débito o crédito HSBC).</p>
                <div class="continue__check">
                    <input type="checkbox" name="clienteHsbc" value="1"> ¡Sí, ya soy cliente HSBC!
                </div>
                <div class="continue__check">
                    <input type="checkbox" name="avisoHsbc" value="1"> Acepto aviso de privacidad de IKÉ y HSBC
                </div>
                <div class="continue__check">
                    <input type="checkbox" name="residenteHsbc" value="1"> Confirmo que me encuentro en territorio nacional
                </div>
            </div>

        </div>

        <div class="separator__line"></div>

        <div class="info">

            <div class="unirse">
                <p class="unirse__txt">Aún no soy cliente, pero me gustaría saber cómo puedo unirme.</p>
                <div class="unirse__box">
                    <div class="unirse__box__title">Abre tu cuenta digital hoy mismo.</div>
                    <p>Igual de seguro, pero desde tu celular.</p>
                    <a href="https://dco-ao.hsbc.com.mx/advance?cid=AFF_HBMX_N4_IK0_00001" target="_blank">Saber más
                        <img src="img/icons/arrow-red.svg">
                    </a>
                </div>
            </div>

            <div class="frm__errmsg" id="frmErrMsg2"></div>

            <div class="box__button stp">
                <button class="box__btn" id="btnStep2">Continuar</button>
                <div class="box__button__line"></div>
            </div>
        </div>
    </section>

    <!-- Asistencias -->

    <section class="lightbox" id="aMedica">
        <div class="lightbox__close">
            <img src="img/icons/close.svg">
        </div>
        <div class="lightbox__content">
            <h2 class="asis__title">Asistencia Médica</h2>

            <div class="asis__picture">
                <img src="img/asistencias/senior-desktop.png">
            </div>

            <div class="asis__box">
                <div class="box">

                    <h2 class="asistencia__title">Check up</h2>
                    <p class="asistencia__txt">El Usuario podrá acudir a un laboratorio de la red para
                        realizarse un check up que incluye: EGO, química sanguínea
                        de 6 elementos, factor RH y BH.</p>

                    <h2 class="asistencia__title">Consultas con médico general a domicilio</h2>

                    <p class="asistencia__txt">Se coordinará la cita de un médico general de nuestra red en
                        caso de emergencia para que acuda al domicilio del usuario.</p>

                    <h2 class="asistencia__title">Consulta médica con especialista</h2>

                    <p class="asistencia__txt">A petición del Usuario se concretará la cita con un médico
                        especialista, para Geriatras la cobertura solo en CDMX,
                        Monterrey y Guadalajara.</p>

                    <h2 class="asistencia__title">Enfermera para cuidados por convalecencia a domicilio</h2>

                    <p class="asistencia__txt">En caso de que el usuario lo requiera y bajo prescripción
                        médica se coordinara la asistencia de una enfermera por
                        cuidas por convalecencia.</p>

                    <h2 class="asistencia__title">Cuidado dental</h2>

                    <p class="asistencia__txt">Atención odontológica a través de nuestra red de
                        proveedores a nivel nacional, incluye: una consulta, una
                        urgencia dental, una radiografía y una aplicación de flúor.</p>

                    <h2 class="asistencia__title">Segunda opinión médica</h2>

                    <p class="asistencia__txt">Se pondrá en contacto al usuario por llamada con médicos
                        especialistas para obtener una segunda opinión médica a
                        través del intercambio de información.</p>

                    <h2 class="asistencia__title">Asistencia médica/psicológica/nutricional online</h2>

                    <p class="asistencia__txt">Orientación médica a través de videollamada con personal
                        especializado sin proporcionar diagnóstico definitivo.</p>

                    <h2 class="asistencia__title">Búsqueda y envío de medicamentos</h2>

                    <p class="asistencia__txt">En caso de que el Usuario lo requiera se apoyara con la búsqueda
                        y entrega de medicamentos a su domicilio, el costo de estos será
                        cubierto por el Usuario.</p>

                    <h2 class="asistencia__title">Renta de equipo médico especializado</h2>

                    <p class="asistencia__txt">Se brindará apoyo en caso de que el Usuario requiera la renta de
                        equipo médico especializado por convalecencia post hospitalaria
                        o accidente.</p>

                    <h2 class="asistencia__title">Red de descuentos</h2>

                    <p class="asistencia__txt">El usuario tendrá acceso a una red de descuentos médicos:
                        hospitales, laboratorios, farmacias.</p>

                    <h2 class="asistencia__title">Cupón de Uber para visitas médicas</h2>

                    <p class="asistencia__txt">Se brindará al usuario un cupón para que pueda redimir el
                        beneficio en la plataforma y acuda a su visita médica.</p>

                    <h2 class="asistencia__title">Orientación médica y nutricional telefónica y online</h2>

                    <p class="asistencia__txt">Orientación médica a través de videollamada o llamada con
                        personal especializado sin proporcionar diagnóstico
                        definitivo.</p>

                    <h2 class="asistencia__title">Ambulancia</h2>

                    <p class="asistencia__txt">En caso de accidente o emergencia repentina que provoque
                        lesiones o traumatismos.</p>

                    <h2 class="asistencia__title">Adaptaciones para vivir con discapacidad</h2>

                    <p class="asistencia__txt">Se ofrecerá al usuario especialistas a domicilio para realizar
                        adaptaciones para vivir con discapacidad auditiva, visual,
                        motriz y mental.</p>

                    <h2 class="asistencia__title">Recordatorios de citas médicas e ingesta de medicamentos</h2>

                    <p class="asistencia__txt">Asistencia telefónica que ayudará al usuario con recordatorios
                        acerca toma de medicamentos.</p>

                </div>
            </div>
        </div>
    </section>

    <section class="lightbox" id="aPadres">
        <div class="lightbox__close">
            <img src="img/icons/close.svg">
        </div>
        <div class="lightbox__content">
            <h2 class="asis__title">Asistencia para Padres</h2>

            <div class="asis__picture">
                <img src="img/asistencias/senior-desktop.png">
            </div>

            <div class="asis__box">
                <div class="box">

                    <div class="box__asis">
                        <h2 class="asistencia__title">SALUD Y BIENESTAR</h2>
                        <ul class="asistencia__list">
                            <li>
                                Red de descuentos médicos
                                <br>
                                - Sin límite
                            </li>
                            <li>
                                Envió y búsqueda de medicamentos <br>
                                - Sin límite
                            </li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">BENEFICIOS</h2>
                        <ul class="asistencia__list">
                            <li>
                                Plataforma de descuentos
                                <br>
                                - Ilimitada
                            </li>
                            <li>
                                Cine 2x1
                                <br>
                                - 1 evento mensual
                            </li>
                            <li>Descuentos escolares</li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">PROFE ONLINE</h2>
                        <ul class="asistencia__list">
                            <li>
                                Profesor online
                                <br>
                                - Sin límite
                            </li>
                            <li>
                                Atención a menores con problemas de aprendizaje
                                <br>
                                - Sin límite
                            </li>
                            <li>
                                Asistencia Psicológica para menores
                                <br>
                                - Sin límite
                            </li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">HOGAR</h2>
                        <ul class="asistencia__list">
                            <li>
                                Soluciones en el hogar
                                <br>
                                - Cerrajeria, vidrieros y electricistas <br>
                                - 2 eventos al año hasta $1,200 por evento
                            </li>
                            <li>
                                Técnico online Sin limite
                                <br>
                                - Sin límite
                            </li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">VIAJES Y EXPERIENCIAS</h2>
                        <ul class="asistencia__list">
                            <li>
                                Plataforma de viajes
                                <br>
                                - Ilimitado
                            </li>
                            <li>
                                Traslado al aeropuerto
                                <br>
                                - 1 evento semestral hasta $500
                            </li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">MASCOTAS</h2>
                        <ul class="asistencia__list">
                            <li>
                                Veterinario online
                                <br>
                                - Ilimitado
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="lightbox" id="aSenior">
        <div class="lightbox__close">
            <img src="img/icons/close.svg">
        </div>
        <div class="lightbox__content">
            <h2 class="asis__title">Asistencia Senior</h2>

            <div class="asis__picture">
                <img src="img/asistencias/senior-desktop.png">
            </div>

            <div class="asis__box">
                <div class="box">

                    <div class="box__asis">
                        <h2 class="asistencia__title">SALUD Y BIENESTAR</h2>
                        <ul class="asistencia__list">
                            <li>
                                Botón de Pánico <br>
                                - 1 evento
                            </li>
                            <li>
                                Taxi para visita médica <br>
                                - 1 evento hasta $500
                            </li>
                            <li>
                                Enfermera para cuidados por convalecencia a domicilio <br>
                                - 2 eventos al año hasta $800 por evento
                            </li>
                            <li>
                                Gestoría administrativa <br>
                                - 1 evento
                            </li>
                            <li>
                                Asistencia Psicológica <br>
                                - Ilimitada
                            </li>
                            <li>
                                Adaptaciones para vivir con discapacidad <br>
                                - 1 evento hasta $1,000 por evento
                            </li>
                            <li>
                                Búsqueda y envío de medicamentos <br>
                                - Sin limite
                            </li>
                            <li>
                                Recordatorios de citas médicas e ingesta de medicamentos <br>
                                - Sin limite
                            </li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">HOGAR</h2>
                        <ul class="asistencia__list">
                            <li>
                                Handyman <br>
                                - 1 evento al año hasta $1000 por evento
                            </li>
                            <li>
                                Soluciones en el hogar <br>
                                - Plomeros, cerrajeros, vidrieros y electricistas <br>
                                - 2 eventos al año hasta $1,200 por evento
                            </li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">VIDA Y ESTILO</h2>
                        <ul class="asistencia__list">
                            <li>
                                Chofer <br>
                                - Ilimitado con costo preferencial
                            </li>
                            <li>
                                Cupón de Uber <br>
                                - 2 eventos al año hasta $150 por evento
                            </li>
                            <li>
                                Cine 2x1 <br>
                                - 1 cupón mensual
                            </li>
                            <li>
                                Referencias Senior <br>
                                - Centros recreativos, programas y centros de atención, casas de
                                retiro, escuelas de manualidades
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="lightbox" id="aJovenes">
        <div class="lightbox__close">
            <img src="img/icons/close.svg">
        </div>
        <div class="lightbox__content">
            <h2 class="asis__title">Asistencia para Jóvenes</h2>

            <div class="asis__picture">
                <img src="img/asistencias/senior-desktop.png">
            </div>

            <div class="asis__box">
                <div class="box">

                    <div class="box__asis">
                        <h2 class="asistencia__title">SALUD Y BIENESTAR</h2>
                        <ul class="asistencia__list">
                            <li>
                                Orientación legal online <br>
                                - Ilimitada
                            </li>
                            <li>
                                Adaptaciones para Gamers <br>
                                - 1 evento hasta $800
                            </li>
                            <li>
                                Cupon para actividades <br>
                                - 1 evento hasta $500
                            </li>
                            <li>
                                Pantalla protegida <br>
                                - 1 evento hasta $1,000
                            </li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">VIDA Y ESTILO</h2>
                        <ul class="asistencia__list">
                            <li>
                                Servicio de limpieza post party <br>
                                - 1 evento al año
                            </li>
                            <li>
                                Red de descuentos <br>
                                - Sin límite
                            </li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">VIAJES Y EXPERIENCIAS</h2>
                        <ul class="asistencia__list">
                            <li>
                                Concierge <br>
                                - Ilimitado
                            </li>
                            <li>
                                Plataforma de viajes <br>
                                - Ilimitada
                            </li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">BENEFICIOS</h2>
                        <ul class="asistencia__list">
                            <li>
                                Cine 2x1 <br>
                                - 1 evento mensual
                            </li>
                            <li>
                                Cupón de Starbucks <br>
                                - 1 cupón mensual por $50
                            </li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">MASCOTAS</h2>
                        <ul class="asistencia__list">
                            <li>
                                Veterinario online <br>
                                - Ilimitado
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="lightbox" id="aMascotas">
        <div class="lightbox__close">
            <img src="img/icons/close.svg">
        </div>
        <div class="lightbox__content">
            <h2 class="asis__title">Asistencia para Mascotas</h2>

            <div class="asis__picture">
                <img src="img/asistencias/senior-desktop.png">
            </div>

            <div class="asis__box">
                <div class="box">

                    <div class="box__asis">
                        <h2 class="asistencia__title">SALUD</h2>
                        <ul class="asistencia__list">
                            <li>
                                Veterinario online <br>
                                - Ilimitado
                            </li>
                            <li>
                                Consulta veterinaria por emergencia <br>
                                - 1 evento al año hasta $500
                            </li>
                            <li>
                                Check up mascota <br>
                                - 1 evento al año hasta $1,000
                            </li>
                            <li>
                                Pipeta antipulgas <br>
                                - 1 evento semestral hasta $400
                            </li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">VIDA Y ESTILO</h2>
                        <ul class="asistencia__list">
                            <li>
                                Red de descuentos <br>
                                - Ilimitado
                            </li>
                            <li>
                                Referencias <br>
                                - Hospedaje, guarderías, estancias, cuidadores y adiestradores
                            </li>
                        </ul>
                    </div>

                    <div class="box__asis">
                        <h2 class="asistencia__title">SEGURIDAD</h2>
                        <ul class="asistencia__list">
                            <li>
                                Asesoría de trámites viales y legales <br>
                                - Ilimitado
                            </li>
                            <li>
                                Orientación legal RC <br>
                                - Ilimitado
                            </li>
                            <li>
                                Servicio funerario <br>
                                - 1 evento hasta $1,000
                            </li>
                            <li>
                                Apoyo psicológico en el duelo de la mascota <br>
                                - Ilimitado
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <div class="lightbox__bg"></div>
