<?php
include "config_permissoes.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET' and $_GET['acao'] === 'atualizar') {
    $codigo = $_GET['codigo'];
    itens($codigo);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' and $_POST['acao'] === 'excluir') {

    $codigo = $_POST['codigo'];

    $query = "DELETE FROM permissoes WHERE codigo = '{$codigo}'";

    if (mysqli_query($con, $query)) {
        sis_logs($codigo, $query, 'permissoes', 'permissão');

        echo json_encode(['status' => true, 'msg' => 'Registro excluído com sucesso']);
    } else {
        echo json_encode(['status' => false, 'msg' => 'Error ao excluír', 'mysqlError' => mysqli_error()]);
    }
    exit();
}


$codigo = $_GET['codigo'];

$query = "SELECT p1.*, p2.descricao AS vinculo FROM permissoes p1 "
    . "LEFT JOIN permissoes p2 ON p1.vinculo = p2.codigo "
    . "WHERE p1.codigo = '{$codigo}'";
$result = mysqli_query($con, $query);

$d = mysqli_fetch_object($result);

?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb shadow bg-gray-custom">
            <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
            <li class="breadcrumb-item" aria-current="page">
                <a href="#" url="<?= $urlPermissoes; ?>/index.php">Permissões</a>
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
                 if(in_array('Permissões - Cadastrar', $ConfPermissoes) or $_SESSION['usuario']['codigo'] == 1){
                ?>
                <button
                        type="button"
                        class="btn btn-success btn-sm float-left"
                        url="<?= $urlPermissoes ?>/form.php"
                        style="margin-right: 2px"
                >
                    <i class="fa-solid fa-plus"></i> Novo
                </button>
                <?php
                 }
                 if(in_array('Permissões - Editar', $ConfPermissoes) or $_SESSION['usuario']['codigo'] == 1){
                ?>
                <button
                        type="button"
                        class="btn btn-warning btn-sm float-left"
                        url="<?= $urlPermissoes ?>/form.php?codigo=<?= $codigo; ?>"
                        style="margin-right: 2px"
                >
                    <i class="fa-solid fa-pencil"></i> Editar
                </button>
                <?php
                 }
                 if(in_array('Permissões - Excluir', $ConfPermissoes) or $_SESSION['usuario']['codigo'] == 1){
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
                <div class="col-md-4 font-weight-bold">Descrição</div>
                <div class="col-md-8"><?= $d->descricao; ?></div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Itens
            </h6>
            <?php
            if(in_array('Ofícios - Cadastrar', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-sm btn-success adicionar-item"
            >
                <i class="fa-solid fa-plus"></i> Adicionar
            </button>
            <?php
            }
            ?>
        </div>

        <div class="card-body card-body-itens">
            <?= itens($d->codigo); ?>
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
                                url: '<?= $urlPermissoes;?>/index.php',
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
                                            url: '<?= $urlPermissoes; ?>/index.php',
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

        $('.adicionar-item').click(function () {
            dialogItem = $.dialog({
                title: 'Cadastrar item',
                content: 'url: <?= $urlPermissoes; ?>/form_item.php?vinculo=<?= $d->codigo;?>',
                columnClass: 'medium'
            })
        });
    </script>

<?php

function itens($codigo)
{
    global $urlPermissoes;
    ?>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">Descrição</th>
            <th scope="col" style="width: 20%">Ação</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $queryItem = "SELECT * FROM permissoes WHERE vinculo = '{$codigo}'";
        $resultItem = mysqli_query($con, $queryItem);

        while ($dItem = mysqli_fetch_object($resultItem)): ?>
            <tr id="linha-<?= $dItem->codigo; ?>">
                <td><?= $dItem->descricao; ?></td>
                <td>
                    <button
                            type="button"
                            class="btn btn-danger btn-excluir-item btn-sm float-left"
                            data-codigo="<?= $dItem->codigo; ?>"
                    >
                        <i class="fa-regular fa-trash-can"></i> Excluir
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        $(function () {
            $('.btn-excluir-item').click(function () {
                var codigo = $(this).data('codigo');

                $.confirm({
                    title: 'Confirmar',
                    content: 'Deseja excluir este item?',
                    columnClass: 'medium',
                    type: 'red',
                    buttons: {
                        sim: {
                            text: 'Sim',
                            btnClass: 'btn-red',
                            action: function () {
                                $.ajax({
                                    url: '<?= $urlPermissoes; ?>/visualizar.php',
                                    method: 'POST',
                                    data: {codigo, acao: 'excluir'},
                                    success: function (response) {
                                        let retorno = JSON.parse(response);

                                        if (retorno.status) {
                                            tata.success('Successo', retorno.msg);
                                            $(`#linha-${codigo}`).remove();
                                        } else {
                                            tata.error('Aviso', retorno.msg);
                                        }
                                    }
                                });
                            }
                        },
                        nao: {
                            text: 'Não',
                            action: function () {

                            }
                        }
                    }
                });
            });
        });
    </script>
    <?php
}
