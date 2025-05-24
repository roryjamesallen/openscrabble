<html>
  <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<?php
     if (!empty($_POST['user'])) {
         $user = strtolower($_POST['user']);
     } else {
         header('Location: scrabble.php');
     }
?>
  </head>
      
  <body onload="loadingScreen()">
    <style>
         :root {
         --grey: #555555;
         --beige: #f1e7d4;
         --double-letter: #aabcbe;
         --triple-letter: #606e7b;
         --double-word: #e8a692;
         --triple-word: #a43236;
         --letter-tile: #f9f4ed;
            }
      
body {
    font-family: Helvetica;
}

.main-container {
    display: flex;
    flex-wrap: wrap;
    gap: 50px;
    margin: auto;
    width: 750px;
}
      
.loading {
    position: absolute;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    padding-top: calc(50vh - 50px);
    text-align: center;
    font-size: 100px;
    background-color: white;
    z-index: 999;
}

.board {
    display: flex;
    flex-wrap: wrap;
    width: 750px;
}

.hand {
    display: flex;
    justify-content: space-evenly;
    width: 600px;
    border: 5px solid #005500;
    box-sizing: border-box;
    background-color: green;
    border-top-color: green;
}

.go-button {
    flex-grow: 1;
}

.tile {
    width: 50px;
    height: 50px;
    line-height: 50px;
    text-align: center;
    font-size: 35px;
    background-color: var(--beige);
    border: 2px solid white;
    border-radius: 5px;
    box-sizing: border-box;
    position: relative;
}
.tile::after {
    display: block;
    position: absolute;
    bottom: -15px;;
    right: 2px;
    font-size: 15px;
}

.letter {
    background-color: var(--letter-tile) !important;
    border-color: var(--grey) !important;
}

.recallable {
    border-color: <?php echo $user ?> !important;
}
      
.e::after, .a::after, .i::after, .o::after, .n::after, .r::after, .t::after, .l::after, .s::after, .u::after {
    content: "1";
}
.d::after, .g::after {
    content: "2";
}
.b::after, .c::after, .m::after, .p::after {
    content: "3";
}
.f::after, .h::after, .v::after, .w::after, .y::after {
    content: "4";
}
.k::after {
    content: "5";
}
.j::after, .x::after {
    content: "8";
}
.q::after, .z::after {
    content: "10";
}

.double-letter {
    background-color: var(--double-letter);
}
.triple-letter {
    background-color: var(--triple-letter);
}
.double-word {
    background-color: var(--double-word);
}
.triple-word {
    background-color: var(--triple-word);
}
    </style>

    <div class="main-container">
      <div id="board" class="board"></div>
      <div id="hand" class="hand"></div>
      <div id="go" class="go-button tile">Go</div>
    </div>
    
  </body>


  <script>
multiplier_classes = [
        'plain',
        'double-letter',
        'triple-letter',
        'double-word',
        'triple-word'
]
    
multipliers = [
	4,0,0,1,0,0,0,4,0,0,0,1,0,0,4,
	0,3,0,0,0,2,0,0,0,2,0,0,0,3,0,
	0,0,3,0,0,0,1,0,1,0,0,0,3,0,0,
	1,0,0,3,0,0,0,1,0,0,0,3,0,0,1,
	0,0,0,0,3,0,0,0,0,0,3,0,0,0,0,
	0,2,0,0,0,2,0,0,0,2,0,0,0,2,0,
	0,0,1,0,0,0,1,0,1,0,0,0,1,0,0,
	4,0,0,1,0,0,0,3,0,0,0,1,0,0,4,
	0,0,1,0,0,0,1,0,1,0,0,0,1,0,0,
	0,2,0,0,0,2,0,0,0,2,0,0,0,2,0,
	0,0,0,0,3,0,0,0,0,0,3,0,0,0,0,
	1,0,0,3,0,0,0,1,0,0,0,3,0,0,1,
	0,0,3,0,0,0,1,0,1,0,0,0,3,0,0,
	0,3,0,0,0,2,0,0,0,2,0,0,0,3,0,
	4,0,0,1,0,0,0,4,0,0,0,1,0,0,4
]

user_hands = [
	"red",
	"blue",
	"green",
	"yellow"
]
user = "<?php echo $user ?>";
user_hand_index = user_hands.indexOf(user);
user_hand = [];
users_turn = "red";

board_letters = [];

picked_up = "";


function loadingScreen() {
	loading = document.createElement("div");
    loading.id = "loading";
	loading.classList.add('loading');
	loading.innerHTML = "Loading Game...";
	board = document.getElementById("board");
	board.appendChild(loading);
}

function loadFile(file) {
	var result = null;
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("GET", file, false);
	xmlhttp.send();
	if (xmlhttp.status==200) {
	    result = xmlhttp.responseText;
	}
	return result;
}

function addLetterToTile(tile, letter) {
    tile.innerHTML = letter;
    tile.classList.add('letter');
    if (letter != "") {
        tile.classList.add(letter.toLowerCase());
    }
}

function removeLetterFromTile(tile) {
    tile.innerHTML = "";
    tile.classList.remove('letter');
    tile.classList.remove(letter.toLowerCase());
    tile.classList.remove('recallable');
}

function saveData(data) {
    $.ajax({
        type: "POST",  //type of method
        url: "write_data.php",  //your page
        data: data,// passing the values
        success: function(res){  
                               
        }
    });
}

function clickedTile(tile) {
    if (user == users_turn) { /* Only if it's the current user's turn */
        hand = document.getElementById('hand');
        
        if (tile.classList.contains('letter')) {
            if (tile.parentNode.id == 'hand') {
                if (picked_up != []) {
                    /* SWAP TILE WITH PICKED UP TILE, tile=tile in hand to swap picked up tile with */
                 
                } else {
                    /* PICK UP TILE FROM HAND, tile=tile in hand to pick up */
                    picked_up = [tile.id, tile.innerHTML];
                    tile.classList.add('recallable');
                    return;
                }
            } else { /* Not in hand */
                if (tile.classList.contains('recallable')) {
                    /* RECALL TILE TO HAND, tile=tile on board to recall */
                    letter = tile.innerHTML;
                    removeLetterFromTile(tile);
                    hand_slot_index = document.getElementById('hand').children.length; /* Index of the end of the hand */
                    hand_tile = createTile(letter, hand_slot_index + 225); /* Create a tile at the end of the hand with letter */
                    hand.appendChild(hand_tile); /* Add the tile to the hand */
                    saveData({ user: user, tile: [tile.id, ""], hand: [hand_slot_index, letter] }); /* Write the changed data */
                } else {
                    /* NOT A RECALLABLE TILE */
                    return;
                }
            }
        } else {
            if (picked_up != []) {
                /* PUT DOWN PICKED UP TILE, tile=slot to put tile down on */
                hand_tile = document.getElementById(picked_up[0])
                real_old_index = parseInt(hand_tile.id);
                hand_old_index = real_old_index - 225; /* Index of removed tile in hand (starting at 0) */
                hand.removeChild(hand_tile);
                addLetterToTile(tile, picked_up[1]);
                tile.classList.add('recallable');
                picked_up = [];
            
                tile_index = parseInt(tile.id);
                saveData({ user: user, tile: [tile_index, tile.innerHTML], hand: [parseInt(hand_old_index), ""] })
            
                return;
            } else {
                /* TILE IS EMPTY SLOT AND NOTHING PICKED UP TO PLACE */
                return;
            }
        }
    }
}

function createTile(letter, id) {
    tile = document.createElement("div"); /* Create a tile div */
    tile.classList.add("tile"); /* Sizing etc */
    tile.id = id.toString();
    if (letter != "") {
        tile.innerHTML = letter; /* Set the letter */
        addLetterToTile(tile, letter);
    }
    tile.addEventListener("click", function(){ clickedTile(this) });
    return tile;
}

function initialiseHand(hand) {
	user_hand_letters.forEach(function (letter, index) {
        if (letter != "") {
            tile = createTile(letter, index + 225); /* Add to index to not overwrite board tiles */
            hand.appendChild(tile); /* Add the tile to the hand */
        }
	})
}

function renderHand() {
	hand = document.getElementById("hand");
    if (hand.innerHTML == "") {
        initialiseHand(hand);
    } else {
        user_hand_letters.forEach(function (letter, index) {
            if (letter != "") {
                slot = document.getElementById(index + 225);
                slot.innerHTML = letter;
            }
        })
    }
}

function initialiseBoard() {
    board_letters.forEach(function (letter, index) {
        tile = createTile(letter, index);
        multiplier_class = multiplier_classes[multipliers[index]];
        tile.classList.add(multiplier_class); /* Background colour */
        board.appendChild(tile); /* Add the tile to the board */
    })
}
    
function renderBoard() {
    board = document.getElementById("board");
    try {
        board.removeChild(document.getElementById("loading")); /* If loading element is still in board */
        initialiseBoard(); /* Board needs to be initialised (including removing loading element */
    } catch { /* Board already initialised (loading removed and first render done) */
        board_letters.forEach(function (letter, index) {
            tile = document.getElementById(index);
            tile.innerHTML = letter;
        })
    }
}

(function poll(){
	$.ajax({
            url: "retrieve_data.php",
                success: function(data){
                    board_letters = data[0];
                    user_hand_letters = data[user_hand_index + 1]; /* 0 is the board array so add 1 */
                    renderBoard();
                    renderHand();
                },
                dataType: "json",
                complete: poll,
                timeout: 30000 });
})();
    
function pickRandom(array) {
	return array[Math.floor(Math.random() * array.length)];
}
    
  </script>
</html>
