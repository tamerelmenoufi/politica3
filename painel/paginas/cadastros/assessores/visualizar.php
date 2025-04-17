<?php
include "config_assessores.php";

$codigo = $_GET['codigo'];
$query = "SELECT a.*, m.municipio AS municipio FROM assessores a "
    . "LEFT JOIN municipios m ON m.codigo = a.municipio "
    . "WHERE a.codigo = '{$codigo}'";
$result = mysqli_query($con, $query);
$d = mysqli_fetch_object($result);

?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item" aria-current="page">
            <a href="#" url="<?= $urlAssessores; ?>/index.php">Assessores</a>
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
        <div class="d-md-flex justify-content-xl-center">
            <?php
            if(in_array('Assessores - Cadastrar', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-success btn-sm float-left"
                    url="<?= $urlAssessores ?>/form.php"
                    style="margin-right: 2px"
            >
                <i class="fa-solid fa-plus"></i> Novo
            </button>
            <?php
            }
            if(in_array('Assessores - Editar', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-warning btn-sm float-left"
                    url="<?= $urlAssessores ?>/form.php?codigo=<?= $codigo; ?>"
                    style="margin-right: 2px"
            >
                <i class="fa-solid fa-pencil"></i> Editar
            </button>
            <?php
            }
            if(in_array('Assessores - Excluir', $ConfPermissoes)){
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
            <div class="col-md-4 font-weight-bold">Nome</div>
            <div class="col-md-8"><?= $d->nome; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">CPF</div>
            <div class="col-md-8"><?= $d->cpf; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Data de nascimento</div>
            <div class="col-md-8"><?= formata_datahora($d->data_nascimento, DATA) ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Sexo</div>
            <div class="col-md-8"><?= getSexoOptions($d->sexo); ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">E-Mail</div>
            <div class="col-md-8"><?= $d->email; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Telefone</div>
            <div class="col-md-8"><?= $d->telefone; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Município</div>
            <div class="col-md-8"><?= $d->municipio; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Endereço</div>
            <div class="col-md-8"><?= $d->endereco; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Responsável</div>
            <div class="col-md-8"><?= $d->responsavel; ?></div>
        </div>
    </div>
</div>

<script>
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
                            url: '<?= $urlAssessores;?>/index.php',
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
                                        url: '<?= $urlAssessores; ?>/index.php',
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
