<?php
    include "../../../../../lib/includes.php";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=sexo.csv');

$md5 = md5(date("YmdHis") . $_SERVER["PHP_SELF"]);

$colunaDescricao = "(CASE WHEN b.sexo = 'm' THEN 'Masculino' WHEN b.sexo = 'f' THEN 'Feminino' END) AS descricao";
$query = "SELECT {$colunaDescricao}, count(*) as qt from beneficiados b "
    . "INNER JOIN  servicos s on s.beneficiado = b.codigo "
    . "group by b.sexo ORDER BY qt DESC";

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

echo "Sexo;Quantidade\n";

    for ($i = 0; $i < count($lg); $i++) {

echo "{$rotulo[$i]};{$qt[$i]}\n";

}
    ?>
