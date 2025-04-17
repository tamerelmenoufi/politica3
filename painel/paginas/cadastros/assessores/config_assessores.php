<?php
include "../../../../lib/includes.php";

$urlAssessores = 'paginas/cadastros/assessores';

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

function getResponsavel()
{
    return [
        'Felipe Souza' => 'Felipe Souza',
        'Thaysa Lippy' => 'Thaysa Lippy'
    ];
}

function getResponsavelOptions($responsavel)
{
    $list = getResponsavel();
    return $list[$responsavel];
}