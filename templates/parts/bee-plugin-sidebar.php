<?php

function hgodbee_sidebar($terms = '') {
	?>
<div class="ui inverted vertical menu sidebar overlay">
    <div class="ui inverted segment">
        <div class="paleta-header _margin-top-20-100">
            <h3 class="ui inverted header">Paleta de Cores</h3>
        </div>
        <?php
if (!empty($terms)) {

		foreach ($terms as $term) {
			hgodbee_accordion($term);
		}

	} else {
		echo 'Josiane avisa: array vazio.';
	}
	?>
    </div>
</div>
<?php
}

function hgodbee_accordion($term) {
    //HGodBee::hb_var_dump($term, __CLASS__, __METHOD__, __LINE__, false);
	?>
<button class="accordion"><?php echo $term->name; ?></button>
<div class="panel">
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore
        magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
        consequat.</p>
</div>
<?php
}