<?php
include 'php_library.php';

sleep(1); /* Server delay to prevent rapid polls bogging it down */

$board = readArrayFile('board.txt');
$users_turn = readArrayFile('users_turn.txt');
$recallable = readArrayFile('recallable.txt');

header('Content-Type: application/json');
echo json_encode([$board, $users_turn, $recallable]);
?>
