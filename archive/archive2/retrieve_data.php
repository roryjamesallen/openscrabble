<?php
include 'php_library.php';

sleep(1); /* Server delay to prevent rapid polls bogging it down */

$board = readArrayFile('board.txt');
$tilebag = readArrayFile('tilebag.txt');

header('Content-Type: application/json');
echo json_encode([$board, $tilebag]);
?>
