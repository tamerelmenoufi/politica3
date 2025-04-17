<?php

include_once "../lib/includes.php";

?>

<style>
    .abrir_detalhes{
        cursor: pointer;
    }
</style>


    <div class="col-md-12">
        <div class="row">

            <div class="col-md-12">

                <h4>Eventos de Ação Social em <?=substr($_GET['data'], 8,2)?>/<?=substr($_GET['data'], 5,2)?>/<?=substr($_GET['data'], 0,4)?></h4>

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Local</th>
                            <th>Serviços</th>
                        </tr>
                    </thead>
                    <tbody>

            <?php
                    $query = "SELECT * FROM `acao_social` where data like '%{$_GET['data']}%'";
                    $result = mysqli_query($con, $query);
                    while($d = mysqli_fetch_object($result)){

                        $q = "select * from acao_social_tipo where codigo in(".($d->servicos?:'0').")";
                        $r = mysqli_query($con, $q);
                        $S = [];
                        while($s = mysqli_fetch_object($r)){
                            $S[] = $s->tipo;
                        }

            ?>

                        <tr class="abrir_detalhes" codigo="<?=$d->codigo?>">
                            <td><?=$d->local?></td>
                            <td><?=implode(', ',$S)?></td>
                        </tr>
            <?php
                    }
            ?>
                    </tbody>
                </table>



            </div>
        </div>
    </div>

<script>
    $(function(){

        $(".abrir_detalhes").click(function(){
            codigo = $(this).attr("codigo");
            dialogDefineData = $.dialog({
                    title: 'Ação Social',
                    content: `url: acao_social_detalhes.php?codigo=${codigo}`,
                    theme: 'bootstrap',
                    columnClass: 'medium'
                });
        })


    })
</script>