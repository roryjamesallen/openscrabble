<?php
include 'php_library.php';

$game_id = $_POST['game_id'];
$game_folder = $game_id."/";

sleep(1); /* Server delay to prevent rapid polls bogging it down */

$board = readArrayFile($game_folder.'board.txt');
$users_turn = readArrayFile($game_folder.'users_turn.txt');
$recallable = readArrayFile($game_folder.'recallable.txt');

header('Content-Type: application/json');
echo json_encode([$board, $users_turn, $recallable]);
?>
