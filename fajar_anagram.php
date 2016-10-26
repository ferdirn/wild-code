<?php

function is_anagram($a, $b) {
	$check_string = count_chars($a, 1) == count_chars($b, 1) ? "TRUE" : "FALSE"; // compare and count character 
	return $check_string;
}

// Call the function 
echo is_anagram("bali", "bila")."<br><br>"; // is_anagram
echo is_anagram("bali", "balia"); // not anagram

?>

