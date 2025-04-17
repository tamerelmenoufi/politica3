<?php
include "../lib/includes.php";


$servico_tipo = $_POST['servico_tipo'];
$senha = ($_POST['senha']);
$local_fonte = $_POST['local_fonte'];

$_SESSION['servico_tipo'] = $servico_tipo;

$whereLocalFonte = $local_fonte ? "lf.codigo = '{$local_fonte}' AND" : "";
$whereServicoTipo = $servico_tipo ? "lf.servico_tipo = '{$servico_tipo}' AND " : "";

$_SESSION['whereServicoTipo'] = $whereServicoTipo;
$_SESSION['whereLocalFonte'] = $whereLocalFonte;

if (!$servico_tipo) {
    $senha = md5($senha);
    $query = "SELECT codigo FROM usuarios WHERE senha = '{$senha}' AND acesso_agenda = '1'";
} else {
    $query = "SELECT lf.*, st.tipo AS st_tipo FROM local_fontes lf "
        . "INNER JOIN servico_tipo st ON st.codigo = lf.servico_tipo "
        . "WHERE {$whereServicoTipo} {$whereLocalFonte} "
        . "lf.senha = '{$senha}' AND lf.deletado = '0'";
}

$result = mysqli_query($con, $query);

if (!@mysqli_num_rows($result)) {
    echo 0;
    exit();
}

$d = mysqli_fetch_object($result);
$colunas = "s.*, b.nome AS b_nome, c.descricao AS c_descricao, st.tipo AS st_tipo, lf.descricao AS lf_descricao";

$queryEventos = "SELECT {$colunas} FROM servicos s "
    . "LEFT JOIN servico_tipo st ON st.codigo = s.tipo "
    . "LEFT JOIN beneficiados b ON b.codigo = s.beneficiado "
    . "LEFT JOIN categorias c ON c.codigo = s.categoria "
    . "LEFT JOIN local_fontes lf ON lf.codigo = s.local_fonte "
    . "WHERE {$whereLocalFonte} {$whereServicoTipo} "
    . "s.data_agenda > 0 AND s.deletado = '0'";

$resultEventos = mysqli_query($con, $queryEventos);

$eventos = [];
$titulo = "";

if (!$servico_tipo) {
    $titulo = "Geral";
} else {
    $titulo = $d->st_tipo . ($whereLocalFonte ? " - {$d->descricao}" : " - Geral");
};

$i = 0;

while ($dadosEventos = mysqli_fetch_object($resultEventos)) :
    $eventos[] = [
        'id' => $dadosEventos->codigo,
        'title' => formata_datahora($dadosEventos->data_agenda, HORA_MINUTO) . ' - ' . $dadosEventos->b_nome . " - " . $dadosEventos->st_tipo . " (" . ($dadosEventos->lf_descricao ?: 'Outros') . ")",
        'start' => date('Y-m-d', strtotime($dadosEventos->data_agenda)),
    ];

    $i++;
endwhile;

?>

<style>
    #calendar a {
        text-decoration: none;
        color: #858796;
    }

    .fc-basic-view .fc-body .fc-row {
        min-height: .1em;
    }

    /* ===== Full calendar ===== */

    div.fc-day-content div {
        max-height: 20px;
        overflow: hidden;
    }

    .fc .fc-daygrid-body-natural .fc-daygrid-day-events {
        margin-bottom: 0;
        min-height: 1.5em;
    }

    .fc-daygrid-day-frame {
        cursor: pointer;
    }

    .fc .fc-daygrid-day-frame i span {
        font-style: normal;
        padding: 3px;
    }

    .day-highlight {
        background-color: #dcedc8 !important;
    }

    /* ===== Full calendar ===== */

    /* ===== Scrollbar CSS ===== */
    /* Firefox */
    #calendar {
        scrollbar-width: auto;
        scrollbar-color: #858585 #ffffff;
    }

    /* Chrome, Edge, and Safari */
    #calendar *::-webkit-scrollbar {
        width: 5px;
    }

    #calendar *::-webkit-scrollbar-track {
        background: #ffffff;
    }

    #calendar *::-webkit-scrollbar-thumb {
        background-color: #858585;
        border-radius: 2px;
        border: 1px none #ffffff;
    }

    .btn-visualizar {
        cursor: pointer;
    }
</style>

<div id="agendamento">
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow flex-row justify-content-between align-items-center">
        <div>
            <a class="navbar-brand mr-4 voltar text-gray-700" href="#" style="font-size: 1.2em">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
        <div class="text-gray-700 h4 font-weight-bold mb-0">
            <?= $titulo; ?>
        </div>

        <div>
            <ul class="navbar-nav ml-auto">
                <?php
                $querySemAgenda = "SELECT lf.*, st.tipo AS st_tipo, b.nome AS b_nome, s.codigo AS s_codigo FROM local_fontes lf "
                    . "INNER JOIN servico_tipo st ON st.codigo = lf.servico_tipo "
                    . "INNER JOIN servicos s ON s.local_fonte = lf.codigo "
                    . "INNER JOIN beneficiados b ON b.codigo = s.beneficiado "
                    . "WHERE {$whereServicoTipo} {$whereLocalFonte} "
                    . "s.data_agenda = 0 AND lf.deletado = '0'";
                #echo $querySemAgenda;

                $resultSemAgenda = mysqli_query($con, $querySemAgenda);

                $numSemAgenda = @mysqli_num_rows($resultSemAgenda);
                ?>
                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell fa-fw"></i>
                        <!-- Counter - Alerts -->
                        <span class="badge badge-danger badge-counter" text_count_sem_agenda>
                            <?= $numSemAgenda ?: '0' ?>+
                        </span>
                    </a>

                    <!-- Dropdown - Alerts -->
                    <div
                            class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="alertsDropdown"
                            style="max-height: 520px;overflow-y: auto"
                    >
                        <h6 class="dropdown-header">
                            Agendamento sem data
                        </h6>
                        <?php if ($numSemAgenda) : ?>
                            <?php while ($dadosSemAgenda = mysqli_fetch_object($resultSemAgenda)) : ?>
                                <a class="dropdown-item d-flex align-items-center sem-agenda" href="#"
                                   id="sem_agenda_<?= $dadosSemAgenda->s_codigo; ?>"
                                   data-cod_servico="<?= $dadosSemAgenda->s_codigo; ?>">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500"><?= $dadosSemAgenda->descricao; ?></div>
                                        <span class="font-weight-bold"><?= $dadosSemAgenda->b_nome; ?></span>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div>
                                    <span class="font-weight-bold text-gray-500">Nenhum agendamento encontrado</span>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="col-md-12">
        <div class="row">

            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Consulta de Agendamentos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-5">
                                <div id="calendar"></div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-7">
                                <div id="resultado"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input
            type="hidden"
            id="servico_tipo"
            value="<?= $servico_tipo ?>"
    >

</div>

<script>
    $(document).ready(function () {
        let time = null;
        let sleep = 100;
        var servico_tipo = $('#servico_tipo').val();

        var date = new Date();

        let dateString = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);

        consulta_agenda(dateString, servico_tipo);

        var calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            height: 'auto',
            contentHeight: 'auto',
            locale: 'pt-br',
            timeZone: 'America/Manaus',
            initialView: 'dayGridMonth',
            fixedWeekCount: false,
            showNonCurrentDates: false,
            dayMaxEvents: 0,
            events: <?= json_encode($eventos); ?>,
            headerToolbar: {
                right: 'prev,next today',
            },
            moreLinkContent: function (arg) {
                let italicEl = document.createElement('i')

                let html = `<span class="badge badge-info text-right float-right">${arg.shortText}</span>`;
                italicEl.innerHTML = html;

                let arrayOfDomNodes = [italicEl];

                return {
                    domNodes: arrayOfDomNodes
                }
            },
            eventContent: function (arg) {
                let italicEl = document.createElement('i')
                let dados = arg.event._def;
                let html = `<span class="btn-visualizar" data-codigo="${dados.publicId}" style="font-style: normal;">${dados.title}</span>`;

                italicEl.innerHTML = html;

                let arrayOfDomNodes = [italicEl]
                return {
                    domNodes: arrayOfDomNodes
                }
            },
            dateClick: function (info) {
                $(".day-highlight").removeClass("day-highlight");
                $(info.dayEl).addClass("day-highlight");
                //calendar.gotoDate(date)
                consulta_agenda(info.dateStr, servico_tipo);
            },
        });

        calendar.render();

        /* -- Eventos cliques -- */

        $('.fc-prev-button').click(function () {
            clearInterval(time);

            var date = calendar.getDate();

            time = setTimeout(() => {
                consulta_agenda(date.toISOString().slice(0, 10));
            }, sleep);
        });

        $('.fc-next-button').click(function () {
            clearInterval(time);
            var date = calendar.getDate();

            time = setTimeout(() => {
                consulta_agenda(date.toISOString().slice(0, 10));
            }, sleep);
        });

        $('.voltar').click(function (e) {
            e.preventDefault();

            $.ajax({
                url: 'form_acesso.php',
                success: function (response) {
                    $('#palco-agenda').html(response);
                }
            })
        });

        $('#agendamento').on('click', '.btn-visualizar', function () {
            var codigo = $(this).data('codigo');

            $.dialog({
                title: false,
                content: `url: modal_visualizar.php?codigo=${codigo}`,
                theme: 'bootstrap',
                columnClass: 'large'
            });
        });

        $('.sem-agenda').click(function () {
            var cod_servico = $(this).data('cod_servico');

            dialogDefineData = $.dialog({
                title: 'Definir data do agendamento',
                content: `url: form_definir_data.php?cod_servico=${cod_servico}`,
                theme: 'bootstrap',
                columnClass: 'medium'
            });
        });

        /* -- Eventos cliques -- */

        function consulta_agenda(data, servico_tipo) {
            $('.loading').fadeIn(200);

            $.ajax({
                url: 'tabela_agendamentos.php',
                method: 'POST',
                data: {
                    data,
                    servico_tipo
                },
                success: function (response) {
                    $('.loading').fadeOut(200);
                    $('#resultado').html(response);
                }
            });
        }
    });
</script>