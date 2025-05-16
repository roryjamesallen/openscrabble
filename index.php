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
        echo "<div id='tile-".$tile_index."' class='scrabble-tile ".$tile_class."' onclick='attemptTileMove(this)'>".$tile_letter."</div>";
    }
}

/* If php posted to itself then update file values before anything else */
if (!empty($_POST)) {
	$new_board_array = $_POST['new_board_array'];
	$new_tilebag = $_POST['new_tilebag'];
	$new_hand = $_POST['new_hand'];
	saveArrayFile('game.txt', $new_board_array);
	saveArrayFile('tilebag.txt', $new_tilebag);
	saveArrayFile('user_hand_1.txt', $new_hand);
}

$tilebag = readArrayFile('tilebag.txt');
$initial_board = readArrayFile('game.txt');
$initial_hand = readArrayFile('user_hand_1.txt');
?>

<html>
    <head>
    <link rel="stylesheet" href="styles.css">
    </head>

    <body>
     
     <div id="main-container" class="main-container">
     <div id="scrabble-board" class="scrabble-board">
<?php
     renderBoard($initial_board);
?>
     </div>

     <div id="hand" class="hand">
<?php
     foreach ($initial_hand as $tile_index => $tile_letter) {
         echo "<div id='hand-tile-".$tile_index."' class='scrabble-tile letter-tile' onclick='setHolding(this)'>".$tile_letter."</div>";
     }
?>
     </div>
	 
	 <div id='holding-tile' class='scrabble-tile letter-tile'></div>
	 <div id='recall-button' class='scrabble-tile letter-tile button' onclick="recallHand()">Recall</div>
	 <div id='go-button' class='scrabble-tile letter-tile button' onclick="makeTurn()">Make Turn</div>
	 
     </div>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script>
	tilebag = <?php echo json_encode($tilebag); ?>; /* Get the tilebag contents at the start */
	
    function setHolding(tile) {
		tile_letter = tile.innerHTML;
		holding_tile = document.getElementById('holding-tile')
		if (tile_letter != "" && holding_tile.innerHTML == "") {
			holding_tile.innerHTML = tile_letter;
			tile.innerHTML = "";
		} else {
			attemptTileMove(tile); /* Put the held tile back in your hand */
		}
    }
	
	function attemptTileMove(slot) {
		tile_in_hand = document.getElementById('holding-tile');
		tile_in_hand_letter = tile_in_hand.innerHTML;
		if (tile_in_hand_letter != "" && slot.innerHTML == "") { /* If there is a tile in your hand and the slot is free */
			slot.innerHTML = tile_in_hand_letter; /* Set the slot to the tile's letter */
			slot.classList.add("letter-tile");
			slot.classList.add("recallable");
			tile_in_hand.innerHTML = ""; /* Remove the tile from your hand */
		}
	}
	
	function recallHand() {
		board = document.getElementById("scrabble-board");
		hand = document.getElementById("hand");
			for (const tile of board.children) {
				if (tile.classList.contains("recallable")) {
					for (const slot of hand.children) {
						if (slot.innerHTML == "") {
							slot.innerHTML = tile.innerHTML;
							tile.innerHTML = "";
							tile.classList.remove("letter-tile");
							tile.classList.remove("recallable");
						}
					}
				}
			}
	}
	
	function generateBoardArray() {
		board = document.getElementById("scrabble-board");
		board_array = [];
		for (const tile of board.children) {
			tile_id = tile.id;
			tile_index = tile_id.split('-');
			tile_letter = tile.innerHTML;
			tile_classes = tile.classList;
			if (tile_classes.contains('double-letter')) {
				tile_class = 1;
			} else if (tile_classes.contains('triple-letter')) {
				tile_class = 2;
			} else if (tile_classes.contains('double-word')) {
				tile_class = 3;
			} else if (tile_classes.contains('triple-word')) {
				tile_class = 4;
			} else {
				tile_class = 0;
			}
			board_array.push([tile_class, tile_letter]);
		}
		return board_array;
	}
	
	function removeLastChar(string) {
		return string.substring(0, string.length - 1); /* Remove last comma */
	}
	
	function makeTurn() {
		board_array = generateBoardArray();
		hand = document.getElementById("hand");
		final_hand = [];
		for (const tile of hand.children) {
			if (tile.innerHTML == "") { /* Tile that needs replacing */
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
		
	    $.post( "index.php", { new_board_array: board_array, new_tilebag: tilebag, new_hand: final_hand } );
	}
    </script>
    </body>
</html>
