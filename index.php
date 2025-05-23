<?php
$current_user = $_POST['current_user'];
if ($current_user == "") {
    header('Location: choose_user.php');
}
include 'php_library.php';

$board_letters = readArrayFile('board.txt');
$tilebag = readArrayFile('tilebag.txt');
$users_turn = readArrayFile('users_turn.txt')[0];
$hand_letters = readArrayFile($current_user.'_hand.txt');
$recallable = readArrayFile('recallable.txt');
if ($recallable == "") {
    $recallable = [];
}

echo "<h1>You are ".$current_user.". It is ".$users_turn."'s turn</h1>";
?>

<html>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    </head>
    <body>
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
         <h1 id="heading"></h1>
         <div class="main-container">
             <div id="board" class="board"></div>
             <div id="hand" class="hand"></div>
         </div>
          
<script>
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
     
function renderBoard(letters) {
    board = document.getElementById('board');
    board.innerHTML = "";
    letters.forEach(function (letter, index) {
        tile = createTile(letter, index);
        if ([3,11,36,38,45,52,59,92,96,98,102,108,116,122,126,128,132,165,172,179,186,188,213,221].includes(index)){
            tile.classList.add('double-letter');
        } else if ([20,24,76,80,84,88,136,140,144,148,200,204].includes(index)) {
            tile.classList.add('triple-letter');
        } else if ([16,28,32,42,48,56,64,112,154,160,168,176,182,192,196,208].includes(index)) {
            tile.classList.add('double-word');
        } else if ([0,7,14,105,119,210,217,224].includes(index)) {
            tile.classList.add('triple-word');
        }
        if (recallable.includes(index)) {
            tile.classList.add('recallable');
        }
        board.appendChild(tile);
    })
}

function renderHand(letters) {
    hand = document.getElementById('hand');
    hand.innerHTML = "";
    letters.forEach(function (letter, index) {
        if (letter != "") {
            tile = createTile(letter, index+225);
            hand.appendChild(tile);
        }
    })
}

function renderAll() {
    renderBoard(board_letters);
    renderHand(hand_letters);
}
     
function reloadPage(current_user) {
    $.ajax({
        type: "POST",
        data: { 'current_user': current_user },
                success: function() {
                    location.reload();
                }
    });
}

function writeGameData(data) {
    $.ajax({
        type: "POST",  //type of method
        url: "write_data.php",  //your page
        data: data,// passing the values
        success: function(res){  
                               
        }
    });
}
    
function poll() {
    $.ajax({
            url: "retrieve_data.php",
            success: function(data) {
                board_letters = data[0]; /* Array of letters on the board */
                renderAll();
                new_users_turn = data[1];
                if (new_users_turn != users_turn) { /* If the user whose turn it is has changed */
                    reloadPage(current_user);
                }
            },
            dataType: "json",
            complete: poll,
            timeout: 30000 });
}

function removeFirstInstance(original_array, item) {
    original_array.splice(original_array.indexOf(item),1);
}

function clickedTile(tile) {
    if (allow_moves == true) { /* Only if it's the current user's turn */
        hand = document.getElementById('hand');
        tile_id = tile.id;
        
        if (tile.classList.contains('letter')) {
            tile_letter = tile.innerHTML;
            if (tile.parentNode.id == 'hand') {
                if (picked_up != "") {
                    /* SWAP TILE WITH PICKED UP TILE, tile=tile in hand to swap picked up tile with */
                 
                } else {
                    /* PICK UP TILE FROM HAND, tile=tile in hand to pick up */
                    picked_up = tile_id;
                }
            } else { /* Not in hand */
                if (recallable.includes(tile_id)) {
                    /* RECALL TILE TO HAND, tile=tile on board to recall */
                    board_letters[tile_id] = ""; /* Remove letter from board */
                    hand_letters.push(tile_letter); /* Add letter to hand */
                    removeFirstInstance(recallable, tile_id); /* Make the letter non recallable */
                    writeGameData({tile: [tile_id, ""], hand: hand_letters, recallable: recallable, user: current_user});
                } else {
                    /* NOT A RECALLABLE TILE */
                }
            }
        } else {
            if (picked_up != "") {
                /* PUT DOWN PICKED UP TILE, tile=slot to put tile down on */
                hand_tile_letter = document.getElementById(picked_up).innerHTML;
                removeFirstInstance(hand_letters, hand_tile_letter);
                board_letters[tile_id] = hand_tile_letter;
                recallable.push(tile_id);
                picked_up = "";
                writeGameData({tile: [tile_id, hand_tile_letter], hand: hand_letters, recallable: recallable, user: current_user})
            } else {
                /* TILE IS EMPTY SLOT AND NOTHING PICKED UP TO PLACE */
            }
        }
    }
    renderAll();
}

/* ---- MAIN CODE ---- */

tilebag = <?php echo json_encode($tilebag) ?>;
current_user = "<?php echo $current_user ?>"; /* String of client's colour */
users_turn = "<?php echo $users_turn ?>"; /* String of current player's colour */
board_letters = <?php echo json_encode($board_letters); ?> /* Initial board state */
hand_letters = <?php echo json_encode($hand_letters); ?> /* Initial user's hand */
recallable = <?php echo json_encode($recallable); ?> /* Tile placed but not confirmed */
picked_up = "";

if (current_user != users_turn) { /* If it's not the current user's turn */
    allow_moves = false;
    poll(); /* Start long polling to show the current player's live tile moves */
} else {
    allow_moves = true;
    renderBoard(board_letters);
    renderHand(hand_letters);
}

</script>
    </body>
</html>
