<?php
include "../../../../lib/includes.php";

$urlServicos = 'paginas/servicos/saude';

$_SESSION['categoria'] = (($_GET['categoria']) ?: $_SESSION['categoria']);

$categoria = (($_GET['categoria']) ?: $_SESSION['categoria']);
list($cat_cod, $cat_desc) = mysqli_fetch_row(mysqli_query($con, "select codigo, descricao from categorias where codigo = '{$categoria}'"));

function getEsfera()
{
    return [
        'Municipal', 'Estadual'
    ];
}

function getSituacao()
{
    return [
        'tramitacao' => 'Tramitação',
        'retorno' => 'Retorno',
        'concluido' => 'Concluído',
    ];
}


function getSituacaoOptions($situacao)
{
    $list = getSituacao();
    return $list[$situacao];
}

function getAtendimento()
{
    return [
        'Atendido',
        'Não atendido',
        'Agendado',
        'Aguardando'
    ];
}

function getAtendimentoOptions($situacaoAtendimento)
{
    $list = getAtendimento();
    return $list[$situacaoAtendimento];

}