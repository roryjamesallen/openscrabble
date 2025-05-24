<?php

function saveArrayFile($file, $array) {
    file_put_contents($file, json_encode($array));
}

function readArrayFile($filename) {
    return json_decode(file_get_contents($filename), true);
}

function createGame($game_id) {
	$game_folder = "games/".$game_id."/";
    mkdir($game_folder);
    saveArrayFile(
        $game_folder."board.txt",
        ["","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","",""]
    );
    $initial_tilebag = ["E","E","E","E","E","E","E","E","E","E","E","E","A","A","A","A","A","A","A","A","A","I","I","I","I","I","I","I","I","I","O","O","O","O","O","O","O","O","N","N","N","N","N","N","R","R","R","R","R","R","T","T","T","T","T","T","D","D","D","D","L","L","L","L","S","S","S","S","U","U","U","U","G","G","G","B","B","C","C","F","F","H","H","M","M","P","P","V","V","W","W","Y","Y","J","K","Q","X","Z","*","*"];

    foreach (["red", "blue", "green", "yellow"] as $user) {
        $user_hand = [];
        for ($tile = 0; $tile < 7; $tile++) {
            
            $random_letter_index = array_rand($initial_tilebag); /* Pick a random index from the tilebag */
            $random_letter = $initial_tilebag[$random_letter_index]; /* Get the letter at the index */
            $user_hand[] = $random_letter; /* Add the letter to the user's hand */
            array_splice($initial_tilebag, $random_letter_index, 1); /* Remove letter from tilebag */
        }
        saveArrayFile($game_folder.$user."_hand.txt", $user_hand);
    }
    saveArrayFile($game_folder."/tilebag.txt", $initial_tilebag);
    saveArrayFile($game_folder."/recallable.txt", []);
    $user_starting = "red"; /* Make random or posted based on who's creating game */
    saveArrayFile($game_folder."/users_turn.txt", [$user_starting]);
}

?>
