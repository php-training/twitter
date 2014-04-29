<?php
use \Model\Followapi;
class Controller_Followapi extends Controller_Rest
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     PHP-Training Team
 * @license    MIT License
 * @copyright  duong.tram@mulodo.com
 * @link       http://fuelphp.com
 */
 
/**
 * The FollowAPI Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
{
	//set const status and message of Error
	const _status_ok = 200;
	const _status_db_error = 500;
	const _status_invalid = 401;
	const _status_not_exist_user = 402;
	const _status_not_exist_token = 403;	
	
	const _message_ok = "";
	const _message_db_error = "Internal Server Error";
	const _message_invalid = "Invalid Argument";
	const _message_not_exist_user = "Not Existed User";
	const _message_not_exist_token = "Not Existed Token";
	
	/** Add follow or unfollow of user. 
	 * Input: $token and $user_id_followed.
	 * Output: Error($status, $message)
	 */ 
	public function action_addfollow()
	{
		try{			
			$token = Input::post('token');
			$user_id_followed = Input::post('user_id_followed');			
			//delete white space
			$token = trim($token) ;
			$user_id_followed = trim($user_id_followed);
			//check invalid value
			$val = Validation::forge();
			//value is requried and $user_id_followed is numeric
			$val->add('token')->add_rule('required');
			$val->add('user_id_followed')->add_rule('required')->add_rule('valid_string', array('numeric'));						
			if(!$val->run(array('token' => $token)) || !$val->run(array('user_id_followed' => $user_id_followed))){				
				$status = self :: _status_invalid;
				$message = self :: _message_invalid;
			}else{
				//search token
				$arr = Followapi::searchToken($token);
				if($arr == self :: _status_db_error){
					$status = self :: _status_db_error;
					$message = self :: _message_db_error;
				}else if($arr == self :: _status_not_exist_token){
					$status = self :: _status_not_exist_token;
					$message = self :: _message_not_exist_token;
				}else{
					$user_id_follow = $arr[0]['iduser'];
					
					//search user id followed in table users
					$arr1 = Followapi::searchUser($user_id_followed);
					
					if($user_id_follow == $user_id_followed){//if user is follow and user is followed is one
						$arr1 = self :: _status_invalid;
					}
					if($arr1 == self :: _status_invalid){
						$status = self :: _status_invalid;
						$message = self :: _message_invalid;
					}else if($arr1 == self :: _status_db_error){
						$status = self :: _status_db_error;
						$message = self :: _message_db_error;
					}else if($arr1 == self :: _status_not_exist_user){
						$status = self :: _status_not_exist_user;
						$message = self :: _message_not_exist_user;
					}else{
						//check exist follow in table follow
						$arr2 = Followapi::isExistFollow($user_id_follow, $user_id_followed);
						
						$current_status = $arr2[0]['status'];
						if($arr2 == self :: _status_db_error){
							$status = self :: _status_db_error;
							$message = self :: _message_db_error;
						}else if($arr2 == self :: _status_not_exist_user){//if not exist follow
							//insert into table follow						
							$follow_status = '1';
							$arr4 = Followapi::insertFollow($user_id_follow, $user_id_followed, $follow_status);
							if($arr4 == self :: _status_db_error){
								$status = self :: _status_db_error;
								$message = self :: _message_db_error;
							}else{
								$status = self :: _status_ok;
								$message = self :: _message_ok;
							}
						}else{//if exist follow
							//update follow with other status						
							$arr3 = Followapi::updateFollow($user_id_follow, $user_id_followed, $current_status);
							if($arr3 == self :: _status_db_error){
								$status = self :: _status_db_error;
								$message = self :: _message_db_error;
							}else{
								$status = self :: _status_ok;
								$message = self :: _message_ok;
							}
						}
					}
				}
			}			
			$info['error'] = array( 'status' => $status , 'message' => $message );
			echo Format :: forge ($info) -> to_xml();	
		}catch (\Exception $e) {
		  echo $e->getMessage();
		}		
	}
	/** View following users list.  
	 * Input: $token.
	 * Output: error($status, $message) and following users list.
	 */
	public function action_viewfollowing(){
		try{
			$token = Input::post('token');				
			//delete white space
			$token = trim($token);						
			//check invalid value
			$val = Validation::forge();
			//value is requried
            $val->add('token')->add_rule('required');
			if(!$val->run(array('token'=>$token))){
				$status = self :: _status_invalid;
				$message = self :: _message_invalid;
			}else{
				//search token
				$arr = Followapi::searchToken($token);
				if($arr == self :: _status_db_error){
					$status = self :: _status_db_error;
					$message = self :: _message_db_error;
				}else if($arr == self :: _status_not_exist_token){
					$status = self :: _status_not_exist_token;
					$message = self :: _message_not_exist_token;
				}else{
					$user_id = $arr[0]['iduser'];
										
					$arr1 = Followapi::viewFollowing($user_id);
					if($arr1 == self :: _status_db_error){
						$status = self :: _status_db_error;
						$message = self :: _message_db_error;
					}else if($arr1 == self :: _status_not_exist_user){
						$status = self :: _status_not_exist_user;
						$message = self :: _message_not_exist_user;						
					}else{
						$status = self :: _status_ok;
						$message = self :: _message_ok;
						foreach($arr1 as $key => $value ){
							if($value['status'] == 1)
								$arr1[$key]['status'] = 'follow';
							else 
								$arr1[$key]['status'] = 'unfollow';
						}
						$result1 = array(
											'error' => array(
													'status' 	=> $status,
													'message' 	=> $message,
											), 'users' => $arr1,
										); 
					}
				}
			}		
					
			
			if($status == self :: _status_ok){
				$result = $result1;
			}else{
				$result = array(
							'error' => array(
										'status' 	=> $status,
										'message' 	=> $message,
										)
							);
			}	
			 
			echo(Format :: forge($result) -> to_xml());
		}catch (\Exception $e) {
		  echo $e->getMessage();
		}
	}
	/** View followed users list. 
	 * Input: $token. 
	 * Output: error($status, $message) and followed users list 
	 */
	public function action_viewfollowed(){
		try{
			$token = Input::post('token');				
			//delete white space
			$token = trim($token) ;						
			//check invalid value
			$val = Validation::forge();
			//value is requried
            $val->add('token')->add_rule('required');
			if(!$val->run(array('token'=>$token))){
				$status = self :: _status_invalid;
				$message = self :: _message_invalid;
			}else{
				//search token
				$arr = Followapi::searchToken($token);
				
				if($arr == self :: _status_db_error){
					$status = self :: _status_db_error;
					$message = self :: _message_db_error;
				}else if($arr == self :: _status_not_exist_token){					
					$status = self :: _status_not_exist_token;
					$message = self :: _message_not_exist_token;
				}else{
					
					$user_id = $arr[0]['iduser'];					
					$arr1 = Followapi::viewFollowed($user_id);
					if($arr1 == self :: _status_db_error){
						$status = self :: _status_db_error;
						$message = self :: _message_db_error;
					}else if($arr1 == self :: _status_not_exist_user){
						$status = self :: _status_not_exist_user;
						$message = self :: _message_not_exist_user;						
					}else{
						$status = self :: _status_ok;
						$message = self :: _message_ok;
						foreach($arr1 as $key => $value ){
							if($value['status'] == 1)
								$arr1[$key]['status'] = 'follow';
							else 
								$arr1[$key]['status'] = 'unfollow';
						}
						$result1 = array(
											'error' => array(
													'status' 	=> $status,
													'message' 	=> $message,
											), 'users' => $arr1,
										); 
					}
				}
			}		
					
			
			if($status == self :: _status_ok){
				$result = $result1;
			}else{
				$result = array(
							'error' => array(
										'status' 	=> $status,
										'message' 	=> $message,
										)
							);
			}	
			 
			echo(Format :: forge($result) -> to_xml());		
		}catch (\Exception $e) {
		  echo $e->getMessage();
		}
	}
}