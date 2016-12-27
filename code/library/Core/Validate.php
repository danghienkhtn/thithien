<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Core_Validate
{
	
	/**
	 * This function can be used to check the sanity of variables
	 *
	 * @access private
	 *
	 * @param string $type  The type of variable can be bool, float, numeric, string, array, or object
	 * @param string $string The variable name you would like to check
	 * @param string $length The maximum length of the variable
	 *
	 * return bool
	 */
	
	public static function sanityCheck($string, $type, $length){

		// assign the type
		$type = 'is_'.$type;
	
		if(!$type($string))
		{
			return FALSE;
		}
		// now we see if there is anything in the string
		elseif(empty($string))
		{
			return FALSE;
		}
		// then we check how long the string is
		elseif(strlen($string) > $length)
		{
			return FALSE;
		}
		else
		{
			// if all is well, we return TRUE
			return TRUE;
		}
	}
	
	/**
	 * This function checks a number is greater than zero
	 * and exactly $length digits. returns TRUE on success.
	 *
	 * @access private
	 *
	 * @param int $num The number to check
	 * @param int $length The number of digits in the number
	 *
	 * return bool
	 */
	public static function checkNumber($num, $length=0){
		if($num >= 0){
			if($length != 0)
				return (strlen($num) == $length)?TRUE:FALSE;
			return TRUE;
		} 
		return FALSE;
	}
	
	/**
	 * This function checks if an email address in a valid format
	 *
	 * @access private
	 *
	 * @param string $email The email address to check
	 *
	 * return bool
	 */
	
	public static function checkEmail($email){
		error_log($email."___");
		return preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $email) ? TRUE : FALSE;
	}
	
	/**
	 * With DateTime you can make the shortest date&time validator for all formats.
	 * @param unknown $date
	 * @param string $format
	 * @return boolean
	 */
	public static function validateDate($date, $format = 'd-m-Y')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

	/**
	 * This function checks if a username in a valid format
	 *
	 * @access private
	 *
	 * @param string $username The username to check
	 *
	 * return bool
	 */
	
	public static function checkUsername($username){
		return preg_match('/^[a-zA-Z0-9_\.\-]+$/', $username) ? TRUE : FALSE;
	}

	/**
	 * This function checks if the password in a valid format
	 *
	 * @access private
	 *
	 * @param string $password The password to check
	 *
	 * return bool
	 */
	
	public static function checkPassword($password){
		return preg_match('/^.{5,30}$/', $password) ? TRUE : FALSE;
	}

	/**
	 * This function checks if a username in a valid format
	 *
	 * @access private
	 *
	 * @param string $string The string to check
	 *
	 * return bool
	 */
	
	public static function checkNormalText($strText){
		return preg_match('/^[a-zA-Z0-9_ \.\-]+$/', $strText) ? TRUE : FALSE;
	}

}
