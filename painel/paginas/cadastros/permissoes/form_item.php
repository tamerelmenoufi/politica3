<?php
include 'config_permissoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = $_POST['descricao'];
    $vinculo = $_POST['vinculo'];

    $query = "INSERT INTO permissoes SET descricao = '{$descricao}', vinculo = '{$vinculo}'";

    if (mysqli_query($con, $query)) {
        $codigo = mysqli_insert_id($con);

        sis_logs('permissoes', $codigo, $query);

        echo json_encode(['status' => true, 'msg' => 'Dados salvo com sucesso']);
    } else {
        echo json_encode(['status' => false, 'msg' => 'Error ao salvar item', 'mysqlError' => mysqli_error()]);
    }
    exit();
}

$vinculo = $_GET['vinculo'];


?>

<form id="form-item">

    <div class="form-group">
        <label for="tipo">Descrição <i class="text-danger">*</i></label>
        <input
                type="text"
                class="form-control"
                id="descricao"
                name="descricao"
                value="<?= '' ?>"
                required
        >

    </div>

    <input type="hidden" id="vinculo" name="vinculo" value="<?= $vinculo; ?>">

    <div class="form-group">
        <button type="submit" class="btn btn-success float-right">Salvar</button>
    </div>
</form>

<script>
    $(function () {

        $('#form-item').submit(function (e) {
            e.preventDefault();

            var dados = $(this).serializeArray();

            $('.loading').fadeIn(200);

            $.ajax({
                url: '<?= $urlPermissoes; ?>/form_item.php',
                method: 'POST',
                data: dados,
                success: function (response) {
                    let retorno = JSON.parse(response);

                    if (retorno.status) {

                        $.ajax({
                            url: '<?= $urlPermissoes; ?>/visualizar.php',
                            data: {
                                codigo: '<?= $vinculo; ?>',
                                acao: 'atualizar'
                            },
                            success: function (response) {
                                $('.loading').fadeOut(200);
                                $('.card-body-itens').html(response);
                            }
                        });

                        tata.success('Sucesso', retorno.msg);
                        dialogItem.close();
                    } else {
                        tata.error('Error', retorno.msg);
                    }
                }
            })
        });
    });
</script>
