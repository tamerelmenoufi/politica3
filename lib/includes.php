<?php
error_reporting(0);
include "connection.php";
include "config.php";
include "utils.php";
include "fn.php";
#include "arrayUtils.php";

if($_GET['lmp'] == 'limpar_busca'){
    $_SESSION['campo_busca'] = false;
    $_SESSION['campo_situacao'] = false;
    $_SESSION['pgInicio'] = 0;
}
