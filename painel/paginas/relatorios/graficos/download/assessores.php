<?php
    include "../../../../../lib/includes.php";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=assessores.csv');

    $md5  = md5(date("YmdHis").$_SERVER["PHP_SELF"]);

    $query = "SELECT a.nome AS descricao, COUNT(*) AS qt FROM servicos s "
    ."INNER JOIN assessores a ON a.codigo = s.assessor "
    ."GROUP BY a.nome ORDER BY qt DESC";

    #$query = "select b.tipo, count(*) as qt from servicos a left join servico_tipo b on a.tipo = b.codigo group by a.tipo";
    $result = mysqli_query($con, $query);
    $n = mysqli_num_rows($result);

    $i=0;
    while($d = mysqli_fetch_object($result)){
        $rotulo[] = $d->descricao;
        $qt[] =  $d->qt;
        $lg[] = $d->descricao; //$Legenda[$i];
        $bg[] = $Bg[$i];
        $bd[] = $Bd[$i];
    $i++;
    }
    echo "Assessores;Quantidade\n";

      for($i = 0; $i < count($lg); $i++){
        echo "{$rotulo[$i]};{$qt[$i]}\n";

      }
    ?>
