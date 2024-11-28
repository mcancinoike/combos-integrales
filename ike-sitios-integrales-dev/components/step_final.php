<?php
    session_start();
?>
<section class="step show" id="ty">
    <div class="ty">
        <div class="ty__box">
            <div class="ty__header">
                <img src="<?php echo $_SESSION["relativePath"]?>img/icons/ty.svg">
                <span id="msg-head"></span>
            </div>
            <div id="msg-seguro">

            </div>
        </div>
        <div class="box__button stp close">
            <a href="./" class="box__btn">Cerrar</a>
            <div class="box__button__line"></div>
        </div>
    </div>
</section>