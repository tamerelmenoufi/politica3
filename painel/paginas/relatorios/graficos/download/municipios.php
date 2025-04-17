<?php
    include "../../../../../lib/includes.php";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=municipios.csv');

$md5 = md5(date("YmdHis") . $_SERVER["PHP_SELF"]);

$query = "SELECT m.municipio AS descricao, COUNT(*) AS qt FROM municipios m "
    . "INNER JOIN beneficiados b ON b.municipio = m.codigo "
    . "INNER JOIN servicos s ON s.beneficiado = b.codigo "
    . "GROUP BY b.municipio ORDER BY qt DESC";

$result = mysqli_query($con, $query);
$i = 0;
while ($d = mysqli_fetch_object($result)) {
    $rotulo[] = $d->descricao;
    $qt[] = $d->qt;
    $lg[] = $d->descricao; //$Legenda[$i];
    $bg[] = $Bg[$i];
    $bd[] = $Bd[$i];
    $i++;
}
echo "Municípios;Quantidade\n";

    for ($i = 0; $i < count($lg); $i++) {
        echo "{$rotulo[$i]};{$qt[$i]}\n";
    }
    ?>
