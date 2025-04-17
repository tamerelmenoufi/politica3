<?php

include "config_beneficiados.php";

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

    $query = "INSERT INTO beneficiados SET {$attr}";

    if (mysqli_query($con, $query)) {
        $codigo = mysqli_insert_id($con);

        sis_logs('beneficiados', $codigo, $query);

        echo json_encode([
            'status' => true,
            'msg' => 'Salvo com sucesso',
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

        <form id="form-beneficiados">
            <h3>CADASTRO DE NOVO BENEFICIADO</h3>
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

            <div class="form-group">
                <label for="nome_mae">Nome da mãe <i class="text-danger">*</i></label>
                <input
                        type="text"
                        class="form-control"
                        id="nome_mae"
                        name="nome_mae"
                        value="<?= $d->nome_mae; ?>"
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
                <div class="col-md-6">
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cep">
                            CEP <i class="text-danger"></i>
                        </label>
                        <input
                                type="text"
                                class="form-control"
                                id="cep"
                                name="cep"
                                value="<?= $d->cep; ?>"
                        >

                    </div>
                </div>
            </div>

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

            <button type="submit" class="btn btn-success">Salvar</button>
        </form>

<script>
    $(function () {
        $('#cpf').mask('999.999.999-99');

        $('#cep').mask('99999-999');

        $('#telefone').mask('(99) 9 9999-9999');

        $('#municipio').selectpicker();

        $('#form-beneficiados').validate();

        $("span[Fechar]").click(function(){
            $("div[NovoCadastroBG]").css("display","none");
            $("div[NovoCadastro]").css("display","none");
            $("div[NovoCadastro]").html('');
            $("#beneficiado").val('');
            $("#beneficiado").selectpicker('refresh');
        });

        $("#cep").blur(function () {
            var cep = $(this).val().replace(/\D/g, '');

            if (cep != "") {
                //Expressão regular para validar o CEP.
                var validacep = /^[0-9]{8}$/;

                if (validacep.test(cep)) {
                    $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {
                        if (!("erro" in dados)) {
                            $("#endereco").val(`${dados.logradouro}, ${dados.bairro}`);
                        } //end if.
                        else {
                            $("#endereco").val("");
                        }
                    });
                }

            }
        });

        $('#form-beneficiados').submit(function (e) {
            e.preventDefault();

            if (!$(this).valid()) return false;

            var dados = $(this).serializeArray();


            $.ajax({
                url: '<?= $urlBeneficiados; ?>/novo.php',
                method: 'POST',
                data: dados,
                success: function (response) {
                    let retorno = JSON.parse(response);

                    if (retorno.status) {
                        tata.success('Sucesso', retorno.msg);

                        //$("#beneficiado").append('<option value="'+retorno.codigo+'">'+retorno.nome+'</option>');
                        //$("#beneficiado").selectpicker('refresh');
                        //$("#beneficiado").selectpicker('val', retorno.codigo);

                        $(".beneficiado").text(retorno.nome);
                        $("#beneficiado").val(retorno.codigo);


                        $("div[NovoCadastroBG]").css("display","none");
                        $("div[NovoCadastro]").css("display","none");
                        $("div[NovoCadastro]").html('');
                        // $.ajax({
                        //     url: '<?= $urlBeneficiados; ?>/visualizar.php',
                        //     data: {codigo: retorno.codigo},
                        //     success: function (response) {
                        //         $('#palco').html(response);
                        //     }
                        // })

                    } else {
                        tata.error('Error', retorno.msg);
                    }
                }
            })
        });
    });
</script>



