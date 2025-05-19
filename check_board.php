<?php

/* Send a string after a random number of seconds (2-10) */

function readArrayFile($filename) {
    return json_decode(file_get_contents($filename), true);
}

sleep(1);

$board = readArrayFile('board.txt');

header('Content-Type: application/json');
echo json_encode($board);

?>
