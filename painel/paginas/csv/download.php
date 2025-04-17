<!-- <button xls type="button" class="btn btn-primary btn-sm">
    <i class="fa-solid fa-download"></i>
</button> -->

<script>
    $(function(){
        $("button[xls]").click(function(){
            query = 
            busca = $('input[type="search"]').val();
            console.log(busca);
            //window.open('paginas/csv/csv.php?busca='+busca);
        });
    })
</script>