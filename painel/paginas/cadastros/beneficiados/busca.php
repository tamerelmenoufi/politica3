<?php
include "config_beneficiados.php";
?>

<div class="input-group mb-3">
    <label class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></label>
    <input type="text" class="form-control campo_busca" placeholder="Localizar Beneficiado ..." value="<?=$_POST['campo_busca']?>">
    <button class="btn btn-outline-secondary buscar" type="button">Buscar</button>
    <button class="btn btn-outline-secondary novo" type="button">Novo</button>
</div>

<div class="list-group">
<?php

    if($_POST['campo_busca']){


        $query = "select * from beneficiados where nome like '%{$_POST['campo_busca']}%' or cpf = '{$_POST['campo_busca']}'";
        $result = mysqli_query($con, $query);
        while($d = mysqli_fetch_object($result)){
?>

    <a selectBeneficiado href="#" codigo="<?=$d->codigo?>" class="list-group-item list-group-item-action"><?=$d->nome?></a>
<?php

        }      

    }
?>
</div>

<script>
    $(function(){

        $(".novo").click(function(){

            $.ajax({
                url:"paginas/cadastros/beneficiados/novo.php",
                success:function(dados){
                    $("div[NovoCadastro]").html(dados);
                    $("div[NovoCadastroBG]").css("display","block");
                    $("div[NovoCadastro]").css("display","block");
                    JanelaPopUp.close();
                },
                error:function(){
                    alert('Ocorreu um erro!');
                }
            });

        }) 
        

        $(".buscar").click(function(){

            campo_busca = $(".campo_busca").val();

            $.ajax({
                url:"paginas/cadastros/beneficiados/busca.php",
                type:"POST",
                data:{
                    campo_busca
                },
                success:function(dados){
                    JanelaPopUp.setContent(dados);
                },
                error:function(){
                    alert('Ocorreu um erro!');
                }
            });

        })        

        $("a[selectBeneficiado]").click(function(){

            codigo = $(this).attr("codigo");
            nome = $(this).text();

            $(".beneficiado").text(nome);
            $("#beneficiado").val(codigo);

            JanelaPopUp.close();

        })

    })

</script>