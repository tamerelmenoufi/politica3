<?php
include '../connection.php';

$tabelas = [
    'acao_social',
    'assessores',
    'beneficiados',
    'categorias',
    'especialidades',
    'local_fontes',
    'municipios',
    'oficios',
    'permissoes',
    'secretarias',
    'servicos',
    'servicos_tipo',
    'usuarios',
];

foreach ($tabelas as $tabela) {
    $query = "ALTER TABLE {$tabela} ADD COLUMN deletado ENUM('0','1') DEFAULT '0'";
    $result = mysqli_query($con, $query);
    echo $tabela . ' - ' . ($result ? 'Criado' : 'Não Criado') . '<br>';
}