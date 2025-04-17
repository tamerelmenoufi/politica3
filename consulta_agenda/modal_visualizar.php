<?php
include 'conf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' and $_POST['acao'] === 'atualizar_situacao') {
    $codigo = $_POST['codigo'];
    $situacao = $_POST['situacao'];

    $query = "UPDATE servicos SET situacao = '{$situacao}' WHERE codigo = '{$codigo}'";

    if (mysqli_query($con, $query)) {
        echo json_encode(['status' => true, 'situacao' => getSituacaoOptions($situacao)]);
    } else {
        echo json_encode(['status' => false]);
    }

    exit();
}
$codigo = $_GET['codigo'];

$query = "SELECT s.*, b.nome AS b_nome, cpf,nome_mae, data_nascimento, telefone, lf.descricao AS lf_descricao, st.tipo AS st_tipo FROM servicos s "
    . "LEFT JOIN beneficiados b ON b.codigo = s.beneficiado "
    . "LEFT JOIN local_fontes lf ON lf.codigo = s.local_fonte "
    . "LEFT JOIN servico_tipo st ON st.codigo = s.tipo "
    . "WHERE s.codigo = '{$codigo}'";

$result = mysqli_query($con, $query);

$d = mysqli_fetch_object($result);
?>


<div class="container-fluid">

    <div>
        <h5 class="card-title text-gray-800">Informações do beneficiado</h5>

        <div class="row">
            <div class="col-md-3">
                <span class="font-weight-bold">Nome</span>
            </div>
            <div class="col-md-9">
                <?= $d->b_nome; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <span class="font-weight-bold">Nome da mãe</span>
            </div>
            <div class="col-md-9">
                <?= $d->nome_mae; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <span class="font-weight-bold">CPF</span>
            </div>
            <div class="col-md-9">
                <?= $d->cpf; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <span class="font-weight-bold">Data de nasc.</span>
            </div>
            <div class="col-md-9">
                <?= formata_datahora($d->data_nascimento, DATA); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <span class="font-weight-bold">Contato</span>
            </div>
            <div class="col-md-9">
                <?= $d->telefone; ?>
            </div>
        </div>
    </div>

    <div class="mt-4 mb-4">
        <h5 class="card-title text-gray-800">Informações do Agendamento</h5>

        <div class="row">
            <div class="col-md-3">
                <span class="font-weight-bold">Situação</span>
            </div>
            <div class="col-md-9">
                <span text_situacao_<?= $codigo; ?>><?= getSituacaoOptions($d->situacao); ?></span>

                <div class="btn-group">
                    <button
                            type="button"
                            class="btn btn-success btn-sm dropdown-toggle"
                            data-toggle="dropdown"
                            aria-expanded="false"
                    >
                        Alterar
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item situacao" data-situacao="tramitacao" href="#">Tramitação</a>
                        <a class="dropdown-item situacao" data-situacao="retorno" href="#">Retorno</a>
                        <a class="dropdown-item situacao" data-situacao="concluido" href="#">Concluído</a>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <span class="font-weight-bold">Tipo de serviço</span>
            </div>
            <div class="col-md-9">
                <?= $d->st_tipo; ?>
            </div>
        </div>

        <?php if ($d->especialista): ?>
            <div class="row">
                <div class="col-md-3">
                    <span class="font-weight-bold">Especialista</span>
                </div>
                <div class="col-md-9">
                    <?= $d->especialista; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3">
                <span class="font-weight-bold">Local/Fonte</span>
            </div>
            <div class="col-md-9">
                <?= $d->lf_descricao; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <span class="font-weight-bold">Data da agenda</span>
            </div>
            <div class="col-md-9">
                <?= formata_datahora($d->data_agenda, DATA_HM) ?>
            </div>
        </div>

        <?php if ($d->detalhes): ?>
            <div class="row">
                <div class="col-md-3">
                    <span class="font-weight-bold">Detalhes</span>
                </div>
                <div class="col-md-9">
                    <?= ucfirst($d->detalhes); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<input type="hidden" id="codigo" value="<?= $codigo; ?>">

<script>
    $(function () {

        $('.situacao').click(function (e) {
            e.preventDefault();
            var codigo = $('#codigo').val();
            var situacao = $(this).data('situacao');
            var rotulo = $(this).text();

            $.ajax({
                url: 'modal_visualizar.php',
                method: 'POST',
                data: {
                    codigo,
                    situacao,
                    acao: 'atualizar_situacao'
                },
                success: function (response) {
                    let retorno = JSON.parse(response);

                    if (retorno.status) {
                        tata.success('Sucesso', 'Situação alterado com sucesso');
                        $(`span[text_situacao_${codigo}]`).text(rotulo);
                    } else {
                        tata.error('Error', 'Error ao alterar situação')
                    }
                }
            })

        });
    });
</script>
