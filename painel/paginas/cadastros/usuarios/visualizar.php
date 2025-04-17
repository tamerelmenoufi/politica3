<?php
include "config_usuarios.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' and $_POST['acao'] === 'acesso_agenda') {
    $codigo = $_POST['codigo'];
    $acesso_agenda = $_POST['acesso_agenda'];

    $query = "UPDATE usuarios SET acesso_agenda = '{$acesso_agenda}' WHERE codigo = '{$codigo}'";

    if (mysqli_query($con, $query)) {
        echo json_encode(['status' => true, 'msg' => 'Acesso alterado com sucesso']);
    } else {
        echo json_encode(['status' => false, 'msg' => 'Error ao liberar acesso à agenda']);
    }
    exit;
}
$codigo = $_GET['codigo'];
$query = "SELECT u.* FROM usuarios u "
    . "WHERE u.codigo = '{$codigo}'";
$result = mysqli_query($con, $query);
$d = mysqli_fetch_object($result);

?>

<style>
    .custom-control-input:checked ~ .custom-control-label::before {
        background: #1cc88a;
    }
</style>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item" aria-current="page">
            <a href="#" url="<?= $urlUsuarios; ?>/index.php">Usuários</a>
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
            if ((in_array('Usuários - Editar', $ConfPermissoes) and $d->codigo != 1) or $_SESSION['usuario']['codigo'] == 1) {
                ?>
                <button
                        type="button"
                        class="btn btn-info btn-sm float-left"
                        url="<?= $urlUsuarios ?>/permissao.php?codigo=<?= $d->codigo; ?>"
                        style="margin-right: 2px"
                >
                    <i class="fa-solid fa-plus"></i> Permissão
                </button>
                <?php
            }
            if (in_array('Usuários - Cadastrar', $ConfPermissoes)) {
                ?>
                <button
                        type="button"
                        class="btn btn-success btn-sm float-left"
                        url="<?= $urlUsuarios ?>/form.php"
                        style="margin-right: 2px"
                >
                    <i class="fa-solid fa-plus"></i> Novo
                </button>
                <?php
            }
            if (in_array('Usuários - Editar', $ConfPermissoes)) {
                ?>
                <button
                        type="button"
                        class="btn btn-warning btn-sm float-left"
                        url="<?= $urlUsuarios ?>/form.php?codigo=<?= $codigo; ?>"
                        style="margin-right: 2px"
                >
                    <i class="fa-solid fa-pencil"></i> Editar
                </button>
                <?php
            }
            if (in_array('Usuários - Excluir', $ConfPermissoes) and $d->codigo != 1) {
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
            <div class="col-md-4 font-weight-bold">Usuário</div>
            <div class="col-md-8"><?= $d->usuario; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Criado em</div>
            <div class="col-md-8"><?= formata_datahora($d->data_cadastro, DATA_HM); ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Status</div>
            <div class="col-md-8">
                <?php
                $status = $d->status == '1' ? 'success' : 'danger';
                ?>
                <span class="badge badge-<?= $status; ?>">
                    <?= getSituacaoOptions($d->status); ?>
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Acesso a agenda</div>
            <div class="col-md-8">
                <div class="custom-control custom-switch">
                    <input
                            type="checkbox"
                            class="custom-control-input"
                            id="acesso_agenda"
                            data-codigo="<?= $codigo; ?>"
                        <?= $d->acesso_agenda === '1' ? ' checked' : ''; ?>
                    >
                    <label class="custom-control-label" for="acesso_agenda"></label>
                </div>
            </div>
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
                            url: '<?= $urlUsuarios;?>/index.php',
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
                                        url: '<?= $urlUsuarios; ?>/index.php',
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

    $('#acesso_agenda').change(function () {
        var codigo = $(this).data('codigo');
        var acesso_agenda = Number($(this).is(':checked'));

        $.ajax({
            url: '<?= $urlUsuarios?>/visualizar.php',
            method: 'POST',
            dataType: 'JSON',
            data: {
                codigo,
                acesso_agenda,
                acao: 'acesso_agenda',
            },
            success: function (data) {
                if (data.status) {
                    tata.success('Sucesso', data.msg);
                } else {
                    tata.error('Error', data.msg);
                }
            }
        })
    })
</script>
