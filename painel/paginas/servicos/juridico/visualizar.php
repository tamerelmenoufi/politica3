<?php
include "config_servicos.php";
$codigo = $_GET['codigo'];

$query = "SELECT s.*, a.nome AS assessor, b.nome AS beneficiado, lf.descricao AS local_fonte, st.tipo AS tipo FROM servicos s "
    . "LEFT JOIN assessores a ON a.codigo = s.assessor "
    . "LEFT JOIN beneficiados b ON b.codigo = s.beneficiado "
    . "LEFT JOIN local_fontes lf ON lf.codigo = s.local_fonte "
    . "LEFT JOIN servico_tipo st ON st.codigo = s.tipo "
    . "WHERE s.codigo = '{$codigo}'";

$result = mysqli_query($con, $query);
$d = mysqli_fetch_object($result);

?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item" aria-current="page">
            <a href="#" url="<?= $urlServicos; ?>/index.php">Serviços</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            Visualizar
        </li>
    </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-md-row flex-column align-items-center justify-content-md-between">
        <h6 class="m-0 font-weight-bold text-primary">
            Visualizar
        </h6>
        <div class="d-block">
            <?php
            if(in_array('Jurídico - Cadastrar', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-success btn-sm float-left"
                    url="<?= $urlServicos ?>/form.php"
                    style="margin-right: 2px"
            >
                <i class="fa-solid fa-plus"></i> Novo
            </button>
            <?php
            }
            if(in_array('Jurídico - Editar', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-warning btn-sm float-left"
                    url="<?= $urlServicos ?>/form.php?codigo=<?= $codigo; ?>"
                    style="margin-right: 2px"
            >
                <i class="fa-solid fa-pencil"></i> Editar
            </button>
            <?php
            }
            if(in_array('Jurídico - Logs', $ConfPermissoes)){
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
            if(in_array('Jurídico - Excluir', $ConfPermissoes)){
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
            <div class="col-md-4 font-weight-bold">Tipo de Serviço</div>
            <div class="col-md-8"><?= $d->tipo; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Assessor</div>
            <div class="col-md-8"><?= $d->assessor; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Beneficiado</div>
            <div class="col-md-8"><?= $d->beneficiado; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Fonte Local</div>
            <div class="col-md-8"><?= $d->local_fonte; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Contato</div>
            <div class="col-md-8"><?= $d->contato; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Especialista</div>
            <div class="col-md-8"><?= $d->especialista; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Data de agenda</div>
            <div class="col-md-8"><?= formata_datahora($d->data_agenda, DATA_HM); ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Data do pedido</div>
            <div class="col-md-8"><?= formata_datahora($d->data_pedido, DATA_HM); ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Situação</div>
            <div class="col-md-8"><?= getSituacaoOptions($d->situacao); ?></div>
        </div>
    </div>
</div>

<script>

    $(".btn-logs").click(function(){
        $.dialog({
            content:"url:paginas/servicos/logs/log_lista.php?codigo=<?=$codigo?>",
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
                            url: '<?= $urlServicos;?>/index.php',
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
                                        url: '<?= $urlServicos; ?>/index.php',
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
