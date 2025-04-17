<?php
include "config_oficios.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $attr = [];

    $codigo = $data['codigo'] ?: null;

    unset($data['codigo']);

    foreach ($data as $name => $value) {
        $attr[] = "{$name} = '" . ($value) . "'";
    }

    // if(!$codigo){
    //     $q = "select * from oficios_sequecia where ano = year(NOW())";
    //     $r = mysqli_query($con, $q);
    //     if(mysqli_num_rows($r)){
    //         $d = mysqli_fetch_object($r);
    //         $n = $d->numero;
    //         mysqli_query($con, "update oficios_sequecia set numero = (numero + 1) where codigo = '{$d->codigo}'");
    //     }else{
    //         $n = 1;
    //         mysqli_query($con, "insert into oficios_sequecia set numero = 2, ano = year(NOW())");
    //     }
    //     $attr[] = "numero = '{$n}/".date("y")."'";
    // }


    $attr = implode(', ', $attr);

    if ($codigo) {
        $query = "UPDATE oficios SET {$attr} WHERE codigo = '{$codigo}'";
    } else {
        $query = "INSERT INTO oficios SET {$attr}";
    }

    if (mysqli_query($con, $query)) {
        $codigo = $codigo ?: mysqli_insert_id($con);

        uploadPdf($codigo);

        sis_logs('oficios', $codigo, $query);

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
    $query = "SELECT * FROM oficios WHERE codigo = '{$codigo}'";
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
            <a href="#" url="<?= $urlOficios; ?>/index.php">Ofícios</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <?= $codigo ? 'Alterar' : 'Cadastrar'; ?>
        </li>
    </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <?= $codigo ? 'Alterar' : 'Cadastrar'; ?> Ofícios
        </h6>
    </div>
    <div class="card-body">
        <form id="form-oficios" enctype="multipart/form-data">

            <div class="form-group">
                <label for="numero">
                    Número <i class="text-danger">*</i>
                </label>
                <input
                        type="text"
                        name="numero"
                        id="numero"
                        class="form-control"
                        value="<?=$d->numero?>"
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


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="esfera">
                            Esfera <i class="text-danger">*</i>
                        </label>
                        <select
                                class="form-control"
                                id="esfera"
                                name="esfera"
                                required
                        >
                            <option value=""></option>
                            <?php
                            foreach (getEsfera() as $value): ?>
                                <option
                                    <?= ($codigo and $d->esfera == $value) ? 'selected' : ''; ?>
                                        value="<?= $value; ?>">
                                    <?= $value; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="secretaria">
                            Secretaria <i class="text-danger">*</i>
                        </label>
                        <div id="container-secretaria">
                            <select
                                    class="form-control secretaria"
                                    id="secretaria"
                                    name="secretaria"
                                    data-live-search="true"
                                    data-none-selected-text="Selecione"
                                    required
                            >
                                <option value=""></option>
                                <?php
                                if ($codigo):
                                    $query = "SELECT * FROM secretarias WHERE esfera = '{$d->esfera}' ORDER BY descricao";
                                    $result = mysqli_query($con, $query);

                                    while ($s = mysqli_fetch_object($result)): ?>
                                        <option
                                            <?= ($codigo and $d->secretaria == $s->codigo) ? 'selected' : ''; ?>
                                                value="<?= $s->codigo ?>">
                                            <?= $s->descricao; ?>
                                        </option>
                                    <?php
                                    endwhile;
                                endif;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea
                        id="descricao"
                        name="descricao"
                        class="form-control"
                        rows="5"
                ><?= $d->descricao; ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <input
                            id="input-id"
                            type="file"
                            name="file"
                            class="file"
                            data-preview-file-type="text"
                    >
                </div>
            </div>


            <div class="row">
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
                            <option value=""></option>
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

        $("#numero").mask("999/99");

        $("#input-id").fileinput({
            <?php if($codigo and is_file("docs/{$codigo}.pdf")):?>
            initialPreview: [
                '<?= $urlOficios; ?>/docs/<?= $codigo;?>.pdf'
            ],
            <?php endif; ?>
            maxFileCount: 0,
            showCaption: true,
            uploadExtraData: {
                'codigo': '',
            },
            showCancel: true,
            enableResumableUpload: true,
            initialPreviewAsData: true,
            overwriteInitial: false,
            fileType: "pdf",
            allowedFileExtensions: ['pdf'],
            initialCaption: "Selecione uma arquivo no formato pdf",
            language: 'pt-BR',
            theme: 'fas',
            showUpload: false,
            fileActionSettings: {
                showUpload: false
            },
            deleteUrl: "<?= $urlOficios; ?>/file-delete.php",
            initialPreviewConfig: [
                {caption: '<?= $codigo;?>.pdf', type: "pdf", size: "100%", width: "100%", key: 1},
            ],
        });

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


        $("#assessor").selectpicker();

        $(".secretaria").selectpicker();

        $('#form-oficios').validate();

        $('#form-oficios').submit(function (e) {

            e.preventDefault();

            if (!$(this).valid()) return false;

            var codigo = $('#codigo').val();

            var formData = new FormData(this);

            if (codigo) {
                formData.append('codigo', codigo);
            }

            $.ajax({
                url: '<?= $urlOficios; ?>/form.php',
                method: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    let retorno = JSON.parse(response);

                    if (retorno.status) {
                        tata.success('Sucesso', retorno.msg);

                        $.ajax({
                            url: '<?= $urlOficios; ?>/visualizar.php',
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

        $('#esfera').change(function () {
            var valor = $(this).val();

            $.ajax({
                url: '<?= $urlOficios; ?>/select_secretarias.php',
                data: {esfera: valor},
                success: function (response) {
                    $('#container-secretaria').html(response);
                }
            })
        });
    });
</script>



