<?php
namespace Model;
use \DB;

class Followapi extends \Model
{
	 /** Search token to get iduser.
	 * Input: $token. 
	 * Output: $iduser of token if exist in login_token table. 403: not exist. 500: data error 
	 */ 
	public static function searchToken($token){
		try{
			$query = DB::query("select iduser from login_token where due_date >= now() and token = '" . $token . "'", DB::SELECT);
			$result = $query->execute();
			$arrs = $result->as_array();			
			$rows = count($arrs);
			if($rows == 0){
				return 403;
			}else{
				return $arrs;
			}
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	
	}
	/** Check user exist in users table. 
	* Input: iduser. 
	* Output: iduser if exist in users table. 402: not exist. 500: data error
	*/
	public static function searchUser($iduser){
		try{
			$query = DB::query("select id from users where id =  " . $iduser,DB::SELECT);
			$result = $query->execute();
			$arrs = $result->as_array();
			$rows = count($arrs);
			if($rows == 0){
				return 402;
			}else{
				return $arrs;
			}
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	
	}
	/** Check follow exist in follow table
	* Input: $user_id_follow, $user_id_followed
	* Output: $id, $status of folllow table if exist. 402: not exist. 500: datat error
	*/
	public static function isExistFollow($user_id_follow, $user_id_followed){
		try{
			$query = DB::query("select id, status from follow where user_id_follow =". $user_id_follow . " and user_id_followed = " . $user_id_followed,DB::SELECT);
			$result = $query->execute();
			$arrs = $result->as_array();
			$rows = count($arrs);
			if($rows == 0){
				return 402;
			}else{
				return $arrs;
			}
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	}
	/** Insert follow into follow table
	* Input: $user_id_follow, $user_id_followed, $status
	* Output: 1:success, 500: unsuccess
	*/
	public static function insertFollow($user_id_follow, $user_id_followed, $status){
		try{
			$query = DB::query("insert into follow(user_id_follow, user_id_followed, status) values(" . $user_id_follow . ", " . $user_id_followed . ", " . $status . ")",DB::INSERT);			
			$result = $query->execute();
			return $result;
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	}
	/** Update status follow in follow table
	* Input: $user_id_follow, $user_id_followed, $currentstatus: status is current time
	* Output: 1: success, 500: data error
	*/
	public static function updateFollow($user_id_follow, $user_id_followed, $currentStatus){
		try{
			if($currentStatus == 1){
				$status = 0;
			}else{
				$status = 1;
			}
			$query = DB::query("update follow set status = " . $status . " where user_id_follow = " . $user_id_follow . " and user_id_followed = " . $user_id_followed,DB::UPDATE);			
			$result = $query->execute();
			return $result;
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	}
	/** Get following users list
	* Input: user_id
	* Output: list of users following user_id. 402: not exist. 500 : data error
	*/
	public static function viewFollowing($user_id){
		try{
			$query = DB::query("select u.id, u.email, f.status from users u, follow f where u.id = f.user_id_follow and  f.user_id_followed =  " . $user_id, DB::SELECT);
			$result = $query->execute();
			$arrs = $result->as_array();					
			$rows = count($arrs);
			if($rows == 0){
				return 402;
			}else{
				return $arrs;
			}
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	}
	/** Get followed users list
	* Input: user_id
	* Output: list of users followed by user_id if exist. 402: not exist. 500 : data error
	*/
	public static function viewFollowed($user_id){
		try{
			$query = DB::query("select u.id, u.email, f.status from users u, follow f where u.id = f.user_id_followed and  f.user_id_follow =  " . $user_id, DB::SELECT);
			$result = $query->execute();
			$arrs = $result->as_array();
			
			$rows = count($arrs);			
			if($rows == 0){
				return 402;
			}else{
				return $arrs;
			}
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	}
}