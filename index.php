<?php
function saveBoard($board) {
    file_put_contents('game.txt', json_encode($board));
}

function readBoard() {
    return json_decode(file_get_contents('game.txt'), true);
}

$tile_types = [
    "",
    "double-letter",
    "triple-letter",
    "double-word",
    "triple-word"
];

$initial_board = readBoard();

?>

<html>
    <head>
    <link rel="stylesheet" href="styles.css">
    </head>

    <body>
     <div class="scrabble-board">
     <?php
     foreach ($initial_board as $tile_index => $tile) {
         $tile_letter = $tile[1];
         if($tile_letter != ""){
             $tile_class = "letter-tile";
         } else {
             $tile_class = $tile_types[$tile[0]];
         }
         echo "<div id='tile-".$tile_index."' class='scrabble-tile ".$tile_class."'>".$tile_letter."</div>";
     }
     ?>
     </div>
    </body>
</html>
