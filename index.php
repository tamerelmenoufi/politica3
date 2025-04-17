<?php
include "lib/includes.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Politica</title>
    <meta charset="UTF-8">
    <meta name="description" content="Sistema de Gerenciamento">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <?php include "lib/header.php"; ?>
</head>

<body>

<style>
    .icon-lg {
        font-size: 50px
    }

    .shadow,
    .subscription-wrapper {
        box-shadow: 0 15px 39px 0 rgba(8, 18, 109, 0.1) !important
    }

    .icon-primary {
        color: #062caf
    }

    .icon-bg-circle {
        position: relative
    }

    .icon-lg {
        font-size: 50px
    }

    .icon-bg-circle::before {
        z-index: 1;
        position: relative
    }

    .icon-bg-circle::after {
        content: '';
        position: absolute;
        width: 68px;
        height: 68px;
        top: -35px;
        left: 15px;
        border-radius: 50%;
        background: inherit;
        opacity: .1
    }

    .icon-bg-orange::after {
        background: #ff7c17
    }

    .icon-orange {
        color: #ff7c17
    }

    .icon-bg-blue::after {
        background: #3682ff
    }

    .icon-blue {
        color: #3682ff
    }

    a, a:hover {
        text-decoration: none;
        color: #858796;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-lg-12 text-center mt-5">
            <h2 class="section-title">Área de Atendimento</h2>
        </div>

        <div class="row d-lg-flex justify-content-lg-center mt-3">

            <div class="col-lg-4 col-md-6 col-12 mb-4">
                <a href="consulta_agenda/">
                    <div class="card border-0 shadow rounded-xs pt-5" style="height: 300px">
                        <div class="card-body">
                            <i class="fa-regular fa-calendar icon-lg icon-blue icon-bg-blue icon-bg-blue icon-bg-circle mb-3"></i>
                            <h4 class="mt-4 mb-3">Consulta de agendas</h4>
                            <p>
                                Essa funcionalidade possibilita que o operador faça uma consulta à agenda de um
                                determinado
                                serviço
                            </p>
                        </div>
                    </div>
                </a>
            </div>


            <div class="col col-lg-4 col-md-6 col-12 mb-4">
                <a href="admin/">
                    <div class="card border-0 shadow rounded-xs pt-5" style="height: 300px">
                        <div class="card-body">
                            <i class="fa fa-user icon-lg icon-orange icon-bg-orange icon-bg-circle mb-3"></i>
                            <h4 class="mt-4 mb-3">Area administrativa</h4>
                            <p>Acessar area administrativa do sistema</p>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>
</body>

<script src="<?= $caminho_vendor; ?>/jquery/jquery.min.js"></script>

<script src="<?= $caminho_vendor; ?>/bootstrap4/js/bootstrap.bundle.min.js"></script>
<script src="<?= $caminho_vendor; ?>/startbootstrap-sb-admin-2/js/sb-admin-2.min.js"></script>

<script>
    $(function () {
        //$('body').load('autenticacao/login.php');
    });
</script>
</html>
