<?php
include "config_servicos.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $attr = [];

    $codigo = $data['codigo'] ?: null;

    unset($data['codigo']);

    foreach ($data as $name => $value) {
        $attr[] = "{$name} = '" . ($value) . "'";
    }

    $attr = implode(', ', $attr);

    if ($codigo) {
        $query = "UPDATE servicos SET {$attr} WHERE codigo = '{$codigo}'";
    } else {
        $query = "INSERT INTO servicos SET {$attr}";
    }

    if (mysqli_query($con, $query)) {
        $codigo = $codigo ?: mysqli_insert_id($con);

        sis_logs('servicos', $codigo, $query);

        echo json_encode([
            'status' => true,
            'msg' => 'Dados salvo com sucesso',
            'codigo' => $codigo,
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'msg' => 'Erro ao salvar',
            'codigo' => $codigo,
            'mysqli_error' => mysqli_error(),
        ]);
    }

    exit;
}

$codigo = $_GET['codigo'];

if ($codigo) {
    $query = "SELECT * FROM servicos WHERE codigo = '{$codigo}'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);
}

?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item" aria-current="page">
            <a href="#" url="<?= $urlServicos; ?>/index.php">Serviços</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <?= $codigo ? 'Alterar' : 'Cadastrar'; ?>
        </li>
    </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <?= $codigo ? 'Alterar' : 'Cadastrar'; ?> Serviços
        </h6>
    </div>
    <div class="card-body">
        <form id="form-servicos">

            <div class="form-group">
                <label for="tipo">
                    Tipo de Serviço <i class="text-danger">*</i>
                </label>
                <select
                        class="form-control"
                        id="tipo"
                        name="tipo"
                        data-live-search="true"
                        data-none-selected-text="Selecione"
                        required
                >
                    <option value=""></option>
                    <?php
                    $query = "SELECT * FROM servico_tipo ORDER BY tipo";
                    $result = mysqli_query($con, $query);

                    while ($s = mysqli_fetch_object($result)): ?>
                        <option
                            <?= ($codigo and $d->tipo == $s->codigo) ? 'selected' : ''; ?>
                                value="<?= $s->codigo ?>">
                            <?= $s->tipo; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

            </div>

            <div class="form-group">
                <label for="beneficiado">
                    Beneficiado <i class="text-danger">*</i>
                </label>
                <select
                        class="form-control"
                        id="beneficiado"
                        name="beneficiado"
                        data-live-search="true"
                        data-none-selected-text="Selecione"
                        required
                >
                    <option value=""></option>
                    <?php
                    $query = "SELECT * FROM beneficiados ORDER BY nome";
                    $result = mysqli_query($con, $query);

                    while ($b = mysqli_fetch_object($result)): ?>
                        <option
                            <?= ($codigo and $d->beneficiado == $b->codigo) ? 'selected' : ''; ?>
                                value="<?= $b->codigo ?>">
                            <?= $b->nome; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

            </div>

            <div class="form-group">
                <label for="contato">
                    Contato <i class="text-danger">*</i>
                </label>
                <input
                        type="text"
                        class="form-control"
                        id="contato"
                        name="contato"
                        value="<?= $d->contato; ?>"
                        required
                >

            </div>

            <div class="form-group">
                <label for="especialista">
                    Especialista <i class="text-danger">*</i>
                </label>
                <input
                        type="text"
                        class="form-control"
                        id="especialista"
                        name="especialista"
                        value="<?= $d->especialista; ?>"
                        required
                >

            </div>

            <div class="form-group">
                <label for="assessor">
                    Assessor <i class="text-danger">*</i>
                </label>
                <select
                        class="form-control"
                        id="assessor"
                        name="assessor"
                        data-live-search="true"
                        data-none-selected-text="Selecione"
                        required
                >
                    <option value=""></option>
                    <?php
                    $query = "SELECT * FROM assessores ORDER BY nome";
                    $result = mysqli_query($con, $query);

                    while ($a = mysqli_fetch_object($result)): ?>
                        <option
                            <?= ($codigo and $d->assessor == $a->codigo) ? 'selected' : ''; ?>
                                value="<?= $a->codigo ?>">
                            <?= $a->nome; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

            </div>

            <div class="form-group">
                <label for="local_fonte">
                    Fonte Local <i class="text-danger">*</i>
                </label>
                <select
                        class="form-control"
                        id="local_fonte"
                        name="local_fonte"
                        data-live-search="true"
                        data-none-selected-text="Selecione"
                        required
                >
                    <option value=""></option>
                    <?php
                    $query = "SELECT * FROM local_fontes ORDER BY descricao";
                    $result = mysqli_query($con, $query);

                    while ($l = mysqli_fetch_object($result)): ?>
                        <option
                            <?= ($codigo and $d->local_fonte == $l->codigo) ? 'selected' : ''; ?>
                                value="<?= $l->codigo ?>">
                            <?= $l->descricao; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="data_agenda">Data de Agenda <i class="text-danger"></i></label>
                        <input
                                type="datetime-local"
                                class="form-control"
                                id="data_agenda"
                                name="data_agenda"
                                value="<?= $codigo ? strftime('%Y-%m-%dT%H:%M:%S', strtotime($d->data_agenda)) : ''; ?>"
                        >

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="situacao">
                            Situação <i class="text-danger">*</i>
                        </label>
                        <select
                                class="form-control"
                                id="situacao"
                                name="situacao"
                                required
                        >
                            <?php
                            foreach (getSituacao() as $key => $value): ?>
                                <option
                                    <?= ($codigo and $d->situacao == $key) ? 'selected' : ''; ?>
                                        value="<?= $key; ?>">
                                    <?= $value; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <input type="hidden" id="codigo" value="<?= $codigo; ?>">

            <button type="submit" class="btn btn-success">Salvar</button>
        </form>
    </div>
</div>

<script>
    $(function () {
        $('#contato').mask('(99) 99999-9999');

        $("#assessor").selectpicker();

        $("#beneficiado").selectpicker();

        $("#tipo").selectpicker();

        $("#local_fonte").selectpicker();

        $('#form-servicos').validate();

        $('#form-servicos').submit(function (e) {
            e.preventDefault();

            if (!$(this).valid()) return false;

            var codigo = $('#codigo').val();
            var dados = $(this).serializeArray();

            if (codigo) {
                dados.push({name: 'codigo', value: codigo})
            }

            $.ajax({
                url: '<?= $urlServicos; ?>/form.php',
                method: 'POST',
                data: dados,
                success: function (response) {
                    let retorno = JSON.parse(response);

                    if (retorno.status) {
                        tata.success('Sucesso', retorno.msg);

                        $.ajax({
                            url: '<?= $urlServicos; ?>/visualizar.php',
                            data: {codigo: retorno.codigo},
                            success: function (response) {
                                $('#palco').html(response);
                            }
                        })
                    } else {
                        tata.error('Error', retorno.msg);
                    }
                }
            })
        });
    });
</script>



