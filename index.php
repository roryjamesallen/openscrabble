<?php
include 'php_library.php';

$board_letters = readArrayFile('board.txt');
$tilebag = readArrayFile('tilebag.txt');

$current_user = $_POST['current_user'];
$users_turn = readArrayFile('users_turn.txt')[0];

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
.board {
    display: flex;
    flex-wrap: wrap;
    width: 750px;
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
        <div id="board" class="board"></div>
          
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
    /*tile.addEventListener("click", function(){ clickedTile(this) });*/
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
        board.appendChild(tile);
    })
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
    
function poll() {
    $.ajax({
            url: "retrieve_data.php",
            success: function(data) {
                board_letters = data[0]; /* Array of letters on the board */
                renderBoard(board_letters);
                new_users_turn = data[1];
                if (new_users_turn != users_turn) { /* If the user whose turn it is has changed */
                    reloadPage(current_user);
                }
            },
            dataType: "json",
            complete: poll,
            timeout: 30000 });
}

tilebag = "<?php echo $tilebag ?>";
current_user = "<?php echo $current_user ?>"; /* String of client's colour */
users_turn = "<?php echo $users_turn ?>"; /* String of current player's colour */

if (current_user != users_turn) { /* If it's not the current user's turn */
    allow_moves = false;
    poll(); /* Start long polling to show the current player's live tile moves */
} else {
    allow_moves = true;
}

</script>
    </body>
</html>
