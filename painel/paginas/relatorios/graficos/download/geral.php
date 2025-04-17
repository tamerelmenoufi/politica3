<?php
    include "../../../../../lib/includes.php";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=geral.csv');

    $md5  = md5(date("YmdHis").$_SERVER["PHP_SELF"]);

    $query = "select b.tipo, count(*) as qt from servicos a left join servico_tipo b on a.tipo = b.codigo group by a.tipo ORDER BY qt DESC";
    $result = mysqli_query($con, $query);
    $i=0;
    while($d = mysqli_fetch_object($result)){
        $rotulo[] = $d->tipo;
        $qt[] =  $d->qt;
        $lg[] = $Legenda[$i];
        $bg[] = $Bg[$i];
        $bd[] = $Bd[$i];
    $i++;
    }
    echo "Legenda;Descrição;Quantidade\n";

      for($i = 0; $i < count($lg); $i++){
        echo "{$lg[$i]};{$rotulo[$i]};{$qt[$i]}\n";

      }

?>