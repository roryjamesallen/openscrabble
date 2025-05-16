<?php
$tile_types = [
    "",
    "double-letter",
    "triple-letter",
    "double-word",
    "triple-word"
];

function saveArrayFile($file, $array) {
    file_put_contents($file, json_encode($array));
}

function readArrayFile($filename) {
    return json_decode(file_get_contents($filename), true);
}

function renderBoard($board) {
    global $tile_types;
    foreach ($board as $tile_index => $tile) {
        $tile_letter = $tile[1];
        if($tile_letter != ""){
            $tile_class = "letter-tile";
        } else {
            $tile_class = $tile_types[$tile[0]];
        }
        echo "<input name='board-tile-".$tile_index."' value='".$tile_letter."' class='scrabble-tile ".$tile_class."' onclick='attemptTileMove(this)'/>";
    }
}

$tilebag = readArrayFile('tilebag.txt');
$initial_board = readArrayFile('game.txt');
$initial_hand = readArrayFile('user_hand_1.txt');

/* If php posted to itself then update file values before anything else */
if (!empty($_POST)) {

    /* Add placed tiles to the new baord and save to file */
    $new_board = [];
    for ($i = 0; $i < 225; $i++) {
        $tile_name = "board-tile-{$i}";
        $tile_letter = $_POST[$tile_name];
        $tile_class = $initial_board[$i][0];
        $new_board[] = [$tile_class, $tile_letter];
    }
	saveArrayFile('game.txt', $new_board);
    $initial_board = $new_board;

    /* Replace placed tiles with random new ones in the user's hand */
    $new_hand = [];
    for ($i = 0; $i < 7; $i++) {
        $tile_name = "hand-tile-{$i}";
        $tile_letter = $_POST[$tile_name];
        if ($tile_letter == "" && count($tilebag) > 0) {
            $new_tile = $tilebag[array_rand($tilebag)]; /* Pick a random new letter from the tilebag */
            unset($tilebag[array_search($new_tile, $tilebag)]); /* Remove the new tile from the tilebag */
            $new_hand[] = $new_tile; /* Put the new letter in the users hand */
            
        } else {
            $new_hand[] = $initial_hand[$i];
        }
    }
	saveArrayFile('user_1_hand.txt', $new_hand);
    $initial_hand = $new_hand;
}

?>

<html>
    <head>
    <link rel="stylesheet" href="styles.css">
    </head>

    <body>
 
     <form action="<?=$_SERVER['PHP_SELF']?>" method="post" class="main-container">
         <div id="scrabble-board" class="scrabble-board">
             <?php renderBoard($initial_board); ?>
         </div>

         <div id="hand" class="hand">
         <?php foreach ($initial_hand as $tile_index => $tile_letter) {
             echo "<input name='hand-tile-".$tile_index."' value='".$tile_letter."' class='scrabble-tile letter-tile' onclick='setHolding(this)'/>";
         } ?>
         </div>
	 
         <input id='holding-tile' value="" class='scrabble-tile letter-tile'></div>
	     <div id='recall-button' class='scrabble-tile letter-tile button' onclick="recallHand()">Recall</div>
     
         <input type="submit" value="Make Turn" name="go-button" class="scrabble-tile letter-tile button"></input>
	</form> 

    <script>
    function setHolding(tile) {
		tile_letter = tile.value;
		holding_tile = document.getElementById('holding-tile')
		if (tile_letter != "" && holding_tile.value == "") {
			holding_tile.value = tile_letter;
			tile.value = "";
		} else {
			attemptTileMove(tile); /* Put the held tile back in your hand */
		}
    }
	
	function attemptTileMove(slot) {
		tile_in_hand = document.getElementById('holding-tile');
		tile_in_hand_letter = tile_in_hand.value;
		if (tile_in_hand_letter != "" && slot.value == "") { /* If there is a tile in your hand and the slot is free */
			slot.value = tile_in_hand_letter; /* Set the slot to the tile's letter */
			slot.classList.add("letter-tile");
			slot.classList.add("recallable");
			tile_in_hand.value = ""; /* Remove the tile from your hand */
		}
	}
	
	function recallHand() {
		board = document.getElementById("scrabble-board");
		hand = document.getElementById("hand");
			for (const tile of board.children) {
				if (tile.classList.contains("recallable")) {
					for (const slot of hand.children) { /* Find the next empty hand slot */
						if (slot.value == "") {
							slot.value = tile.value;
							tile.value = "";
							tile.classList.remove("letter-tile");
							tile.classList.remove("recallable");
						}
					}
				}
			}
	}

	function makeTurn() {
		board_array = generateBoardArray();
		hand = document.getElementById("hand");
		final_hand = [];
		for (const tile of hand.children) {
			if (tile.value == "") { /* Tile that needs replacing */
				if (tilebag.length != 0) {
					replacement_tile = tilebag[Math.floor(Math.random() * tilebag.length)];; /* Pick a random tile from the bag */
					final_hand.push(replacement_tile); /* Put the tile in the user's hand */
					bag_tile = tilebag.indexOf(replacement_tile); /* Find one (first) instance of tile in bag */
					tilebag.splice(bag_tile, 1); /* Remove the tile from the bag */
				}
			} else {
				
				final_hand.push(tile.innerHTML);
			}
		}
	}
    </script>
    </body>
</html>
