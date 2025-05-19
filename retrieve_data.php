<?php

/* Send a string after a random number of seconds (2-10) */

function readArrayFile($filename) {
    return json_decode(file_get_contents($filename), true);
}

sleep(1);

$board = readArrayFile('board.txt');
$red_hand = readArrayFile('red_hand.txt');
$blue_hand = readArrayFile('blue_hand.txt');

header('Content-Type: application/json');
echo json_encode([$board, $blue_hand, $blue_hand]);

?>
