<?php
include "config_usuarios.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $permissoes = $_POST['permissoes'];
    $codigo = $_POST['codigo'];

    $query = "UPDATE usuarios SET permissoes = '{$permissoes}' WHERE codigo = '{$codigo}'";

    if (mysqli_query($con, $query)) {
        sis_logs('usuarios', $codigo, $query);

        echo json_encode(['status' => true, 'msg' => 'Permissões salvas com sucesso']);
    } else {
        echo json_encode(['status' => false, 'msg' => 'Error ao alterar permissões', 'mysqlError' => mysqli_error()]);
    }

    exit();
}

$codigo = $_GET['codigo'];

$query = "SELECT u.* FROM usuarios u WHERE u.codigo = '{$codigo}'";
$result = mysqli_query($con, $query);
$d = mysqli_fetch_object($result);

$usuario_permissao = explode(',', $d->permissoes);
?>

<link rel="stylesheet" href="<?= $caminho_vendor; ?>/checkbox-tree/css/styles.css">

<style>
    .checkbox {
        margin: 0 5px;
    }
</style>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item" aria-current="page">
            <a href="#" url="<?= $urlUsuarios; ?>/index.php">Usuários</a>
        </li>
        <li class="breadcrumb-item" aria-current="page">
            <a href="#" url="<?= $urlUsuarios; ?>/visualizar.php?codigo=<?= $codigo; ?>"><?= '#' . $codigo ?></a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            Permissão
        </li>
    </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-md-row flex-column align-items-center justify-content-md-between">
        <h6 class="m-0 font-weight-bold text-primary">
            Permissão
        </h6>
    </div>
    <div class="card-body">
        <?php

        function checkbox($name)
        {
            global $usuario_permissao;
            $isChecked = (in_array($name, $usuario_permissao)) ? 'checked' : '';
            return '<input class="checkbox" data-name="' . $name . '" id="' . $name . '" type="checkbox" ' . $isChecked . '/><label for="' . $name . '">' . $name . '</label>';
        }

        function arvore($arr)
        {
            $html = "<ul>";

            foreach ($arr as $k => $v) {
                if (is_array($v)) {
                    $html .= "<li> " . $k;
                    $html .= arvore($v);
                    $html .= "</li>";
                } else {
                    $html .= "<li>" . checkbox($v) . "</li>";
                }
            }

            $html .= "</ul>";
            return $html;
        }


        /*$permissao = [
            'Cadastros' => [
                'Assessores' => [
                    'Assessores - Visualizar',
                    'Assessores - Cadastrar',
                    'Assessores - Alterar',
                    'Assessores - Excluir'
                ],
                'Beneficiados'
            ],
            'Solititações' => [
                'Certidão de Nacimento',
                'Registro geral',
                'CRAS',
            ],
            'Eventos' => [
                'Ação Social',
                'Oficios'
            ],
            'Gerenciador'
        ];*/

        $queryPerm = "SELECT * FROM permissoes ORDER BY descricao";
        $resultPerm = mysqli_query($con, $queryPerm);
        $permissao = [];
        while ($dadosPerm = mysqli_fetch_object($resultPerm)) {
            if (!$dadosPerm->vinculo) {

                $queryVinc = "SELECT * FROM permissoes WHERE vinculo = '{$dadosPerm->codigo}'";
                $resultVinc = mysqli_query($con, $queryVinc);

                if (!mysqli_num_rows($resultVinc)) {
                    $permissao[] = $dadosPerm->descricao;
                }

                while ($dVinc = mysqli_fetch_object($resultVinc)) {
                    $permissao[$dadosPerm->descricao][$dVinc->descricao] = [
                        "{$dVinc->descricao} - Visualizar",
                        "{$dVinc->descricao} - Cadastrar",
                        "{$dVinc->descricao} - Editar",
                        "{$dVinc->descricao} - Logs",
                        "{$dVinc->descricao} - Excluir",
                    ];
                }
            }


        }
        ?>

        <ul class="checktree">
            <?php
            echo arvore($permissao);
            ?>
        </ul>

        <input type="hidden" value="<?= $codigo; ?>" id="codigo">
        <div class="form-group">
            <button type="button" class="btn btn-success float-right salvar">Salvar</button>
        </div>
    </div>
</div>

<script src="<?= $caminho_vendor; ?>/checkbox-tree/js/checktree.js"></script>

<script>
    $(function () {
        $("ul.checktree").checktree();

        $('.salvar').click(function () {
            var checkbox = $('.checkbox');
            var codigo = $('#codigo').val();

            var permissoes = [];

            checkbox.map((index, item) => {
                if ($(item).is(':checked')) {
                    permissoes.push($(item).data('name'));
                }
            });

            $.ajax({
                url: '<?= $urlUsuarios; ?>/permissao.php',
                method: 'POST',
                data: {
                    codigo,
                    permissoes: permissoes.join(','),
                },
                success: function (response) {
                    let retorno = JSON.parse(response);

                    if (retorno.status) {
                        tata.success('Sucesso', retorno.msg);
                    } else {
                        tata.error('Error', retorno.msg);
                    }
                }
            });
        });
    });
</script>
