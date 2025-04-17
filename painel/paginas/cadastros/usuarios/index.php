<?php
include "config_usuarios.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' and $_POST['acao'] === 'excluir') {
    $codigo = $_POST['codigo'];

    if (exclusao('usuarios', $codigo)) {
        echo json_encode(["status" => true, "msg" => "Registro excluído com sucesso"]);
    } else {
        echo json_encode(["status" => false, "msg" => "Error ao tentar excluír"]);
    }
    exit();
}

$query = "SELECT * FROM usuarios WHERE deletado = '0'".(($_SESSION['usuario']['codigo'] != 1)?" and codigo != 1":false);
$result = mysqli_query($con, $query);

?>

<!--<h1 class="h3 mb-2 text-gray-800">Usuários</h1>-->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item active" aria-current="page">Usuários</li>
    </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            Usuários
        </h6>
        <?php
        if (in_array('Usuários - Cadastrar', $ConfPermissoes)) {
            ?>
            <button type="button" class="btn btn-success btn-sm" url="paginas/cadastros/usuarios/form.php">
                <i class="fa-solid fa-plus"></i> Novo
            </button>
            <?php
        }
        ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">

            <table id="datatable" class="table" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>Usuário</th>
                    <th>Status</th>
                    <th class="mw-20">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($d = mysqli_fetch_object($result)):
                    $status = $d->status == '1' ? 'success' : 'danger';
                    ?>
                    <tr id="linha-<?= $d->codigo; ?>">
                        <td><?= $d->nome ?></td>
                        <td><?= $d->usuario; ?></td>
                        <td>
                        <span class="badge badge-<?= $status; ?>">
                            <?= getSituacaoOptions($d->status); ?>
                        </span>
                        </td>
                        <td>
                            <?php
                            if( (in_array('Usuários - Editar', $ConfPermissoes) and $d->codigo != 1) or $_SESSION['usuario']['codigo'] == 1){
                            ?>
                            <button
                                    class="btn btn-sm btn-link"
                                    url="<?= $urlUsuarios ?>/permissao.php?codigo=<?= $d->codigo ?>"
                            >
                                <i class="fa-solid fa-user-lock text-dark"></i>
                            </button>
                            <?php
                            }
                            //if(in_array('Usuários - Visualizar', $ConfPermissoes)){
                            ?>
                            <button
                                    class="btn btn-sm btn-link"
                                    url="<?= $urlUsuarios ?>/visualizar.php?codigo=<?= $d->codigo ?>"
                            >
                                <i class="fa-regular fa-eye text-info"></i>
                            </button>
                            <?php
                            //}
                            if (in_array('Usuários - Editar', $ConfPermissoes)) {
                                ?>
                                <button
                                        class="btn btn-sm btn-link"
                                        url="<?= $urlUsuarios ?>/form.php?codigo=<?= $d->codigo; ?>"
                                >
                                    <i class="fa-solid fa-pencil text-warning"></i>
                                </button>
                                <?php
                            }
                            if (in_array('Usuários - Excluir', $ConfPermissoes) and $d->codigo != 1) {
                                ?>
                                <button
                                        class="btn btn-sm btn-link btn-excluir"
                                        data-codigo="<?= $d->codigo ?>"
                                >
                                    <i class="fa-regular fa-trash-can text-danger"></i>
                                </button>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<script>
    $(function () {
        $("#datatable").DataTable();

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
                                    } else {
                                        tata.error('Error', retorno.msg);
                                    }

                                    $(`#linha-${codigo}`).remove();
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
    });
</script>