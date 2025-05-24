<html>
    <head>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>
     <div class="main-container">
        <form action="index.php" method="post">
            <h1>Select Player:</h1>
			<select name="current_user">
				<option name="red">red</option>
				<option name="green">green</option>
				<option name="blue">blue</option>
				<option name="yellow">yellow</option>
			</select>
			
			<input type="submit" value="Enter"/>
			<input name="game_id" value=""/>
			<h1>Create New Game?</h1>
			<input type="checkbox" name="create_game"/>
            
        </form>
     </div>
    </body>
</html>
