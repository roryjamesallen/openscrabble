<?php
$tile_types = [
    "",
    "double-letter",
    "triple-letter",
    "double-word",
    "triple-word"
];

function saveBoard($board) {
    file_put_contents('game.txt', json_encode($board));
}

function readBoard() {
    return json_decode(file_get_contents('game.txt'), true);
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
        echo "<div id='tile-".$tile_index."' class='scrabble-tile ".$tile_class."'>".$tile_letter."</div>";
    }
}

$initial_board = readBoard();

$initial_hand = ["A", "B", "C", "D", "E", "F", "G"];

?>

<html>
    <head>
    <link rel="stylesheet" href="styles.css">
    </head>

    <body>
     <div class="main-container">
     <div class="scrabble-board">
<?php
     renderBoard($initial_board);
?>
     </div>

     <div class="hand">
<?php
     foreach ($initial_hand as $tile_index => $tile_letter) {
         echo "<div id='hand-tile-".$tile_index."' class='scrabble-tile letter-tile' onclick='setHolding(this)'>".$tile_letter."</div>";
     }
?>
     </div>
     </div>

    <script>
    function setHolding(tile_id) {
        tile_index = tile_id.split('-')
    }
    </script>
    </body>
</html>
