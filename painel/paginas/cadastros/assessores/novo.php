<?php
include "config_assessores.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $attr = [];

    $codigo = $data['codigo'] ?: null;

    unset($data['codigo']);

    if (!$codigo) $data['data_cadastro'] = 'NOW()';

    foreach ($data as $name => $value) {
        $attr[] = "{$name} = '" . ($value) . "'";
    }

    $attr = implode(', ', $attr);

    $query = "INSERT INTO assessores SET {$attr}";

    if (mysqli_query($con, $query)) {
        $codigo = mysqli_insert_id($con);

        sis_logs('assessores', $codigo, $query);

        echo json_encode([
            'status' => true,
            'msg' => 'Dados salvo com sucesso',
            'nome' => $_POST['nome'],
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

?>
<style>
    .btn-fechar{
        position:absolute;
        right:20px;
        top:20px;
        cursor:pointer;
        font-size:20px;
    }
</style>

        <form id="form-assessores">
            <h3>CADASTRO DE NOVO ASSESSOR</h3>
            <span Fechar class="btn-fechar"><i class="fas fa-times"></i></span>
            <div class="form-group">
                <label for="nome">Nome <i class="text-danger">*</i></label>
                <input
                        type="text"
                        class="form-control"
                        id="nome"
                        name="nome"
                        value="<?= $d->nome; ?>"
                        required
                >
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cpf">CPF <i class="text-danger"></i></label>
                        <input
                                type="text"
                                class="form-control"
                                id="cpf"
                                name="cpf"
                                value="<?= $d->cpf; ?>"
                        >

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="data_nascimento">
                            Data de Nascimento <i class="text-danger">*</i>
                        </label>
                        <input
                                type="date"
                                class="form-control"
                                id="data_nascimento"
                                name="data_nascimento"
                                value="<?= $d->data_nascimento; ?>"
                                required
                        >

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sexo">Sexo <i class="text-danger">*</i></label>
                        <select
                                class="form-control"
                                id="sexo"
                                name="sexo"
                                required
                        >
                            <option value=""></option>
                            <?php foreach (getSexo() as $key => $sexo) : ?>
                                <option
                                    <?= ($codigo and $d->sexo == $key) ? "selected" : ""; ?>
                                        value="<?= $key; ?>"
                                >
                                    <?= $sexo; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="email">
                            E-Mail <i class="text-danger"></i>
                        </label>
                        <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?= $d->email; ?>"
                        >

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="telefone">
                            Telefone <i class="text-danger">*</i>
                        </label>
                        <input
                                type="text"
                                class="form-control"
                                id="telefone"
                                name="telefone"
                                value="<?= $d->telefone; ?>"
                                required
                        >

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="municipio">
                            Municipio <i class="text-danger">*</i>
                        </label>
                        <select
                                class="form-control"
                                id="municipio"
                                name="municipio"
                                data-live-search="true"
                                required
                        >
                            <option value=""></option>
                            <?php
                            $query = "SELECT * FROM municipios";
                            $result = mysqli_query($con, $query);

                            while ($m = mysqli_fetch_object($result)): ?>
                                <option
                                    <?= ($codigo and $d->municipio == $m->codigo) ? 'selected' : ''; ?>
                                        value="<?= $m->codigo ?>">
                                    <?= $m->municipio; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>

                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="endereco">
                            Endereco <i class="text-danger">*</i>
                        </label>
                        <input
                                type="text"
                                class="form-control"
                                id="endereco"
                                name="endereco"
                                value=" <?= $d->endereco; ?>"
                                required
                        >

                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="responsavel">
                    Responsavel <i class="text-danger">*</i>
                </label>
                <select
                        class="form-control"
                        id="responsavel"
                        name="responsavel"
                        required
                >
                    <option value=""></option>

                    <?php foreach (getResponsavel() as $key => $responsavel) : ?>
                        <option
                            <?= ($codigo and $d->responsavel == $key) ? "selected" : ""; ?>
                                value="<?= $key; ?>"
                        >
                            <?= $responsavel; ?>
                        </option>
                    <?php endforeach; ?>

                </select>

            </div>
            <input type="hidden" id="codigo" value="<?= $codigo; ?>">

            <button type="submit" class="btn btn-success">Salvar</button>
        </form>

<script>
    $(function () {
        $("#cpf").mask("999.999.999-99");

        $("#telefone").mask("(99) 9 9999-9999");

        $('#municipio').selectpicker();

        $('#form-assessores').validate();

        $("span[Fechar]").click(function(){
            $("div[NovoAssessorBG]").css("display","none");
            $("div[NovoAssessor]").css("display","none");
            $("div[NovoAssessor]").html('');
            $("#assessor").val('');
            $("#assessor").selectpicker('refresh');
        });

        $('#form-assessores').submit(function (e) {
            e.preventDefault();

            if (!$(this).valid()) return false;

            var codigo = $('#codigo').val();
            var dados = $(this).serializeArray();

            if (codigo) {
                dados.push({name: 'codigo', value: codigo})
            }

            $.ajax({
                url: '<?= $urlAssessores; ?>/novo.php',
                method: 'POST',
                data: dados,
                success: function (response) {
                    let retorno = JSON.parse(response);

                    if (retorno.status) {
                        tata.success('Sucesso', retorno.msg);

                        //$("#assessor").append('<option value="'+retorno.codigo+'">'+retorno.nome+'</option>');
                        //$("#assessor").selectpicker('refresh');
                        //$("#assessor").selectpicker('val', retorno.codigo);

                        $(".assessor").text(retorno.nome);
                        $("#assessor").val(retorno.codigo);

                        $("div[NovoAssessorBG]").css("display","none");
                        $("div[NovoAssessor]").css("display","none");
                        $("div[NovoAssessor]").html('');

                    } else {
                        tata.error('Error', retorno.msg);
                    }
                }
            })
        });
    });
</script>



