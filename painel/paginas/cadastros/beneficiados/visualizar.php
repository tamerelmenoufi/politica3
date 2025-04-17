<?php
include "config_beneficiados.php";

$codigo = $_GET['codigo'];
$query = "SELECT a.*, m.municipio AS municipio FROM beneficiados a "
    . "LEFT JOIN municipios m ON m.codigo = a.municipio "
    . "WHERE a.codigo = '{$codigo}'";
$result = mysqli_query($con, $query);
$d = mysqli_fetch_object($result);

?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item" aria-current="page">
            <a href="#" url="<?= $urlBeneficiados; ?>/index.php">Beneficiados</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            Visualizar
        </li>
    </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-md-row flex-column align-items-center justify-content-md-between">
        <h6 class="m-0 font-weight-bold text-primary">
            Visualizar
        </h6>
        <div class="d-md-flex justify-content-xl-center">
            <?php
            if(in_array('Beneficiados - Cadastrar', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-success btn-sm float-left"
                    url="<?= $urlBeneficiados ?>/form.php"
                    style="margin-right: 2px"
            >
                <i class="fa-solid fa-plus"></i> Novo
            </button>
            <?php
            }
            if(in_array('Beneficiados - Editar', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-warning btn-sm float-left"
                    url="<?= $urlBeneficiados ?>/form.php?codigo=<?= $codigo; ?>"
                    style="margin-right: 2px"
            >
                <i class="fa-solid fa-pencil"></i> Editar
            </button>
            <?php
            }
            if(in_array('Beneficiados - Excluir', $ConfPermissoes)){
            ?>
            <button
                    type="button"
                    class="btn btn-danger btn-excluir btn-sm float-left"
                    data-codigo="<?= $codigo; ?>"
            >
                <i class="fa-regular fa-trash-can"></i> Excluir
            </button>
            <?php
            }
            ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 font-weight-bold">Nome</div>
            <div class="col-md-8"><?= $d->nome; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Nome da Mãe</div>
            <div class="col-md-8"><?= $d->nome_mae; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">CPF</div>
            <div class="col-md-8"><?= $d->cpf; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Data de nascimento</div>
            <div class="col-md-8"><?= formata_datahora($d->data_nascimento, DATA); ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Sexo</div>
            <div class="col-md-8"><?= getSexoOptions($d->sexo); ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">E-Mail</div>
            <div class="col-md-8"><?= $d->email; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Telefone</div>
            <div class="col-md-8"><?= $d->telefone; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">Município</div>
            <div class="col-md-8"><?= $d->municipio; ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 font-weight-bold">CEP</div>
            <div class="col-md-8"><?= $d->cep; ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4 font-weight-bold">Endereço</div>
            <div class="col-md-8"><?= $d->endereco; ?></div>
        </div>


        <h3 class="mt-3">Serviços</h3>
        <?php
        $q = "SELECT a.*, b.tipo as tipo_nome, c.descricao as sub_tipo_nome FROM servicos a left join servico_tipo b on a.tipo = b.codigo left join categorias c on a.categoria = c.codigo where a.beneficiado = '{$d->codigo}' order by a.data_pedido";
        $r = mysqli_query($con, $q);
        $n = mysqli_num_rows($r);
        if($n){
        ?>
        <table class="table table-hover">
          <thead>
            <tr>
                <th>Serviçco</th>
                <!--<th>Detalhes</th>-->
                <th>Data</th>
                <th>Situação</th>
            </tr>
          </thead>
          <tbody>
            <?php
            while($s = mysqli_fetch_object($r)){
            ?>
            <tr>
                <td><?=$s->tipo_nome.(($s->sub_tipo_nome)?" ({$s->sub_tipo_nome})":false)?></td>
                <!--<td><?=$s->detalhes?></td>-->
                <td><?=formata_datahora($s->data_pedido)?></td>
                <td><?=strtoupper($s->situacao)?></td>
            </tr>
            <?php
            }
            ?>
          </tbody>  
        </table>
        <?php
        }
        ?>

    </div>
</div>

<script>
    $('.btn-excluir').click(function () {
        var codigo = $(this).data('codigo');

        $.confirm({
            title: 'Aviso',
            content: 'Deseja excluir este registro?',
            type: 'red',
            icon: 'fa fa-warning',
            buttons: {
                sim: {
                    text: 'Sim',
                    btnClass: 'btn-red',
                    action: function () {
                        $.ajax({
                            url: '<?= $urlBeneficiados;?>/index.php',
                            method: 'POST',
                            data: {
                                acao: 'excluir',
                                codigo
                            },
                            success: function (response) {
                                let retorno = JSON.parse(response);

                                if (retorno.status) {
                                    tata.success('Sucesso', retorno.msg);

                                    $.ajax({
                                        url: '<?= $urlBeneficiados; ?>/index.php',
                                        success: function (response) {
                                            $('#palco').html(response);
                                        }
                                    });
                                } else {
                                    tata.error('Error', retorno.msg);
                                }
                            }
                        })
                    }
                },
                nao: {
                    text: 'Não'
                }
            }
        })
    });
</script>
