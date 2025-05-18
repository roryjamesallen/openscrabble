<html>
	<head>
    <link rel="stylesheet" href="styles.css">
    </head>
<body>
<?php
	function randomName($length) {
		$random_name = '';
		$letters = 'abcdefghijklmnopqrstuvwxyz';
		for ($i = 0; $i < $length; $i++) {
			$index = random_int(0,strlen($letters)-1);
			$random_name = $random_name.$letters[$index];
		}
		return $random_name;
	}

	if ($_POST['game-id'] == "") { 			/* If setting up a new name */
		$game_id = randomName(6);			/* Generate a random name */
		$game_folder = 'games/'.$game_id;	/* Work out the game's folder name */
		mkdir($game_folder);				/* Create the game's folder */
		foreach (['game', 'tilebag', 'red', 'green', 'blue', 'yellow'] as $file) {
			copy('defaults/'.$file.'.txt',  $game_folder.'/'.$file.'.txt');	/* Create all default files */
		}
		
		$tilebag_path = $game_folder.'/tilebag.txt';
		$tilebag = json_decode(file_get_contents($tilebag_path), true);	/* Open the tilebag */
		foreach (['red', 'green', 'blue', 'yellow'] as $user) {							/* For each user */
			$user_tiles = [];
			for ($i = 0; $i < 7; $i++) {												/* For 7 tiles */
				$new_tile = $tilebag[array_rand($tilebag)]; 	/* Pick a random new letter from the tilebag */
				$user_tiles[] = $new_tile;						/* Add the tile to the user's hand */
				unset($tilebag[array_search($new_tile, $tilebag)]);		/* Remove the tile from the tilebag */
				$tilebag = array_values($tilebag);
			}
			file_put_contents($game_folder.'/'.$user.'.txt', json_encode($user_tiles));	/* Update the user's hand file */
		}
		file_put_contents($tilebag_path, json_encode($tilebag));	/* Update the tilebag file */
		
	} else {
		$game_id = $_POST['game-id'];		/* If game should already exist */
		if (!is_dir('games/'.$game_id)) {	/* Make sure it does exist */
			echo "<h1>Game does not exist IDIOT</h1>";
			exit;							/* Failarmy */
		}
	}
?>
	<div class="main-container">
		<div class="hand">
		<?php
			foreach (['O','P','E','N','S','C','R','A','B','B','L','E'] as $letter) {
				echo "<div class='scrabble-tile letter-tile'>".$letter."</div>";
			}
		?>
		</div>
		<form action="index.php" method="post">
		Select Player:<br>
			<input type="hidden" name="game_id" value="<?php echo $game_id ?>"/>
			<input type="submit" name="red" value="red"/>
			<input type="submit" name="blue" value="blue"/>
			<input type="submit" name="green" value="green"/>
			<input type="submit" name="yellow" value="yellow"/>
		</form>
	</div>
</body>