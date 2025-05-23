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

$users_turn_text = "You are <span style='color:".$current_user."'>".$current_user."</span>. It is ";
if ($users_turn == $current_user) {
    $users_turn_text = $users_turn_text."your";
} else {
    $users_turn_text = $users_turn_text.$users_turn."'s";
}
$users_turn_text = $users_turn_text." turn.";
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="styles.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    </head>
    <body>
         <div class="main-container">
             <h1 id="heading" style="margin: 0; margin-top: 25px;"><?php echo $users_turn_text ?></h1>
             <div id="board" class="board"></div>
             <div id="hand" class="hand"></div>
             <div id="make-turn-button" class="make-turn-button tile letter" onclick="makeTurn()">Go</div>
             <h1 id="heading" style="margin: 0; margin-bottom: 25px;"><?php echo count($tilebag)." tiles left"?></h1>
         </div>      
<script>
function addLetterToTile(tile, letter) {
    tile.innerHTML = letter;
    tile.classList.add('letter');
    if (letter != "") {
        tile.classList.add(letter.toLowerCase());
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
            tile.classList.add(users_turn); /* Outline placed but not confirmed letters in the player's colour for all users to see */
        }
        board.appendChild(tile);
    })
}

function renderHand(letters) {
    hand = document.getElementById('hand');
    hand.innerHTML = "";
    letters.forEach(function (letter, index) {
        if (letter != "") {
            letter_id = index + 225;
            tile = createTile(letter, letter_id);
            if (picked_up == letter_id && allow_moves == true) { /* Only show to the current player and only if it's their turn */
                tile.classList.add(current_user); /* Outline in the user's colour */
            }
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
    new_array = [];
    found = false;
    original_array.forEach(function (potential_item, index) {
        if (potential_item == item && found == false) {
            found = true;
        } else {
            new_array.push(potential_item);
        }
    })
    return new_array;
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
                    picked_up_letter = hand_letters[picked_up - 225];
                    hand_letters[picked_up - 225] = tile_letter;
                    hand_letters[tile_id - 225] = picked_up_letter;
                    picked_up = ""
                } else {
                    /* PICK UP TILE FROM HAND, tile=tile in hand to pick up */
                    picked_up = tile_id;
                }
            } else { /* Not in hand */
                if (recallable.includes(tile_id)) {
                    /* RECALL TILE TO HAND, tile=tile on board to recall */
                    board_letters[tile_id] = ""; /* Remove letter from board */
                    hand_letters.push(tile_letter); /* Add letter to hand */
                    recallable = removeFirstInstance(recallable, tile_id); /* Make the letter non recallable */
                    writeGameData({tile: [tile_id, ""], hand: hand_letters, recallable: recallable, user: current_user});
                } else {
                    /* NOT A RECALLABLE TILE */
                }
            }
        } else {
            if (picked_up != "") {
                /* PUT DOWN PICKED UP TILE, tile=slot to put tile down on */
                hand_tile_letter = document.getElementById(picked_up).innerHTML;
                hand_index = picked_up - 225; /* Work out index of tile within hand based on its id */
                hand_letters.splice(hand_index,1); /* Remove the letter from the user's hand */
                board_letters[tile_id] = hand_tile_letter; /* Add the letter to the board */
                recallable.push(tile_id); /* Mark the letter as recallable */
                picked_up = "";
                writeGameData({tile: [tile_id, hand_tile_letter], hand: hand_letters, recallable: recallable, user: current_user});
            } else {
                /* TILE IS EMPTY SLOT AND NOTHING PICKED UP TO PLACE */
            }
        }
    }
    renderAll();
}

function makeTurn() {
    if (allow_moves == true) {
        all_users = ["red","blue","green","yellow"];
        users_turn_index = all_users.indexOf(current_user);
        new_users_turn_index = users_turn_index + 1;
        if (new_users_turn_index == all_users.length) {
            new_users_turn_index = 0;
        }
        users_turn = all_users[new_users_turn_index];
        while (tilebag.length != 0 && hand_letters.length < 7) { /* As long as there are tiles left in the tilebag and the user needs more tiles replacing */
            replacement_tile = tilebag[Math.floor(Math.random()*tilebag.length)]; /* Pick a random new letter from the tilebag */
            tilebag = removeFirstInstance(tilebag, replacement_tile); /* Remove it from the tilebag */
            hand_letters.push(replacement_tile); /* Add it to the user's hand */
        }
        writeGameData({user: current_user, users_turn: users_turn, hand: hand_letters, tilebag: tilebag});
        reloadPage(current_user);
    }
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
    empty_board = ["","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","L","","O","","A","","D","","I","","N","","G","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","",""];
    renderBoard(empty_board);
    renderHand(["L","O","A","D","I","N","G"]);
    poll(); /* Start long polling to show the current player's live tile moves */
} else {
    allow_moves = true;
    renderBoard(board_letters);
    renderHand(hand_letters);
}

</script>
    </body>
</html>
