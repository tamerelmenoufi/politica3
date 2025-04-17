<?php
include "../../../../lib/includes.php";

$urlServicos = 'paginas/servicos/odontologia';

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
