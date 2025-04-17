<?php
include "../../../../lib/includes.php";

$urlBeneficiados = 'paginas/cadastros/beneficiados';

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
        'FELIPE SOUSA' => 'Felipe Sousa',
        'THAYSA' => 'Thaysa'
    ];
}

function getResponsavelOptions($responsavel)
{
    $list = getResponsavel();
    return $list[$responsavel];
}