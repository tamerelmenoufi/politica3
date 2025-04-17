<?php
include_once "../lib/includes.php";
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9 mt-5">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body">
                    <div class="position-relative d-flex flex-row align-items-center">
                        <div>
                            <a
                                    href="../" class="text-decoration-none text-gray-700"
                                    style="font-size: 1.6em"
                            >
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        </div>
                        <div style="flex: 1">
                            <h1 class="h3 text-gray-800 text-center m-0 font-weight-bold">Consultar agendamentos</h1>
                        </div>
                    </div>
                    <hr class="mb-5">

                    <form id="form-consulta">

                        <div class="form-group row">
                            <label for="servico_tipo" class="col-lg-2 col-form-label">Tipo de serviço</label>
                            <div class="col-lg-10">
                                <select
                                        class="form-control"
                                        id="servico_tipo"
                                        name="servico_tipo"
                                >
                                    <option value="">Geral</option>
                                    <?php
                                    $query = "SELECT * FROM servico_tipo WHERE deletado = '0' ORDER BY tipo";
                                    $result = mysqli_query($con, $query);

                                    while ($d = mysqli_fetch_object($result)): ?>
                                        <option value="<?= $d->codigo ?>"><?= $d->tipo; ?></option>
                                    <?php endwhile; ?>
                                        <option value="acao_social">Ação Social</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row form-local-fonte">
                            <label for="local_fonte" class="col-lg-2 col-form-label">Local/Fonte</label>
                            <div class="col-lg-10">
                                <select
                                        class="form-control"
                                        id="local_fonte"
                                        name="local_fonte"
                                >
                                    <option value="">Geral</option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nome" class="col-lg-2 col-form-label">Chave de Acesso</label>
                            <div class="col-lg-10">
                                <input
                                        type="password"
                                        class="form-control"
                                        name="senha"
                                        id="senha"
                                        placeholder="Digite a chave de acesso"
                                >
                            </div>
                        </div>

                        <div class="form-group float-right mt-4">
                            <button
                                    type="submit"
                                    class="btn btn-success"
                            >
                                Consultar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#servico_tipo').change(function () {
            var valor = $(this).val();

            $.ajax({
                url: 'select_local_fonte.php',
                data: {servico_tipo: valor},
                success: function (response) {
                    $('#local_fonte').html(response);
                }
            })
        });

        $('#form-consulta').submit(function (e) {
            e.preventDefault();

            if ($('#senha').val() === "") {
                tata.error('Error!', 'Preencha a chave de acesso');
                return false;
            }

            var local_fonte = $('#local_fonte').val();
            var servico_tipo = $('#servico_tipo').val();
            var senha = $('#senha').val();

            if(servico_tipo == 'acao_social'){
                $.ajax({
                    url: 'acao_social.php',
                    method: 'POST',
                    success: function (response) {
                        $('#palco-agenda').html(response);
                    }
                })
            }else{
                $.ajax({
                    url: 'agendamento.php',
                    method: 'POST',
                    data: {
                        local_fonte,
                        servico_tipo,
                        senha
                    },
                    success: function (response) {
                        if (response == '0') {
                            tata.error('Error', 'Consulta não encontrada')
                        } else {
                            $('#palco-agenda').html(response);
                        }
                    }
                })
            }

        });
    });
</script>