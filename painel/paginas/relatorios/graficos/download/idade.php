<?php
    include "../../../../../lib/includes.php";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=idade.csv');

$md5 = md5(date("YmdHis") . $_SERVER["PHP_SELF"]);

$ano_atual = date('Y');

$query = "SELECT SUM(IF((YEAR(CURRENT_DATE) - DATE_FORMAT(b.data_nascimento, '%Y')) < 18,1,0)) AS qt, 'Menor 18' AS descricao FROM
beneficiados b INNER JOIN servicos s ON s.beneficiado = b.codigo
UNION
SELECT SUM(IF((YEAR(CURRENT_DATE) - DATE_FORMAT(b.data_nascimento, '%Y')) BETWEEN 18 AND 25,1,0)) AS qt, 'Entre 18 e 25' AS descricao
FROM beneficiados b INNER JOIN servicos s ON s.beneficiado = b.codigo
UNION
SELECT SUM(IF((YEAR(CURRENT_DATE) - DATE_FORMAT(b.data_nascimento, '%Y')) BETWEEN 26 AND 35,1,0)) AS qt,  'Entre 26 e 35' AS descricao
FROM beneficiados b INNER JOIN servicos s ON s.beneficiado = b.codigo
UNION
SELECT SUM(IF((YEAR(CURRENT_DATE) - DATE_FORMAT(b.data_nascimento, '%Y')) BETWEEN 36 AND 45,1,0)) AS qt,  'Entre 36 e 45' AS descricao
FROM beneficiados b INNER JOIN servicos s ON s.beneficiado = b.codigo
UNION
SELECT SUM(IF((YEAR(CURRENT_DATE) - DATE_FORMAT(data_nascimento, '%Y')) >= 46,1,0)) AS qt, 'Maior 46' AS descricao
FROM beneficiados b INNER JOIN servicos s ON s.beneficiado = b.codigo  ORDER BY qt DESC

";
//"LEFT JOIN servicos s ON s.beneficiado = b.codigo";

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
echo "Idade;Quantidade\n";

    for ($i = 0; $i < count($lg); $i++) {
        echo "{$rotulo[$i]};{$qt[$i]}\n";
    }
?>
