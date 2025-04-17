<?php

    include_once "../lib/includes.php";


    $query = "SELECT * FROM `acao_social` where codigo = '{$_GET['codigo']}'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);

    $q = "select * from acao_social_tipo where codigo in(".($d->servicos?:'0').")";
    $r = mysqli_query($con, $q);
    $S = [];
    while($s = mysqli_fetch_object($r)){
        $S[] = $s->tipo;
    }

?>

<style>

</style>


    <div class="col-md-12">
        <div class="row">

            <div class="col-md-12">

                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <td>
                                <b class="small">Local</b><br>
                                <?=$d->local?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b class="small">Data</b><br>
                                <?=substr($d->data, 8,2)?>/<?=substr($d->data, 5,2)?>/<?=substr($d->data, 0,4)?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b class="small">Serviços</b><br>
                                <?=implode(', ',$S)?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b class="small">Descrição</b><br>
                                <?=$d->descricao?>
                            </td>
                        </tr>

                    </tbody>
                </table>



            </div>
        </div>
    </div>

<script>
    $(function(){




    })
</script>