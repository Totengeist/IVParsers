<?php
require_once("helper/IVFileParser.php");
require_once("helper/ShipFile.php");

use IVParser\IVFile;
use IVParser\ShipFile;

$filename = $_FILES['file']['tmp_name'];
try {
    echo json_encode((new ShipFile(file_get_contents($filename)))->info);
} catch (Exception $e) {
    http_response_code(403);
    echo json_encode(['error' => $e->getMessage()]);
}

unlink($filename);
