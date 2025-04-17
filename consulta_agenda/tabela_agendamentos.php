<?php
include 'conf.php';

if ($_POST['data']) $_SESSION['data'] = $_POST['data'];

$data = $_SESSION['data'];
$servico_tipo = $_SESSION['servico_tipo'];
$filtro = $_POST['filtro'];

$query = "SELECT s.*, b.nome AS b_nome, lf.descricao AS lf_descricao, st.tipo AS st_descricao FROM servicos s "
    . "LEFT JOIN local_fontes lf ON lf.codigo = s.local_fonte "
    . "LEFT JOIN servico_tipo st ON st.codigo = s.tipo "
    . "LEFT JOIN beneficiados b ON b.codigo = s.beneficiado ";

$whereGeral = "WHERE {$_SESSION['whereServicoTipo']} {$_SESSION['whereLocalFonte']} s.data_agenda LIKE '%{$data}%'  ORDER BY data_agenda";
$result = mysqli_query($con, $query . $whereGeral);

$mes = date('m', strtotime($data));
$whereMes = "WHERE {$_SESSION['whereServicoTipo']} {$_SESSION['whereLocalFonte']} DATE_FORMAT(s.data_agenda, \"%m\") = '{$mes}' "
    . "ORDER BY data_agenda";
$resultMes = mysqli_query($con, $query . $whereMes);

$mesArray = [
    '01' => 'Janeiro',
    '02' => 'Fevereiro',
    '03' => 'Março',
    '04' => 'Abril',
    '05' => 'Maio',
    '06' => 'Junho',
    '07' => 'Julho',
    '08' => 'Agosto',
    '09' => 'Setembro',
    '10' => 'Outubro',
    '11' => 'Novembro',
    '12' => 'Dezembro'
];

?>

<h1 class="h5 text-gray-600 font-weight-bold mt-lg-0 mt-4 mt-sm-4">
    <?= 'Dia ' . date('d', strtotime($data)); ?>
</h1>

<div class="table-responsive mb-2" style="max-height: 350px; border: 1px solid #36b9cc">

    <table class="table table-sm table-bordered table-hover table-striped" style="font-size: .9rem;">
        <thead>
        <tr>
            <th class="text-center">Data</th>
            <th class="text-center">Beneficiado</th>
            <th class="text-center">Local</th>
            <th class="text-center">Serviço</th>
            <th class="text-center">Situação</th>
            <th class="text-center">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (mysqli_num_rows($result)):
            while ($d = mysqli_fetch_object($result)):
                $data = formata_datahora($d->data_agenda, DATA_HM);
                ?>
                <tr>
                    <td class="text-center"><?= $data; ?></td>
                    <td class="text-center"><?= $d->b_nome; ?></td>
                    <td class="text-center text-nowrap"><?= $d->lf_descricao; ?></td>
                    <td class="text-center text-nowrap"><?= $d->st_descricao; ?></td>
                    <td class="text-center text-nowrap">
                        <span text_situacao_<?= $d->codigo; ?>><?= getSituacaoOptions($d->situacao); ?></span>
                    </td>
                    <td>
                        <button
                                type="button"
                                class="btn btn-sm btn-link btn-visualizar"
                                data-codigo="<?= $d->codigo; ?>"
                        >
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>

        <?php else:
            echo '<tr><td class="text-center" colspan="6">Nenhum agendamento previsto na data de hoje</td></tr>';
            ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<br>
<h1 class="h5 text-gray-600 font-weight-bold">Mês de <?= $mesArray[date('m', strtotime($data))]; ?></h1>

<div class="table-responsive" style="max-height: 350px; border: 1px solid #36b9cc">
    <table class="table table-sm table-bordered table-hover table-striped" style="font-size: .9rem;">
        <thead>
        <tr>
            <th class="text-center">Data</th>
            <th class="text-center">Beneficiado</th>
            <th class="text-center">Local</th>
            <th class="text-center">Serviço</th>
            <th class="text-center">Situação</th>
            <th class="text-center">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (mysqli_num_rows($resultMes)):
            while ($d = mysqli_fetch_object($resultMes)):
                $data = formata_datahora($d->data_agenda, DATA_HM);
                ?>
                <tr>
                    <td class="text-center"><?= $data; ?></td>
                    <td class="text-center"><?= $d->b_nome; ?></td>
                    <td class="text-center text-nowrap"><?= $d->lf_descricao; ?></td>
                    <td class="text-center text-nowrap"><?= $d->st_descricao; ?></td>
                    <td class="text-center text-nowrap">
                        <span text_situacao_<?= $d->codigo; ?>><?= getSituacaoOptions($d->situacao); ?></span>
                    </td>
                    <td class="text-center">
                        <button
                                type="button"
                                class="btn btn-sm btn-link btn-visualizar"
                                data-codigo="<?= $d->codigo; ?>"
                        >
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>

        <?php else:
            echo '<tr><td class="text-center" colspan="6">Nenhum agendamento previsto neste mês</td></tr>';
            ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    $(function () {

    });
</script>