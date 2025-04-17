<?php
include_once "config_servicos.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' and $_POST['acao'] === 'excluir') {
    $codigo = $_POST['codigo'];

    if (exclusao('servicos', $codigo)) {
        echo json_encode(["status" => true, "msg" => "Registro excluído com sucesso"]);
    } else {
        echo json_encode(["status" => false, "msg" => "Error ao tentar excluír"]);
    }
    exit;
}

$query = "SELECT s.*, a.nome AS assessor, b.nome AS beneficiado FROM servicos s "
    . "LEFT JOIN assessores a ON a.codigo = s.assessor "
    . "LEFT JOIN beneficiados b ON b.codigo = s.beneficiado "
    . "WHERE s.tipo = '6' AND s.deletado = '0' "
    . "ORDER BY s.codigo DESC";
$result = mysqli_query($con, $query);

?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item active" aria-current="page">Odontologia</li>
    </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            Serviços
        </h6>
        <?php
        if (in_array('Odontologia - Cadastrar', $ConfPermissoes)) {
            ?>
            <button type="button" class="btn btn-success btn-sm" url="<?= $urlServicos; ?>/form.php">
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
                    <th class="filterhead"></th>
                    <th class="filterhead"></th>
                    <th class="filterhead"></th>
                    <th class="filterhead">Situação</th>
                    <th class="filterhead"></th>
                </tr>
                <tr>
                    <th>Beneficiado</th>
                    <th>Assessor</th>
                    <th>Data da Agenda</th>
                    <th>Situação</th>
                    <th class="mw-20">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($d = mysqli_fetch_object($result)): ?>
                    <tr id="linha-<?= $d->codigo; ?>">
                        <td><?= $d->beneficiado; ?></td>
                        <td><?= $d->assessor; ?></td>
                        <td><?= formata_datahora($d->data_agenda, DATA_HM); ?></td>
                        <td><?= getSituacaoOptions($d->situacao); ?></td>
                        <td>
                            <button
                                    class="btn btn-sm btn-link"
                                    url="<?= $urlServicos ?>/visualizar.php?codigo=<?= $d->codigo ?>"
                            >
                                <i class="fa-regular fa-eye text-info"></i>
                            </button>
                            <?php
                            if (in_array('Odontologia - Editar', $ConfPermissoes)) {
                                ?>
                                <button
                                        class="btn btn-sm btn-link"
                                        url="<?= $urlServicos ?>/form.php?codigo=<?= $d->codigo; ?>"
                                >
                                    <i class="fa-solid fa-pencil text-warning"></i>
                                </button>
                                <?php
                            }
                            if (in_array('Odontologia - Excluir', $ConfPermissoes)) {
                                ?>
                                <button class="btn btn-sm btn-link btn-excluir" data-codigo="<?= $d->codigo ?>">
                                    <i class="fa-regular fa-trash-can text-danger"></i>
                                </button>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th>Beneficiado</th>
                    <th>Assessor</th>
                    <th>Data da Agenda</th>
                    <th>Situação</th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    $(function () {
        var table = $('#datatable').DataTable({
            "bLengthChange": false,
            "iDisplayLength": 15
        });

        $(".filterhead").each(function (i) {
            var select = $('<select><option value=""></option></select>')
                .appendTo($(this).empty())
                .on('change', function () {
                    var term = $(this).val();
                    table.column(i).search(term, false, false).draw();
                });
            table.column(i).data().unique().sort().each(function (d, j) {
                select.append('<option value="' + d + '">' + d + '</option>')
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