<?php
include_once "config_oficios.php";

$esfera = $_GET['esfera'];
$query = "SELECT * FROM secretarias WHERE esfera = '{$esfera}'";
$result = mysqli_query($con, $query);

?>

<select
        class="form-control secretaria"
        id="secretaria"
        name="secretaria"
        data-live-search="true"
        data-none-selected-text="Selecione"
        required
>
    <?php while ($d = mysqli_fetch_object($result)): ?>

        <option value=""></option>
        <?php
        $query = "SELECT * FROM secretarias WHERE esfera = '{$esfera}' ORDER BY descricao";
        $result = mysqli_query($con, $query);

        while ($s = mysqli_fetch_object($result)): ?>
            <option
                <?= ($codigo and $d->secretaria == $s->codigo) ? 'selected' : ''; ?>
                    value="<?= $s->codigo ?>">
                <?= $s->descricao; ?>
            </option>
        <?php endwhile; ?>


    <?php endwhile;
    ?>
</select>

<script>
    $(function () {
        $(".secretaria").selectpicker();
    })
</script>

