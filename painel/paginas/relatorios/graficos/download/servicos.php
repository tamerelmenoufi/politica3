<?php
    include "../../../../../lib/includes.php";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=servicos.csv');

    $md5  = md5(date("YmdHis").$_SERVER["PHP_SELF"]);
    $query = "SELECT b.tipo, COUNT(*) AS qt FROM servicos a LEFT JOIN servico_tipo b ON a.tipo = b.codigo GROUP BY a.tipo ORDER BY qt DESC";
    $result = mysqli_query($con, $query);
    $i=0;
    while($d = mysqli_fetch_object($result)){
        $rotulo[] = $d->tipo;
        $qt[] =  $d->qt;
        $lg[] = $d->tipo; //$Legenda[$i];
        $bg[] = $Bg[$i];
        $bd[] = $Bd[$i];
    $i++;
    }
    echo "Serviços;Quantidade\n";

      for($i = 0; $i < count($lg); $i++){
        echo "{$rotulo[$i]};{$qt[$i]}\n";
      }
?>
