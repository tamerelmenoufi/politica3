<?php
include "config_oficios.php";

$codigo = $_GET['codigo'];

if ($codigo) {
    $d = ListaLogs('oficios', $codigo);
}

?>
<style>
    .jconfirm .jconfirm-box div.jconfirm-closeIcon{
        right:35px;
    }
</style>
<div class="card shadow mb-4" style="margin:20px;">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Operação</th>
                    <th>Usuário</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($d as $ind => $val){
                ?>
                <tr>
                    <td><?=$val[0]?></td>
                    <td><?=$val[1]?></td>
                    <td><?=$val[2]?></td>
                    <td>
                        <button abrir cod="<?=$ind?>" class="btn btn-success">
                            <i class="fa fa-eye"></i>
                        </button>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(function () {

        $("button[abrir]").click(function(){
            indice = $(this).attr('cod');
            $.dialog({
                content:"url:<?= $urlOficios;?>/log.php?codigo=<?=$codigo?>&indice="+indice,
                title:false,
                columnClass:'col-md-8 col-md-offset-2'
            });
        });

    });
</script>



