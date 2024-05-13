<?php
session_start();
require_once("../../../configFinal.php");
include_once 'translation.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo translate('Používatelská príručka'); ?></title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</head>
<body class="bg-dark-custom">
<?php include "menu.php"; ?>
<div class="container-md p-1 mt-4 mb-4">
    <?php include "content.php"; ?>
    <form method="post" id="pdfForm" action="export_pdf.php">
        <div class="buttons">
            <button type="submit" name="export_pdf" id="exportPdfBtn" class="btn btn-outline-secondary">Exportovať do
                PDF
            </button>
        </div>
    </form>
</div>

</body>
</html>
