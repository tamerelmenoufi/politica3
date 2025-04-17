<?php
include "config_beneficiados.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' and $_POST['acao'] === 'excluir') {
    $codigo = $_POST['codigo'];

    if (exclusao('beneficiados', $codigo)) {
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
                            b.nome like '%{$_SESSION['campo_busca']}%'
                        or  m.municipio like '%{$_SESSION['campo_busca']}%' 
                        or  b.cpf = '{$_SESSION['campo_busca']}'
                    )";

}

$limite = (($_SESSION['pgLimite'])?:10);
$inicio = (($_SESSION['pgInicio'])?(($_SESSION['pgInicio']-1)*$limite):0);
////////////////////////PAGINACAO/////////////////////////////////

$query = "SELECT count(*) as qt FROM beneficiados b "
    . "LEFT JOIN municipios m ON m.codigo = b.municipio "
    . "WHERE b.deletado = '0' {$where}";
$result = mysqli_query($con, $query);
$qt = mysqli_fetch_object($result);
$total = $qt->qt;
$paginas = ($total/$limite) + (($total%$limite)?1:0);


$query = "SELECT b.*, m.municipio AS municipio FROM beneficiados b "
    . "LEFT JOIN municipios m ON m.codigo = b.municipio "
    . "WHERE b.deletado = '0' {$where}"
    . "ORDER BY codigo desc limit {$inicio}, {$limite}";
$result = mysqli_query($con, $query);

?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb shadow bg-gray-custom">
        <li class="breadcrumb-item"><a href="#" url="content.php">Início</a></li>
        <li class="breadcrumb-item active" aria-current="page">Beneficiados (Foram encontrados <?=$total?> registros, exibidos em páginas de <?=$limite?>)</li>
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
                    ////
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
            Beneficiados
        </h6>
        <?php
        if (in_array('Beneficiados - Cadastrar', $ConfPermissoes)) {
            ?>
            <button type="button" class="btn btn-success btn-sm" url="paginas/cadastros/beneficiados/form.php">
                <i class="fa-solid fa-plus"></i> Novo
            </button>
            <?php
        }
        ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">

            <table id="datatable" class="table" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Município</th>
                    <th class="mw-20">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($d = mysqli_fetch_object($result)): ?>
                    <tr id="linha-<?= $d->codigo; ?>">
                        <td><?= $d->nome ?></td>
                        <td><?= $d->cpf; ?></td>
                        <td><?= $d->municipio; ?></td>
                        <td>
                            <button
                                    class="btn btn-sm btn-link"
                                    url="<?= $urlBeneficiados ?>/visualizar.php?codigo=<?= $d->codigo ?>"
                            >
                                <i class="fa-regular fa-eye text-info"></i>
                            </button>
                            <?php
                            if (in_array('Beneficiados - Editar', $ConfPermissoes)) {
                                ?>
                                <button
                                        class="btn btn-sm btn-link"
                                        url="<?= $urlBeneficiados ?>/form.php?codigo=<?= $d->codigo; ?>"
                                >
                                    <i class="fa-solid fa-pencil text-warning"></i>
                                </button>
                                <?php
                            }
                            if (in_array('Beneficiados - Excluir', $ConfPermissoes)) {
                                ?>
                                <button
                                        class="btn btn-sm btn-link btn-excluir"
                                        data-codigo="<?= $d->codigo ?>"
                                >
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
                url:"<?= $urlBeneficiados;?>/index.php",
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
                url:"<?= $urlBeneficiados;?>/index.php",
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
                url:"<?= $urlBeneficiados;?>/index.php",
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
                url:"<?= $urlBeneficiados;?>/index.php",
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

        $(".btn-excluir").click(function () {
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
                                        //$(`#linha-${codigo}`).remove();
                                        $(this).parent().parent().remove();
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
    });
</script>