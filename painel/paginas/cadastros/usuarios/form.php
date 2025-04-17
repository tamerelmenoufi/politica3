<?php
include "config_usuarios.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $attr = [];

    $codigo = $data['codigo'] ?: null;

    unset($data['codigo'], $data['senha_2']);

    if (!$codigo) $data['data_cadastro'] = date("Y-m-d H:i:s");

    if ($codigo and empty($data['senha'])) unset($data['senha']);

    foreach ($data as $name => $value) {
        if ($name == 'senha') {
            $attr[] = "{$name} = '" . md5($value) . "'";
        } else {
            $attr[] = "{$name} = '" . ($value) . "'";
        }
    }

    $attr = implode(', ', $attr);

    if ($codigo) {
        $query = "UPDATE usuarios SET {$attr} WHERE codigo = '{$codigo}'";
    } else {
        $query = "INSERT INTO usuarios SET {$attr}";
    }

    #file_put_contents("query.txt",$query);

    if (mysqli_query($con, $query)) {
        $codigo = $codigo ?: mysqli_insert_id($con);

        sis_logs('usuarios', $codigo, $query);

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
    $query = "SELECT * FROM usuarios WHERE codigo = '{$codigo}'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);
}

?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item" aria-current="page">
            <a href="#" url="<?= $urlUsuarios; ?>/index.php">Usuários</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <?= $codigo ? 'Alterar' : 'Cadastrar'; ?>
        </li>
    </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <?= $codigo ? 'Alterar' : 'Cadastrar'; ?> usuarios
        </h6>
    </div>
    <div class="card-body">
        <form id="form-usuarios">
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
                <label for="usuario">Usuário <i class="text-danger">*</i></label>
                <input
                        type="text"
                        class="form-control"
                        id="usuario"
                        name="usuario"
                        value="<?= $d->usuario; ?>"
                    <?= (($d->codigo == 1) ? 'readonly="readonly"' : false) ?>
                        required
                >
            </div>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="senha">Senha <i class="text-danger">*</i></label>
                        <input
                                type="password"
                                class="form-control"
                                id="senha"
                                name="senha"
                                value="<?= !$codigo ? $d->senha : ''; ?>"
                            <?= !$codigo ? 'required' : ''; ?>
                        >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="senha_2">Confirmar senha <i class="text-danger">*</i></label>
                        <input
                                type="password"
                                class="form-control"
                                id="senha_2"
                                name="senha_2"
                            <?= !$codigo ? 'required' : ''; ?>
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

        $('#form-usuarios').validate({
            rules: {
                senha: {
                    minlength: 4
                },
                senha_2: {
                    minlength: 4,
                    equalTo: '[name="senha"]'
                }
            },
            messages: {
                senha: {
                    minlength: 'Digite minímo 4 caracteres'
                },
                senha_2: {
                    minlength: 'Digite minímo 4 caracteres',
                    equalTo: 'As senhas não conferem'
                }
            }
        });

        //$("#cpf").mask("999.999.999-99");

        $("#telefone").mask("(99) 9999-9999");

        $('#municipio').selectpicker();

        $('#form-usuarios').validate();

        $('#form-usuarios').submit(function (e) {
            e.preventDefault();

            if (!$(this).valid()) return false;

            var codigo = $('#codigo').val();
            var dados = $(this).serializeArray();

            if (codigo) {
                dados.push({name: 'codigo', value: codigo})
            }

            $.ajax({
                url: '<?= $urlUsuarios; ?>/form.php',
                method: 'POST',
                data: dados,
                success: function (response) {
                    let retorno = JSON.parse(response);

                    if (retorno.status) {
                        tata.success('Sucesso', retorno.msg);

                        $.ajax({
                            url: '<?= $urlUsuarios; ?>/visualizar.php',
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



