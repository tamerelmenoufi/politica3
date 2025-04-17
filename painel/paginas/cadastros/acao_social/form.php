<?php
include "config_acao_social.php";

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
        $query = "UPDATE acao_social SET {$attr} WHERE codigo = '{$codigo}'";
    } else {
        $query = "INSERT INTO acao_social SET {$attr}";
    }

    if (mysqli_query($con, $query)) {
        $codigo = $codigo ?: mysqli_insert_id($con);

        sis_logs('acao_social', $codigo, $query);

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
    $query = "SELECT * FROM acao_social WHERE codigo = '{$codigo}'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);
}

?>
<style>
    div[NovoAssessorBG]{
        position:fixed;
        left:0;
        bottom:0;
        width:100%;
        height:100%;
        z-index:999;
        background-color:#333;
        opacity:0.5;
        display:none;
        z-index:998;
    }
    div[NovoAssessor]{
        position:relative;
        z-index:999;
        background-color:#fff;
        padding:20px;
        padding:20px;
        border-radius:10px;
        display:none;
    }
</style>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item" aria-current="page">
            <a href="#" url="<?= $acaoSocial; ?>/index.php">Ação Social</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <?= $codigo ? 'Alterar' : 'Cadastrar'; ?>
        </li>
    </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <?= $codigo ? 'Alterar' : 'Cadastrar'; ?> Ação Social
        </h6>
    </div>
    <div class="card-body">
        <form id="form-acao-social">

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
                    <option value="novo">Novo Cadastro</option>
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
            <div NovoAssessorBG></div>
            <div NovoAssessor></div>

            <div class="form-group">
                <label for="local">Local <i class="text-danger">*</i></label>
                <input
                        type="text"
                        class="form-control"
                        id="local"
                        name="local"
                        value="<?= $d->local; ?>"
                        maxlength="255"
                        required
                >

            </div>

            <div class="form-group">
                <label for="servicos">Serviços <i class="text-danger">*</i></label>

                <?php
                $queryServicos = "SELECT * FROM acao_social_tipo ORDER BY tipo";
                $resultServico = mysqli_query($con, $queryServicos);

                $servicos_check = explode(',', $d->servicos);

                while ($dados_servico = mysqli_fetch_object($resultServico)):
                    $isChecked = (@in_array($dados_servico->codigo, $servicos_check));
                    ?>
                    <div class="form-check">
                        <input
                                class="form-check-input servicos"
                                type="checkbox"
                                id="servicos-<?= $dados_servico->codigo; ?>"
                                value="<?= $dados_servico->codigo; ?>"
                            <?= $isChecked ? 'checked' : ''; ?>

                        >
                        <label class="form-check-label" for="servicos-<?= $dados_servico->codigo; ?>">
                            <?= $dados_servico->tipo; ?>
                        </label>
                    </div>
                <?php endwhile; ?>

            </div>

            <div class="form-group">
                <label for="descricao">Descrição<i class="text-danger">*</i></label>
                <textarea id="descricao" name="descricao" class="form-control"><?= $d->descricao ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="data">Data <i class="text-danger">*</i></label>
                        <input
                                type="datetime-local"
                                class="form-control"
                                id="data"
                                name="data"
                                value="<?= $codigo ? strftime('%Y-%m-%dT%H:%M:%S', strtotime($d->data)) : ''; ?>"
                                required
                        >

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
        $("#assessor").selectpicker();

        $('#form-acao-social').validate();

        $("#assessor").change(function(){
            valor = $(this).val();
            if(valor === 'novo'){
                $.ajax({
                    url:"paginas/cadastros/assessores/novo.php",
                    success:function(dados){
                        $("div[NovoAssessor]").html(dados);
                        $("div[NovoAssessorBG]").css("display","block");
                        $("div[NovoAssessor]").css("display","block");
                    },
                    error:function(){
                        alert('Ocorreu um erro!');
                    }
                });
            }
        });

        $("div[NovoAssessorBG]").click(function(){
            $("div[NovoAssessorBG]").css("display","none");
            $("div[NovoAssessor]").css("display","none");
            $("div[NovoAssessor]").html('');
            $("#assessor").val('');
            $("#assessor").selectpicker('refresh');
        });



        $('#form-acao-social').submit(function (e) {
            e.preventDefault();

            if (!$(this).valid()) return false;

            var codigo = $('#codigo').val();
            var dados = $(this).serializeArray();

            if (codigo) {
                dados.push({name: 'codigo', value: codigo})
            }

            var servicos = [];

            $(".servicos").each(function (index, item) {
                console.log($(item).val());
                if ($(item).is(':checked')) {
                    servicos.push($(item).val());
                }
            });

            dados.push({name: 'servicos', value: servicos.join(',')})

            $.ajax({
                url: '<?= $acaoSocial; ?>/form.php',
                method: 'POST',
                data: dados,
                success: function (response) {
                    let retorno = JSON.parse(response);

                    if (retorno.status) {
                        tata.success('Sucesso', retorno.msg);

                        $.ajax({
                            url: '<?= $acaoSocial; ?>/visualizar.php',
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



