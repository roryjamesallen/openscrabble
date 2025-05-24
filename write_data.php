<?php

function saveArrayFile($file, $array) {
    file_put_contents($file, json_encode($array));
}

function readArrayFile($filename) {
    return json_decode(file_get_contents($filename), true);
}

$game_id = $_POST['game_id'];
$game_folder = "games/".$game_id."/";

if (!empty($_POST['board'])) { /* If a full board rewrite is sent */
    $board = $_POST['board'];
    saveArrayFile($game_folder.'board.txt', $board);
}

if (!empty($_POST['tile'])) { /* If only one tile is being written start from current board */
    $board = readArrayFile($game_folder.'board.txt'); 
    
    $tile_index = $_POST['tile'][0]; /* Index of tile in board array */
    $tile_value = $_POST['tile'][1]; /* Letter of tile to write */
    
    $board[$tile_index] = $tile_value;
    saveArrayFile($game_folder.'board.txt', $board);
}

if (!empty($_POST['hand'])) { /* If updating the hand */
    $user = $_POST['user'];
    $user_file = $game_folder.$user.'_hand.txt';
    $hand = $_POST['hand'];
    saveArrayFile($user_file, $hand);
}

if (!empty($_POST['tilebag'])) { /* If updating the hand */
    $tilebag = $_POST['tilebag'];
    saveArrayFile($game_folder.'tilebag.txt', $tilebag);
}

if (!empty($_POST['users_turn'])) { /* If updating who's turn it is */
    $new_users_turn = $_POST['users_turn'];
    saveArrayFile($game_folder.'users_turn.txt', [$new_users_turn]);
}

if (!empty($_POST['recallable'])) { /* If updating the hand */
    saveArrayFile($game_folder.'recallable.txt', $_POST['recallable']);
} else {
    saveArrayFile($game_folder.'recallable.txt', []);
}

?>
