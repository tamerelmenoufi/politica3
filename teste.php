<?php
    include "lib/includes.php";
    exit();
    //Aqui seriam as atualizações de teste
    //Acrescendo mais uma linha de teste

    for($i=0;$i<100;$i++){

        set_time_limit(90);

        $situacao = ['tramitacao', 'retorno', 'concluido'];

        $query = "select * from local_fontes order by rand() limit 1";
        $result = mysqli_query($con, $query);
        $d = mysqli_fetch_object($result);

        $query = "select * from beneficiados order by rand() limit 1";
        $result = mysqli_query($con, $query);
        $d1 = mysqli_fetch_object($result);

        $query = "select * from assessores order by rand() limit 1";
        $result = mysqli_query($con, $query);
        $d2 = mysqli_fetch_object($result);

        $query = "select * from especialidades order by rand() limit 1";
        $result = mysqli_query($con, $query);
        $d3 = mysqli_fetch_object($result);

        $mes = rand(3,5);
        $dia = rand(1,29);

        $hora = rand(8,18);
        $minuto = rand(0,59);

        $agenda = date("Y-m-d", mktime($hora, $minuto, 0, (date("m") + $mes) , (date("d") + $dia), date("Y")));

        $query = "insert into servicos set
                                            tipo = '{$d->servico_tipo}',
                                            categoria = '{$d->categoria}',
                                            beneficiado = '{$d1->codigo}',
                                            assessor = '{$d2->codigo}',
                                            data_agenda = '{$agenda}',
                                            especialidade = '{$d3->codigo}',
                                            local_fonte =  '{$d->codigo}',
                                            data_pedido = NOW(),
                                            situacao = '".rand(0,2)."'
                ";
        mysqli_query($con, $query);



    }
    echo "OK";
?>