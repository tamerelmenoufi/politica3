<?php
header('Content-Type: application/json'); // set json response headers
$file = "docs/{$_POST['key']}.pdf";
echo delete($file);
//exit();

function delete($file)
{
    if (is_file($file)) {
        if (@unlink($file)) {
            return json_encode([
                'initialPreview' => false
            ]);
        } else {
            return ['error' => 'Error ao excluír arquivo'];
        }
    } else {
        return ['error' => 'Nenhum arquivo encontrado'];
    }
}

?>