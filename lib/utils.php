<?php
const DATA = 'd/m/Y';
const DATA_HMS = 'd/m/Y H:i:s';
const DATA_HM = 'd/m/Y H:i';
const HORA_MINUTO = 'H:i';

function formata_datahora($datahora, $formato = null)
{

    if(substr($datahora,0,4) == '0000'){
        return '<span class="text-danger">Não Informada</span>';
    }

    if (!$formato) $formato = 'd/m/Y H:i:s';

    if ($datahora == 0) return '(Não definido)';

    return date($formato, strtotime($datahora));
}

function dataMysql($dt)
{
    list($data, $hora) = explode(" ",$dt);
    list($d, $m, $a) = explode("/", $data);
    if($d*1 > 0 and $m*1 > 0 and $a*1 > 0){
        return "{$a}-{$m}-{$d}".(($hora)?" ".$hora:false);
    }
    else{
        return false;
    }
}

function Sts($st)
{
    $opc = [
        'Tramitação' => 'tramitacao',
        'Retorno' => 'retorno',
        'Concluído' => 'concluido',
    ];

    if($opc[$st]){
        return $opc[$st];
    }else{
        return false;
    }

}