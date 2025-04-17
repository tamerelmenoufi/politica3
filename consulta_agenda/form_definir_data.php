<?php
include "../lib/includes.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cod_servico = $_POST['cod_servico'];
    $data_agenda = $_POST['data_agenda'];

    $query = "UPDATE servicos SET data_agenda = '{$data_agenda}' WHERE codigo = '{$cod_servico}'";
    if (mysqli_query($con, $query)) {

        $query = "SELECT s.*, b.nome AS b_nome, c.descricao AS c_descricao, st.tipo AS st_tipo, lf.descricao AS lf_descricao FROM servicos s "
            . "LEFT JOIN servico_tipo st ON st.codigo = s.tipo "
            . "LEFT JOIN beneficiados b ON b.codigo = s.beneficiado "
            . "LEFT JOIN local_fontes lf ON lf.codigo = s.local_fonte "
            . "LEFT JOIN categorias c ON c.codigo = s.categoria "
            . " WHERE s.codigo = '{$cod_servico}'";

        $result = mysqli_query($con, $query);
        $d = mysqli_fetch_object($result);

        echo json_encode([
            'status' => true,
            'msg' => 'Data de agendamento cadastrado com sucesso',
            'data' => [
                'id' => $d->codigo,
                'title' => formata_datahora($d->data_agenda, HORA_MINUTO) . ' - ' . $d->b_nome ." - ". $d->st_tipo." (" . ($d->lf_descricao ?: 'Outros') . ")",
                'start' => date('Y-m-d', strtotime($d->data_agenda))
            ],
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'msg' => 'Error ao cadastrar data de agendamento'
        ]);
    }
    exit();
}

$cod_servico = $_GET['cod_servico'];

$query = "SELECT s.*, st.tipo AS st_tipo, b.nome AS b_nome, lf.descricao AS lf_descricao, c.descricao AS c_descricao FROM servicos s "
    . "LEFT JOIN servico_tipo st ON st.codigo = s.tipo "
    . "LEFT JOIN beneficiados b ON b.codigo = s.beneficiado "
    . "LEFT JOIN local_fontes lf ON lf.codigo = s.local_fonte "
    . "LEFT JOIN categorias c ON c.codigo = s.categoria "
    . "WHERE s.codigo = '{$cod_servico}'";

$result = mysqli_query($con, $query);
$d = mysqli_fetch_object($result);

?>

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <b>Tipo de serviço</b>
            </div>
            <div class="col-md-8">
                <?= $d->st_tipo; ?>
            </div>
        </div>

        <?php if ($d->c_descricao): ?>
            <div class="row">
                <div class="col-md-4">
                    <b>Categoria</b>
                </div>
                <div class="col-md-8">
                    <?= $d->c_descricao; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <b>Beneficiado</b>
            </div>
            <div class="col-md-8">
                <?= $d->b_nome; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <b>Local</b>
            </div>
            <div class="col-md-8">
                <?= $d->lf_descricao; ?>
            </div>
        </div>

        <br>

        <form id="form-agendamento" action="">

            <div class="form-group">
                <label for="data_agenda">Data e hora</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                    </div>
                    <input
                            type="datetime-local"
                            class="form-control"
                            id="data_agenda"
                            aria-describedby="data e hora"
                    >
                </div>
            </div>

            <input
                    id="cod_servico"
                    type="hidden"
                    value="<?= $cod_servico; ?>"
            >

            <div class="form-group">
                <button type="submit" class="btn btn-success btn-block">Salvar</button>
            </div>
        </form>

    </div>
</div>

<script>
    $(function () {

        $('#form-agendamento').submit(function (e) {
            e.preventDefault();

            var cod_servico = $('#cod_servico').val();
            var data_agenda = $('#data_agenda').val();


            if (!data_agenda) {
                $.alert({
                    title: 'Aviso',
                    content: 'Por favor, preencha o campo data',
                    theme: 'bootstrap',
                    columnClass: 'medium',
                    type: 'red',
                })
                return false;
            }

            $.ajax({
                url: 'form_definir_data.php',
                method: 'POST',
                dataType: 'JSON',
                data: {
                    cod_servico,
                    data_agenda
                },
                success: function (response) {
                    console.log(response);

                    if (response.status) {
                        $.ajax({
                            url: 'tabela_agendamentos.php',
                            success: function (html) {
                                $('#resultado').html(html);
                            }
                        });

                        let source = [
                            {
                                id: response.data.id,
                                title: response.data.title,
                                start: response.data.start,
                            },
                        ];

                        calendar.batchRendering(() => calendar.addEventSource(source));

                        let count_sem_agenda = $('span[text_count_sem_agenda]');
                        let count = (parseInt(count_sem_agenda.text()) - 1);

                        console.log(count_sem_agenda.text(), count);

                        count_sem_agenda.text(count);

                        tata.success('Sucesso', response.msg);
                        $(`#sem_agenda_${cod_servico}`).remove();
                        dialogDefineData.close();


                    } else {
                        tata.error('Error', response.msg);
                    }

                }
            })
        });
    });
</script>