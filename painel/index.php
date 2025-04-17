<?php
include "../lib/includes.php";

if ($_POST['acao'] === 'verifica_sessao') {
    echo json_encode(["sessao" => isset($_SESSION['usuario'])]);
    exit();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: ../admin/");
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Painel - Dashboard</title>

    <?php include "../lib/header.php"; ?>

    <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet"
    >

    <link rel="stylesheet" href="css/loading.css">
    <link rel="stylesheet" href="<?= $caminho_vendor ?>/datatables/datatables.min.css">
    <link rel="stylesheet" href="<?= $caminho_vendor ?>/datatables/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= $caminho_vendor ?>/jquery-confirm/css/jquery-confirm.min.css">
    <link rel="stylesheet" href="<?= $caminho_vendor ?>/bootstrap-select/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="<?= $caminho_vendor ?>/bootstrap-fileinput/css/fileinput.min.css">
</head>

<body id="page-top">
<style>
    .bg-gray-custom {
        background-color: #f8f9fc !important;
    }

    .mw-20 {
        width: 20% !important;
    }
</style>
<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <?php include "sidebar.php"; ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <?php include "header.php"; ?>

        <!-- Main Content -->
        <div id="content" style="position: relative">
            <div class="loading">
                <div class="loader"></div>
            </div>
            <!-- Begin Page Content -->
            <div class="container-fluid">
                <div id="palco">
                    <?php include "content.php"; ?>
                </div>
            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <?php include "footer.php"; ?>
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Aviso</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Selecione "Sair" abaixo se estiver pronto para encerrar sua sessão atual.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" href="../">Sair</a>
            </div>
        </div>
    </div>
</div>

<script src="<?= "{$caminho_vendor}/jquery/jquery.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/bootstrap4/js/bootstrap.bundle.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/bootstrap-select/js/bootstrap-select.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/bootstrap-select/js/i18n/defaults-pt_BR.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/jquery/jquery.easing.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/jquery/jquery.validate.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/jquery/jquery.maskedinput.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/startbootstrap-sb-admin-2/js/sb-admin-2.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/jquery-confirm/js/jquery-confirm.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/fontawesome/js/all.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/tata/tata.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/datatables/datatables.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/datatables/dataTables.bootstrap4.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/tata/index.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/bootstrap-fileinput/js/fileinput.min.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/bootstrap-fileinput/js/locales/pt-BR.js"; ?>"></script>
<script src="<?= "{$caminho_vendor}/bootstrap-fileinput/theme/theme.min.js"; ?>"></script>
<script src="<?= $caminho_vendor; ?>/chart/chart.min.js"></script>
<script>
    $(document).ready(function () {

        //Datatables
        $.extend(true, $.fn.dataTable.defaults, {
            "language": {
                "url": "<?= $caminho_vendor; ?>/datatables/pt_br.json",
                responsive: true
            },
            "order": [],
            "columnDefs": [{
                targets: 'no-sort',
                orderable: false,
            }],
            stateSave: true,
        });

        //Jconfirm
        jconfirm.defaults = {
            typeAnimated: true,
            type: "blue",
            smoothContent: true,
        }

        //Validate
        jQuery.validator.setDefaults({
            errorClass: 'text-danger',
            validClass: 'text-success',
            errorElement: 'span',
        });

        jQuery.extend(jQuery.validator.messages, {
            required: 'Campo obrigatórios',
        });

        // Bootstrap select
        // $.fn.selectpicker.Constructor.DEFAULTS.noneSelectedText = 'Selecione';

        // $.fn.selectpicker.Constructor.DEFAULTS.mobile = true;


    });

    $(function () {

        $(document).on('click', '[url]', function (e) {
            e.preventDefault();
            var url = $(this).attr('url');

            console.log(url);
            $('.loading').fadeIn(200);

            localStorage.clear();

            $.ajax({
                url: "index.php",
                type: "POST",
                dataType: "JSON",
                data: {acao: "verifica_sessao"},
                success: function (response) {
                    if (!response.sessao) {
                        window.location = '../admin';
                    }
                }
            });

            var lmp = $(this).attr('acao');

            $.ajax({
                url,
                data:{
                    lmp
                },
                success: function (data) {
                    $('#palco').html(data);
                }
            })
                .done(function () {
                    $('.loading').fadeOut(200);
                })
                .fail(function (error) {
                    alert('Error');
                    $('.loading').fadeOut(200);
                })
        });

    });
</script>

</body>
</html>