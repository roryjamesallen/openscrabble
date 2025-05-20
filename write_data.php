<?php

function saveArrayFile($file, $array) {
    file_put_contents($file, json_encode($array));
}

function readArrayFile($filename) {
    return json_decode(file_get_contents($filename), true);
}

if (!empty($_POST['board'])) { /* If a full board rewrite is sent */
    $board = $_POST['board'];
    saveArrayFile('board.txt', $board);
}

if (!empty($_POST['tile'])) { /* If only one tile is being written start from current board */
    $board = readArrayFile('board.txt'); 
    
    $tile_index = $_POST['tile'][0]; /* Index of tile in board array */
    $tile_value = $_POST['tile'][1]; /* Letter of tile to write */
    
    $board[$tile_index] = $tile_value;
    saveArrayFile('board.txt', $board);
}

if (!empty($_POST['hand'])) { /* If updating the hand */
    $user = $_POST['user'];
    $user_file = $user.'_hand.txt';
    $hand = readArrayFile($user_file);
    $hand[$_POST['hand'][0]] = $_POST['hand'][1];
    saveArrayFile($user_file, $hand);
}

?>
