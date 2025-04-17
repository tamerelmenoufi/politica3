<?php
include "../../../../lib/includes.php";

$urlUsuarios = 'paginas/cadastros/usuarios';

function getSexo()
{
    return [
        'm' => 'Masculino',
        'f' => 'Feminino'
    ];
}

function getSexoOptions($sexo)
{
    $list = getSexo();
    return $list[$sexo];
}

function getSituacao()
{
    return [
        '0' => 'Inativo',
        '1' => 'Ativo',
    ];
}

function getSituacaoOptions($situacao)
{
    $list = getSituacao();
    return $list[$situacao];
}
