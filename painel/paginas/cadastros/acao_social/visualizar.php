<?php
include "config_acao_social.php";
$codigo = $_GET['codigo'];
$query = "SELECT ac.*, a.nome AS assessor FROM acao_social ac "
    . "LEFT JOIN assessores a ON a.codigo = ac.assessor "
    . "WHERE ac.codigo = '{$codigo}'";
$result = mysqli_query($con, $query);
$d = mysqli_fetch_object($result);

?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item" aria-current="page">
            <a href="#" url="<?= $acaoSocial; ?>/index.php">Ação Social</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            Visualizar
        </li>
    </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            Visualizar
        </h6>
        <div class="d-block">
            <?php
            if(in_array('Ação Social - Cadastrar', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-success btn-sm float-left"
                    url="<?= $acaoSocial ?>/form.php"
                    style="margin-right: 2px"
            >
                <i class="fa-solid fa-plus"></i> Novo
            </button>
            <?php
            }
            if(in_array('Ação Social - Editar', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-warning btn-sm float-left"
                    url="<?= $acaoSocial ?>/form.php?codigo=<?= $codigo; ?>"
                    style="margin-right: 2px"
            >
                <i class="fa-solid fa-pencil"></i> Editar
            </button>
            <?php
            }
            if(in_array('Ação Social - Logs', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-info btn-logs btn-sm float-left"
                    data-codigo="<?= $codigo; ?>"
            >
                <i class="fa-solid fa-clock-rotate-left"></i> Logs
            </button>
            <?php
            }
           if(in_array('Ação Social - Excluir', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-danger btn-excluir btn-sm float-left"
                    data-codigo="<?= $codigo; ?>"
            >
                <i class="fa-regular fa-trash-can"></i> Excluir
            </button>
            <?php
            }
            ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 font-weight-bold">Data</div>
            <div class="col-md-8">
                <span class="badge badge-pill badge-success"><?= formata_datahora($d->data, DATA_HM); ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Assessor</div>
            <div class="col-md-8"><?= $d->assessor; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Local</div>
            <div class="col-md-8"><?= $d->local; ?></div>
        </div>

        <div class="row">
            <div class="col-md-12 font-weight-bold">Serviço</div>
            <div class="col-md-12">
                <?php
                $queryServicos = "SELECT * FROM acao_social_tipo WHERE codigo IN({$d->servicos}) ORDER BY tipo";
                $resultServicos = mysqli_query($con, $queryServicos);

                $servicosDescricao = [];

                if (mysqli_num_rows($resultServicos)):
                    while ($dadosServicos = mysqli_fetch_object($resultServicos)):
                        $servicosDescricao[] = $dadosServicos->tipo;
                    endwhile;

                    echo implode(', ', $servicosDescricao);
                endif;
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 font-weight-bold">Descrição</div>
            <div class="col-md-12">
                <?=$d->descricao?>
            </div>
        </div>
    </div>
</div>

<script>

    $(".btn-logs").click(function(){
        $.dialog({
            content:"url:<?= $acaoSocial;?>/log_lista.php?codigo=<?=$codigo?>",
            title:false,
            columnClass:'col-md-10 col-md-offset-1'
        });
    });

    $('.btn-excluir').click(function () {
        var codigo = $(this).data('codigo');

        $.confirm({
            title: 'Aviso',
            content: 'Deseja excluir este registro?',
            type: 'red',
            icon: 'fa fa-warning',
            buttons: {
                sim: {
                    text: 'Sim',
                    btnClass: 'btn-red',
                    action: function () {
                        $.ajax({
                            url: '<?= $acaoSocial;?>/index.php',
                            method: 'POST',
                            data: {
                                acao: 'excluir',
                                codigo
                            },
                            success: function (response) {
                                let retorno = JSON.parse(response);

                                if (retorno.status) {
                                    tata.success('Sucesso', retorno.msg);

                                    $.ajax({
                                        url: '<?= $acaoSocial; ?>/index.php',
                                        success: function (response) {
                                            $('#palco').html(response);
                                        }
                                    });
                                } else {
                                    tata.error('Error', retorno.msg);
                                }
                            }
                        })
                    }
                },
                nao: {
                    text: 'Não'
                }
            }
        })
    });
</script>
