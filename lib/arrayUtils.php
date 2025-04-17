<?php

function getSituacao()
{
    return [
        'concluido' => 'Concluído',
        'tramitacao' => 'Tramitação',
        'retorno' => 'Retorno',
    ];
}

function getSituacaoOptions($situacao)
{
    $list = getSituacao();
    return $list[$situacao];
}