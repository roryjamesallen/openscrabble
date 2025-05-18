<?php

?>

<html>
    <head>
    <link rel="stylesheet" href="styles.css">
    </head>

	<body>
		<div class="main-container">
			<div class="hand">
			<?php
				foreach (['O','P','E','N','S','C','R','A','B','B','L','E'] as $letter) {
					echo "<div class='scrabble-tile letter-tile'>".$letter."</div>";
				}
			?>
			</div>
			
			<form action="join_game.php" method="post">
				Game ID:<br>
				<input name="game-id" value=""/> (Leave blank to create a new game)<br>
				<input type="submit" name="join-game" value="Join Game"/><br>
			</form>
		</div>
	</body>
</html>