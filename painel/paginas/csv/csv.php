<?php

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=relatorio.csv');
    header('Pragma: no-cache');

    include("./config_csv.php");

    // beneficido  b.nome
    // assessor    a.nome
    // data_agenda s.data_agenda
    // situacao    s.situacao
    // local       lf.descricao
    // local_responsavel s.local_responsavel
    // Especialidade s.especialista

    $where = false;
    if(trim($_GET['busca'])){
        $opc = explode(" ",$_GET['busca']);
        $nivel_campo = [];
        for($i=0;$i<count($opc);$i++){
            $b = trim($opc[$i]);
            $nivel_campo['beneficido'] = "b.nome like '%{$b}%'";
            if($_SESSION['saude_xls']){
                $nivel_campo['especialista'] = "s.especialista like '%{$b}%'";
            }else{
                $nivel_campo['especialista'] = false;
            }
            $nivel_campo['assessor'] = "a.nome like '%{$b}%'";
            if(dataMysql($b)){
            $nivel_campo['data_agenda'] = "s.data_agenda like '%".dataMysql($b)."%'";
            }else{
                $nivel_campo['data_agenda'] = false;
            }
            if(Sts($b)){
            $nivel_campo['situacao'] = "s.situacao = '{$b}'";
            }else{
                $nivel_campo['situacao'] = false;
            }
            $nivel_campo['local'] = "lf.descricao like '%{$b}%'";
            $nivel_campo['local_responsavel'] = "s.local_responsavel like '%{$b}%'";

            // foreach($nivel_campo as $ind => $val){
            $nivel_campo = array_filter($nivel_campo, 'strlen');
            $nivel_where[] = "(".implode(" or ", $nivel_campo).")";
            // }

        }
        // $nivel_where = [];
        // foreach($nivel_campo as $ind => $val){
        //     $nivel_where[] = " AND (".implode(" or ", $val).")";
        // }
        $where = implode(" OR ", $nivel_where);
    }

    // $Query = [];
    // $query_xls = [];
    // for($i=0;$i<count($nivel_where);$i++){

    //     $Query[] = str_replace("ORDER BY", $nivel_where[$i] . " ORDER BY ", $_SESSION['query_xls']);

    // }

    // if($Query) $query_xls = "(".implode(") UNION (", $Query).")";

    if($where) {$query_xls = str_replace("WHERE", "WHERE ".$where." AND ", $_SESSION['query_xls']);}else{$query_xls = $_SESSION['query_xls'];}

    // echo "<pre>";
    // print_r($_SESSION);
    // echo "</pre>";
    // echo "<hr>";
    // echo "<pre>";
    // print_r($_GET);
    // echo "</pre>";
    // echo $query_xls;
    // echo "\n\n";
    $result = mysqli_query($con, $query_xls);
?>
Beneficiado;Assessor;<?=(($_SESSION['saude_xls'])?'Especialidade;':false)?>Data da Agenda;Situação;Local
<?php
    while ($d = mysqli_fetch_object($result)){
?>
<?= $d->beneficiado; ?>;<?= $d->assessor; ?>;<?=(($_SESSION['saude_xls'])?$d->especialista.';':false)?><?= formata_datahora($d->data_agenda, DATA_HM); ?>;<?= getSituacaoOptions($d->situacao); ?>;<?= $d->lf_descricao . (($d->local_responsavel)?' ('.$d->local_responsavel.')':false); ?><?="\n"?>
<?php
    }
?>