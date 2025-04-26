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
<style>
    div[NovoCadastroBG]{
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
    div[NovoCadastro]{
        position:relative;
        z-index:999;
        background-color:#fff;
        padding:20px;
        padding:20px;
        border-radius:10px;
        display:none;
    }
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
    .assessor, .beneficiado{
        cursor:pointer;
    }
</style>
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
            <?= $codigo ? 'Alterar' : 'Cadastrar'; ?> Jurídico
        </h6>
    </div>
    <div class="card-body">
        <form id="form-servicos">

            <input type="hidden" id="tipo" name="tipo" value="8" />

            <?php
                $query = "SELECT * FROM especialidades where servico_tipo = '8' ORDER BY descricao";
                $result = mysqli_query($con, $query);
                if(mysqli_num_rows($result)){
            ?>

            <div class="form-group">
                <label for="especialidade">
                    Especialidade <i class="text-danger">*</i>
                </label>
                <select
                        class="form-control"
                        id="especialidade"
                        name="especialidade"
                        data-live-search="true"
                        data-none-selected-text="Selecione"
                        required
                >
                    <option value=""></option>
                    <?php

                    while ($b = mysqli_fetch_object($result)): ?>
                        <option
                            <?= ($codigo and $d->especialidade == $b->codigo) ? 'selected' : ''; ?>
                                value="<?= $b->codigo ?>">
                            <?= $b->descricao; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

            </div>
            <?php
                }
            ?>
            <div class="form-group">
                <label for="beneficiado">
                    Beneficiado <i class="text-danger">*</i>
                </label>
                <?php
                    $query = "SELECT * FROM beneficiados where codigo = '{$d->beneficiado}'";
                    $result = mysqli_query($con, $query);
                    $b = mysqli_fetch_object($result);
                ?>
                <div class="form-control beneficiado" ><?=$b->nome?></div>
                <input type="hidden" id="beneficiado" name="beneficiado" value="<?=$d->beneficiado?>" required>
                <?php
                /*
                ?>
                <select
                        class="form-control selectpicker"
                        id="beneficiado"
                        name="beneficiado"
                        data-live-search="true"
                        data-none-selected-text="Selecione"
                        required
                >
                    <option value=""></option>
                    <option value="novo">Novo Cadastro</option>
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
                <?php
                //*/
                ?>

            </div>

            <div NovoCadastroBG></div>
            <div NovoCadastro></div>

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
                <?php
                    $query = "SELECT * FROM assessores where codigo = '{$d->assessor}'";
                    $result = mysqli_query($con, $query);
                    $a = mysqli_fetch_object($result);
                ?>
                <div class="form-control assessor" ><?=$a->nome?></div>
                <input type="hidden" id="assessor" name="assessor" value="<?=$d->assessor?>" required>
                <?php
                    /*
                ?>                
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
                <?php
                    //*/
                ?>  
            </div>
            <div NovoAssessorBG></div>
            <div NovoAssessor></div>

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
                    $query = "SELECT * FROM local_fontes where servico_tipo = '8' and deletado != '1' ORDER BY descricao";
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
        
        $(".beneficiado").click(function(){

        JanelaPopUp = $.dialog({
            title:"Beneficiados",
            content:"url:paginas/cadastros/beneficiados/busca.php",
            columnClass:'col-md-8'
        })

        })

        $(".assessor").click(function(){

        JanelaPopUp = $.dialog({
            title:"Assessores",
            content:"url:paginas/cadastros/assessores/busca.php",
            columnClass:'col-md-8'
        })

        })


        //$('#contato').mask('(99) 99999-9999');


        //$("#assessor").selectpicker();
        //$("#beneficiado").selectpicker();

        /*
        $("#beneficiado").change(function(){
        valor = $(this).val();
        if(valor === 'novo'){
            $.ajax({
                url:"paginas/cadastros/beneficiados/novo.php",
                success:function(dados){
                    $("div[NovoCadastro]").html(dados);
                    $("div[NovoCadastroBG]").css("display","block");
                    $("div[NovoCadastro]").css("display","block");
                },
                error:function(){
                    alert('Ocorreu um erro!');
                }
            });
        }
        });
        //*/

        $("div[NovoCadastroBG]").click(function(){
        $("div[NovoCadastroBG]").css("display","none");
        $("div[NovoCadastro]").css("display","none");
        $("div[NovoCadastro]").html('');
        //$("#beneficiado").val('');
        //$("#beneficiado").selectpicker('refresh');
        });

        /*
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
        //*/

        $("div[NovoAssessorBG]").click(function(){
        $("div[NovoAssessorBG]").css("display","none");
        $("div[NovoAssessor]").css("display","none");
        $("div[NovoAssessor]").html('');
        //$("#assessor").val('');
        //$("#assessor").selectpicker('refresh');
        });


        $("#especialidade").selectpicker();

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



