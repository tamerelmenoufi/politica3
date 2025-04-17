<?php
include "config_oficios.php";
$codigo = $_GET['codigo'];

$query = "SELECT o.*, a.nome AS assessor, s.descricao AS secretaria FROM oficios o "
    . "LEFT JOIN assessores a ON a.codigo = o.assessor "
    . "LEFT JOIN secretarias s ON s.codigo = o.secretaria "
    . "WHERE o.codigo = '{$codigo}'";

$result = mysqli_query($con, $query);
$d = mysqli_fetch_object($result);

?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item" aria-current="page">
            <a href="#" url="<?= $urlOficios; ?>/index.php">Ofícios</a>
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
            if(in_array('Ofícios - Cadastrar', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-success btn-sm float-left"
                    url="<?= $urlOficios ?>/form.php"
                    style="margin-right: 2px"
            >
                <i class="fa-solid fa-plus"></i> Novo
            </button>
            <?php
            }
            if(in_array('Ofícios - Editar', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-warning btn-sm float-left"
                    url="<?= $urlOficios ?>/form.php?codigo=<?= $codigo; ?>"
                    style="margin-right: 2px"
            >
                <i class="fa-solid fa-pencil"></i> Editar
            </button>
            <?php
            }
            if(in_array('Ofícios - Logs', $ConfPermissoes)){
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
            if(in_array('Ofícios - Excluir', $ConfPermissoes)){
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
            <div class="col-md-4 font-weight-bold">Número</div>
            <div class="col-md-8"><?= $d->numero; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Assessor</div>
            <div class="col-md-8"><?= $d->assessor; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Esfera</div>
            <div class="col-md-8"><?= $d->esfera; ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Secretaria</div>
            <div class="col-md-8"><?= $d->secretaria; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Situação</div>
            <div class="col-md-8"><?= getSituacaoOptions($d->situacao); ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Anexo</div>
            <div class="col-md-8">
                <?php
                if (is_file("docs/{$d->codigo}.pdf")) {
                    echo '<button type="button" class="btn btn-info btn-sm visualizar_anexo" data-url="docs/' . $d->codigo . '.pdf">'
                        . '<i class="fa-solid fa-file"></i> Visualizar'
                        . '</button>';
                } else {
                    echo 'Nenhum arquivo anexado';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>

    $(".btn-logs").click(function(){
        $.dialog({
            content:"url:<?= $urlOficios;?>/log_lista.php?codigo=<?=$codigo?>",
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
                            url: '<?= $urlOficios;?>/index.php',
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
                                        url: '<?= $urlOficios; ?>/index.php',
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

    $('.visualizar_anexo').click(function () {
        var url = `<?= $urlOficios; ?>/${$(this).data('url')}`

        var iframe = `<iframe style="width: 100%; min-height: 500px" src="${url}"></iframe>`;

        $.dialog({
            title: false,
            content: iframe,
            columnClass: 'xlarge'
        });


    });
</script>
