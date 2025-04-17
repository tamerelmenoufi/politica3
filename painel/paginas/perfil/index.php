<?php
include '../../../lib/includes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;

    $codigo = $_SESSION['usuario']['codigo'];

    unset($data['senha_2']);

    $attr = [];

    if ($data['senha']) unset($data['senha']);

    foreach ($data as $key => $value):
        $attr[] = "{$key} = '{$value}'";
    endforeach;

    $colunas = implode(', ', $attr);

    $query = "UPDATE usuarios SET {$colunas} WHERE codigo = '{$codigo}'";
    if (mysqli_query($con, $query)) {
        $_SESSION['usuario']['usuario'] = $data['usuario'];
        echo json_encode(["status" => true, "msg" => "Dados alterado com sucesso"]);
    } else {
        echo json_encode(["status" => false, "msg" => "Error ao tentar alterar", 'mysqlError' => mysqli_error()]);
    }

    exit();
}

$query = "SELECT * FROM usuarios WHERE codigo = '{$_SESSION['usuario']['codigo']}'";
$d = mysqli_fetch_object(mysqli_query($con, $query));


?>

<div class="card">
    <div class="card-header">
        Perfil
    </div>
    <div class="card-body">
        <div class="d-md-flex justify-content-md-center">
            <div class="col-md-6">
                <ul class="nav nav-pills mb-5" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a
                                class="nav-link active"
                                id="pills-home-tab"
                                data-toggle="pill"
                                href="#pills-home"
                                role="tab"
                                aria-controls="pills-home"
                                aria-selected="true">
                            Geral
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a
                                class="nav-link"
                                id="pills-profile-tab"
                                data-toggle="pill"
                                href="#pills-profile"
                                role="tab"
                                aria-controls="pills-profile"
                                aria-selected="false"
                        >Contato</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact"
                           role="tab"
                           aria-controls="pills-contact" aria-selected="false">Configuração</a>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                         aria-labelledby="pills-home-tab">
                        <form id="form-perfil">

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
                                <label for="nome">Usuário <i class="text-danger">*</i></label>
                                <input
                                        type="text"
                                        class="form-control"
                                        id="usuario"
                                        name="usuario"
                                        value="<?= $d->usuario; ?>"
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
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="senha_2">Confirmar senha <i
                                                    class="text-danger">*</i></label>
                                        <input
                                                type="password"
                                                class="form-control"
                                                id="senha_2"
                                                name="senha_2"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button class="btn btn-success float-right">Salvar</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                         aria-labelledby="pills-profile-tab">
                        <h3>Contato</h3>
                    </div>
                    <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                         aria-labelledby="pills-contact-tab">
                        <h3>Configuração</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#form-perfil').validate({
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

        $('#form-perfil').submit(function (e) {
            e.preventDefault();

            var dados = $(this).serializeArray();

            $.ajax({
                url: 'paginas/perfil/index.php',
                method: 'POST',
                data: dados,
                success: function (response) {
                    let retorno = JSON.parse(response);

                    if (retorno.status) {
                        tata.success("Aviso", retorno.msg);
                    } else {
                        console.log(retorno.mysqlError);
                        tata.error("Aviso", retorno.msg);
                    }
                }
            });
        });
    });
</script>
