<?php
session_start();
require_once("../../../configFinal.php");
include_once 'translation.php';
require_once __DIR__ . '../../../../vendor/autoload.php';

use Dompdf\Dompdf;

if(isset($_POST['export_pdf'])) {
    $dompdf = new Dompdf;

    $options = $dompdf->getOptions();
    $options->set('defaultFont', 'Arial');

    ob_start();
    include 'content.php';
    $html = ob_get_clean();

    $html = str_replace('<?php echo translate(', '<?php echo translate(\'', $html);
    $html = str_replace('); ?>', '\'); ?>', $html);

    $dompdf->loadHtml($html);
    $dompdf->render();
    $dompdf->stream();
}
?>
