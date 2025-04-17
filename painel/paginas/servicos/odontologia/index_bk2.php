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
            </table>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#datatable thead tr')
            .clone(true)
            .addClass('filters')
            .appendTo('#datatable thead');

        var table = $('#datatable').DataTable({
            orderCellsTop: true,
            initComplete: function () {
                var api = this.api();

                // For each column
                api
                    .columns()
                    .eq(0)
                    .each(function (colIdx) {


                        var cell = $('.filters th')
                            .eq($(api.column(colIdx).header()).index());

                        var title = $(cell).text();

                        if (colIdx != 3) {
                            $(cell).html('<input class="form-control" type="text" placeholder="" />');


                            $('input', $('.filters th').eq($(api.column(colIdx).header()).index()))
                                .off('keyup change')
                                .on('keyup change', function (e) {
                                    e.stopPropagation();

                                    $(this).attr('title', $(this).val());
                                    var regexr = '({search})';

                                    var cursorPosition = this.selectionStart;

                                    api
                                        .column(colIdx)
                                        .search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '',
                                            this.value != '',
                                            this.value == ''
                                        )
                                        .draw();

                                    $(this)
                                        .focus()[0]
                                        .setSelectionRange(cursorPosition, cursorPosition);
                                });
                        } else {
                            let select = $(cell).html('<select class="form-control"><option value="">ede</option></select>');

                            $('select', $('.filters th').eq($(api.column(colIdx).header()).index()))
                                .on('change', function (e) {
                                    e.stopPropagation();

                                    $(this).attr('title', $(this).val());
                                    var regexr = '({search})';

                                    console.log($(this).attr('title', $(this).val()));
                                    var cursorPosition = this.selectionStart;

                                    api
                                        .column(colIdx)
                                        .search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '',
                                            this.value != '',
                                            this.value == ''
                                        )
                                        .draw();

                                    $(this)
                                        .focus()[0]
                                        .setSelectionRange(cursorPosition, cursorPosition);
                                });

                            var html = '<option value=""></option>';
                            api.column(colIdx).data().unique().sort().each(function (key, value) {
                                html += '<option value="' + key + '">' + key + '</option>';

                                $(select.html()).find('option').remove().append(html);
                            });

                            $(select.html()).find('option').remove();
                        }
                    });


            },
        });

        /*$(".filters th").each(function (i) {
            var select = $('<select><option value=""></option></select>')
                .appendTo($(this).empty())
                .on('change', function () {
                    var term = $(this).val();
                    table.column(i).search(term, false, false).draw();
                });
            table.column(i).data().unique().sort().each(function (d, j) {
                select.append('<option value="' + d + '">' + d + '</option>')
            });
        });*/

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