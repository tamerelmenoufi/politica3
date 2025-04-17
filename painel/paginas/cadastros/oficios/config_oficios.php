<?php
include "../../../../lib/includes.php";

$urlOficios = 'paginas/cadastros/oficios';

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

function uploadPdf($codigo)
{
    global $_FILES;
    global $_POST;

    #file_put_contents('docs/debug.txt', json_encode($_FILES['file']['tmp_name']));
    $targetDir = 'docs';
    $fileBlob = 'file';

    if (!file_exists($targetDir) and isset($_FILES[$fileBlob])) {
        @mkdir($targetDir);
    }

    if (isset($_FILES[$fileBlob])) {
        $file = $_FILES[$fileBlob]['tmp_name'];
        $fileName = $_POST['fileName'];
        $fileSize = $_POST['fileSize'];
        $fileId = $_POST['fileId'];
        $index = $_POST['chunkIndex'];
        $totalChunks = $_POST['chunkCount'];
        $targetFile = $targetDir . '/' . $codigo . '.pdf';

        if ($totalChunks > 1) {
            $targetFile .= '_' . str_pad($index, 4, '0', STR_PAD_LEFT);
        }

        if (move_uploaded_file($file, $targetFile)) {

            $chunks = glob("{$targetDir}/{$fileName}_*");
            $allChunksUploaded = $totalChunks > 1 && count($chunks) == $totalChunks;

            if ($allChunksUploaded) {
                $outFile = $targetDir . '/' . $fileName;
                combineChunks($chunks, $outFile);
            }
            $zoomUrl = 'docs/' . $fileName;

            return 'ok';
        } else {
            return [
                'error' => 'Error uploading chunk ' . $_POST['chunkIndex']
            ];
        }
    }
    return [
        'error' => 'No file found'
    ];

}

function combineChunks($chunks, $targetFile)
{
    $handle = fopen($targetFile, 'a+');

    foreach ($chunks as $file) {
        fwrite($handle, file_get_contents($file));
    }

    foreach ($chunks as $file) {
        @unlink($file);
    }

    fclose($handle);
}
