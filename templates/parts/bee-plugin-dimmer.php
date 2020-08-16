<?php
/**
 * 
 */

 function hgodbee_dimmers(){
    hgodbee_dimmer_salvando();
    hgodbee_dimmer_carregando();
 }

function hgodbee_dimmer_salvando() {
    ?>
<div class="ui active dimmer salvando">
    <div class="ui loader slow orange medium elastic text">Salvando</div>
</div>
<?php
}

function hgodbee_dimmer_carregando($class = '') {
    ?>
<div class="ui active dimmer carregando <?php echo $class; ?>">
    <div class="ui loader slow violet medium elastic text">Carregando</div>
</div>
<?php
}