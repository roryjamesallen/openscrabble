<?php
	$board_array = $_POST['board_array'];
	$tilebag = $_POST['tilebag'];
	$final_hand = $_POST['final_hand'];
	file_put_contents('game.txt', $board_array);
	file_put_contents('tilebag.txt', $tilebag);
	file_put_contents('user_hand_1.txt', $final_hand);
?>