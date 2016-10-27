<?php 
	
function check_palindrome($string){
	echo "String: ". $string . "<br>";
	$reverse = strrev($string); // reverse the string parameter
	if($string == $reverse){ // compare if the original word is same as the reverse of the same word
		echo "Output: This string is a palindrome";
	}else{
		echo "Output: This string is not a palindrome";
	}
}

// Call the function
echo check_palindrome("abu uba")."<br><br>"; // palindrome
echo check_palindrome("aku i uka"); // is not palindrome
?>
