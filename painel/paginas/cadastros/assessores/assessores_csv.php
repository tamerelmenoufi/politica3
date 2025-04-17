<?php
    include "config_assessores.php";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=assessores.csv');

    if($_SESSION['campo_busca']){

        $where  = " and (
                                nome like '%{$_SESSION['campo_busca']}%'
                            or  responsavel like '%{$_SESSION['campo_busca']}%' 
                            or  cpf = '{$_SESSION['campo_busca']}'
                        )";
    
    }
    

    $query = "SELECT * FROM assessores WHERE deletado != '1' {$where}  order by nome";
    $result = mysqli_query($con, $query);
        echo "ASSESSOR(A);CPF;RESPONSÁVEL\n";
    while ($d = mysqli_fetch_object($result)):
        echo "{$d->nome};{$d->cpf};{$d->responsavel}\n";
    endwhile;