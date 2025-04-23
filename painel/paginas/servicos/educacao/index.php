<?php
include_once "config_servicos.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' and $_POST['acao'] === 'excluir') {
    $codigo = $_POST['codigo'];

    if (exclusao('servicos', $codigo)) {
        echo json_encode(["status" => true, "msg" => "Registro excluído com sucesso"]);
    } else {
        echo json_encode(["status" => false, "msg" => "Error ao tentar excluír"]);
    }
    exit;
}

////////////////////////PAGINACAO/////////////////////////////////

if($_POST['acao'] == 'limite') $_SESSION['pgLimite'] = $_POST['limite'];

if($_POST['pagina']) { $_SESSION['pgInicio'] = $_POST['pagina']; }

if($_POST['acao'] == 'busca'){
    $_SESSION['campo_busca'] = $_POST['campo_busca'];
    $_SESSION['campo_situacao'] = $_POST['situacao'];
    $_SESSION['pgInicio'] = 0;
}
if($_POST['acao'] == 'limpar_busca'){
    $_SESSION['campo_busca'] = false;
    $_SESSION['campo_situacao'] = false;
    $_SESSION['pgInicio'] = 0;
}

if($_SESSION['campo_busca']){

    $where  = " and (
                            a.nome like '%{$_SESSION['campo_busca']}%'
                        or  b.nome like '%{$_SESSION['campo_busca']}%' 
                        or  lf.descricao like '%{$_SESSION['campo_busca']}%'
                        or  s.local_responsavel like '%{$_SESSION['campo_busca']}%'
                    )";

}

if($_SESSION['campo_situacao']){

    $where  .= " and (
                            s.situacao = '{$_SESSION['campo_situacao']}'
                    )";

}

$limite = (($_SESSION['pgLimite'])?:10);
$inicio = (($_SESSION['pgInicio'])?(($_SESSION['pgInicio']-1)*$limite):0);
////////////////////////PAGINACAO/////////////////////////////////


$colunaAtendimento = "(CASE WHEN s.data_agenda <= NOW() AND s.situacao = 'concluido' AND s.data_agenda > 0 THEN 'Atendido' "
. "WHEN s.data_agenda < NOW() AND s.situacao != 'concluido' AND s.data_agenda > 0 THEN 'Não atendido' "
. "WHEN s.data_agenda > NOW() AND s.data_agenda > 0 THEN 'agendado' "
. "ELSE 'Aguardando' "
. "END) AS atendimento, lf.descricao AS lf_descricao ";

//echo "<br><br><br><br><br>";
//*
$query = "SELECT count(*) as qt FROM servicos s "
    . "LEFT JOIN assessores a ON a.codigo = s.assessor "
    . "LEFT JOIN beneficiados b ON b.codigo = s.beneficiado "
    . "LEFT JOIN especialidades t ON t.codigo = s.especialidade "
    . "LEFT JOIN local_fontes lf ON lf.codigo = s.local_fonte "
    . "WHERE s.tipo = '1' AND s.deletado = '0' {$where}";
$result = mysqli_query($con, $query);
$qt = mysqli_fetch_object($result);
$total = $qt->qt;
$paginas = ($total/$limite) + (($total%$limite)?1:0);
//*/   
$query = "SELECT s.*, a.nome AS assessor, b.nome AS beneficiado, t.descricao as especialidade, {$colunaAtendimento} FROM servicos s "
    . "LEFT JOIN assessores a ON a.codigo = s.assessor "
    . "LEFT JOIN beneficiados b ON b.codigo = s.beneficiado "
    . "LEFT JOIN especialidades t ON t.codigo = s.especialidade "
    . "LEFT JOIN local_fontes lf ON lf.codigo = s.local_fonte "
    . "WHERE s.tipo = '1' AND s.deletado = '0' {$where}"
    . "ORDER BY s.codigo DESC limit {$inicio}, {$limite}";


$result = mysqli_query($con, $query);

$_SESSION['query_xls'] = $query;
$_SESSION['saude_xls'] = false;
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item active" aria-current="page">Educação (Foram encontrados <?=$total?> registros, exibidos em páginas de <?=$limite?>)</li>
    </ol>
</nav>


<!-- PAGINACAO -->
    <div class="row">
        <div class="col-md-3">
            <div class="input-group mb-3">

            <div class="input-group-prepend">
                    <label class="input-group-text">Exibir</label>
                </div>
                <select class="form-control limite" aria-label="Example select with button addon">
                    <option value="10" <?=((($_SESSION['pgLimite']) == "10")?'selected':false)?>>10</option>
                    <option value="25" <?=((($_SESSION['pgLimite']) == "25")?'selected':false)?>>25</option>
                    <option value="50" <?=((($_SESSION['pgLimite']) == "50")?'selected':false)?>>50</option>
                    <option value="100" <?=((($_SESSION['pgLimite']) == "100")?'selected':false)?>>100</option>
                </select>

                <div class="input-group-prepend">
                    <label class="input-group-text">Página</label>
                </div>
                <select class="form-control paginacao" aria-label="Example select with button addon">
                    <?php
                    for($i=1;$i<=$paginas;$i++){
                    ?>
                    <option value="<?=$i?>" <?=((($_SESSION['pgInicio']) == $i)?'selected':false)?>><?=$i?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-md-9">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></label>
                </div>
                <input type="text" class="form-control campo_busca" placeholder="Buscar por ..." value="<?=$_SESSION['campo_busca']?>">
                <select
                        id="filtro-situacao"
                        class="form-control filtro-situacao"
                        title="Situação"
                        data-width="auto"
                >
                    <option value="">Situação</option>
                    <?php
                    foreach (getSituacao() as $key => $value):
                        echo "<option value=\"{$value}\" ".(($_SESSION['campo_situacao'] == $value)?'selected':false).">{$value}</option>";
                    endforeach;
                    ?>
                </select>
                <div class="input-group-prepend">
                    <button class="btn btn-outline-secondary buscar" type="button">Buscar</button>
                    <button class="btn btn-outline-danger limpar_busca" type="button">Limpar</button>
                </div>
            </div>
        </div>
    </div>
<!-- PAGINACAO -->


<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            Serviços - EDUCAÇÃO
        </h6>
        <?php
        if (in_array('Certidão de Nascimento - Cadastrar', $ConfPermissoes)) {
        ?>
        <span>
        <?php
            include("../../csv/download.php");
            ?>
            <button type="button" class="btn btn-success btn-sm" url="<?= $urlServicos; ?>/form.php">
                <i class="fa-solid fa-plus"></i> Novo
            </button>
        </span>
            <?php
        }
        ?>
    </div>




    <div class="card-body">
        <div class="table-responsive">

            <table id="datatable" class="table" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>Beneficiado</th>
                    <th>Assessor</th>
                    <th>Data da Agenda</th>
                    <th>Situação</th>
                    <th>Local</th>
                    <th class="mw-20">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($d = mysqli_fetch_object($result)): ?>
                    <tr id="linha-<?= $d->codigo; ?>">
                        <td><?= $d->beneficiado; ?></td>
                        <td><?= $d->assessor; ?></td>
                        <td><?= formata_datahora($d->data_agenda, DATA_HM); ?></td>
                        <td><?= getSituacaoOptions($d->situacao); ?></td>
                        <td><?= $d->lf_descricao . (($d->local_responsavel)?' ('.$d->local_responsavel.')':false); ?></td>
                        <td>
                            <button
                                    class="btn btn-sm btn-link"
                                    url="<?= $urlServicos ?>/visualizar.php?codigo=<?= $d->codigo ?>"
                            >
                                <i class="fa-regular fa-eye text-info"></i>
                            </button>
                            <?php
                            if (in_array('Certidão de Nascimento - Editar', $ConfPermissoes)) {
                                ?>
                                <button
                                        class="btn btn-sm btn-link"
                                        url="<?= $urlServicos ?>/form.php?codigo=<?= $d->codigo; ?>"
                                >
                                    <i class="fa-solid fa-pencil text-warning"></i>
                                </button>
                                <?php
                            }
                            if (in_array('Certidão de Nascimento - Excluir', $ConfPermissoes)) {
                                ?>
                                <button class="btn btn-sm btn-link btn-excluir" data-codigo="<?= $d->codigo ?>">
                                    <i class="fa-regular fa-trash-can text-danger"></i>
                                </button>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<script>
    $(function () {

        ////////////////////////////PAGINACAO////////////////////////////////////////////



        $(".limite").change(function(){
            limite = $(this).val();
            $('.loading').fadeIn(200);
            $.ajax({
                url:"<?= $urlServicos;?>/index.php",
                type:"POST",
                data:{
                    limite,
                    acao:'limite'
                },
                success:function(dados){
                    $('#palco').html(dados);
                    $('.loading').fadeOut(200);
                }
            })
        })

        $(".buscar").click(function(){
            campo_busca = $(".campo_busca").val();
            situacao = $("#filtro-situacao").val();
            if(!campo_busca && !situacao){
                $.alert({
                    title:"Erro na Busca",
                    type:"red",
                    title:"Digite uma informação para realizar a busca!"
                });
                return false;
            }
            $('.loading').fadeIn(200);
            $.ajax({
                url:"<?= $urlServicos;?>/index.php",
                type:"POST",
                data:{
                    campo_busca,
                    situacao,
                    acao:'busca'
                },
                success:function(dados){
                    $('#palco').html(dados);
                    $('.loading').fadeOut(200);
                }
            })
        })

        $(".limpar_busca").click(function(){
            $('.loading').fadeIn(200);
            $.ajax({
                url:"<?= $urlServicos;?>/index.php",
                type:"POST",
                data:{
                    acao:'limpar_busca'
                },
                success:function(dados){
                    $('#palco').html(dados);
                    $('.loading').fadeOut(200);
                }
            })
        })        

        $(".paginacao").change(function(){
            pagina = $(this).val();
            $('.loading').fadeIn(200);
            $.ajax({
                url:"<?= $urlServicos;?>/index.php",
                type:"POST",
                data:{
                  pagina
                },
                success:function(dados){
                    $('#palco').html(dados);
                    $('.loading').fadeOut(200);
                }
            })
        })  
        ////////////////////////////////////PAGINACAO////////////////////////////////////////




        
        //var table = $("#datatable").DataTable();


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
                            $('.loading').fadeIn(200);
                            $.ajax({
                                url: '<?= $urlServicos;?>/index.php',
                                method: 'POST',
                                data: {
                                    acao: 'excluir',
                                    codigo
                                },
                                success: function (response) {
                                    let retorno = JSON.parse(response);
                                    $('.loading').fadeOut(200);
                                    if (retorno.status) {
                                        tata.success('Sucesso', retorno.msg);
                                    } else {
                                        tata.error('Error', retorno.msg);
                                    }

                                    $(`#linha-${codigo}`).remove();
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
    });
</script>