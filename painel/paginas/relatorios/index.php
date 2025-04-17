<?php
    include "../../../lib/includes.php";
?>
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-body">
                <div graficos opc="<?=$_GET['tipo']?>"></div>
            </div>
        </div>
    </div>
</div>


<script>

    function grafico(obj,opc){
        $.ajax({
            url:"paginas/relatorios/graficos/"+opc+".php?<?=md5(date("YmdHis"))?>",
            success:function(dados){
                    obj.html(dados);
            },
            error:function(){
                alert('Ocorreu um erro!');
            }
        });
    }

    $(function(){
        $("div[graficos]").each(function(){
            obj = $(this);
            opc = $(this).attr("opc");
            console.log(opc);
            grafico(obj,opc);
        });
    })
</script>