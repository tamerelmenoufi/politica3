<?php
include "../lib/includes.php";

$servico_tipo = $_GET['servico_tipo'];

$query = "SELECT * FROM especialidades WHERE servico_tipo = '{$servico_tipo}'";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result)):
    echo '<option value=""></option>';
    while ($d = mysqli_fetch_object($result)):?>
        <option value="<?= $d->codigo; ?>"><?= $d->descricao; ?></option>
    <?php endwhile;
endif;
?>