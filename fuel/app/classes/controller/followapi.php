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
	const _STATUS_OK = 200;
	const _STATUS_DB_ERROR = 500;
	const _STATUS_INVALID = 401;
	const _STATUS_NOT_EXIST_USER = 402;
	const _STATUS_NOT_EXIST_TOKEN = 403;	
	
	const _MESSAGE_OK = "";
	const _MESSAGE_DB_ERROR = "Internal Server Error";
	const _MESSAGE_INVALID = "Invalid Argument";
	const _MESSAGE_NOT_EXIST_USER = "Not Existed User";
	const _MESSAGE_NOT_EXIST_TOKEN = "Not Existed Token";
	
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
				$status = self :: _STATUS_INVALID;
				$message = self :: _MESSAGE_INVALID;
			}else{
				//search token
				$arr = Followapi::searchToken($token);
				if($arr == self :: _STATUS_DB_ERROR){
					$status = self :: _STATUS_DB_ERROR;
					$message = self :: _MESSAGE_DB_ERROR;
				}else if($arr == self :: _STATUS_NOT_EXIST_TOKEN){
					$status = self :: _STATUS_NOT_EXIST_TOKEN;
					$message = self :: _MESSAGE_NOT_EXIST_TOKEN;
				}else{
					$user_id_follow = $arr[0]['iduser'];
					
					//search user id followed in table users
					$arr1 = Followapi::searchUser($user_id_followed);
					
					if($user_id_follow == $user_id_followed){//if user is follow and user is followed is one
						$arr1 = self :: _STATUS_INVALID;
					}
					if($arr1 == self :: _STATUS_INVALID){
						$status = self :: _STATUS_INVALID;
						$message = self :: _MESSAGE_INVALID;
					}else if($arr1 == self :: _STATUS_DB_ERROR){
						$status = self :: _STATUS_DB_ERROR;
						$message = self :: _MESSAGE_DB_ERROR;
					}else if($arr1 == self :: _STATUS_NOT_EXIST_USER){
						$status = self :: _STATUS_NOT_EXIST_USER;
						$message = self :: _MESSAGE_NOT_EXIST_USER;
					}else{
						//check exist follow in table follow
						$arr2 = Followapi::isExistFollow($user_id_follow, $user_id_followed);
						
						$current_status = $arr2[0]['status'];
						if($arr2 == self :: _STATUS_DB_ERROR){
							$status = self :: _STATUS_DB_ERROR;
							$message = self :: _MESSAGE_DB_ERROR;
						}else if($arr2 == self :: _STATUS_NOT_EXIST_USER){//if not exist follow
							//insert into table follow						
							$follow_status = '1';
							$arr4 = Followapi::insertFollow($user_id_follow, $user_id_followed, $follow_status);
							if($arr4 == self :: _STATUS_DB_ERROR){
								$status = self :: _STATUS_DB_ERROR;
								$message = self :: _MESSAGE_DB_ERROR;
							}else{
								$status = self :: _STATUS_OK;
								$message = self :: _MESSAGE_OK;
							}
						}else{//if exist follow
							//update follow with other status						
							$arr3 = Followapi::updateFollow($user_id_follow, $user_id_followed, $current_status);
							if($arr3 == self :: _STATUS_DB_ERROR){
								$status = self :: _STATUS_DB_ERROR;
								$message = self :: _MESSAGE_DB_ERROR;
							}else{
								$status = self :: _STATUS_OK;
								$message = self :: _MESSAGE_OK;
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
				$status = self :: _STATUS_INVALID;
				$message = self :: _MESSAGE_INVALID;
			}else{
				//search token
				$arr = Followapi::searchToken($token);
				if($arr == self :: _STATUS_DB_ERROR){
					$status = self :: _STATUS_DB_ERROR;
					$message = self :: _MESSAGE_DB_ERROR;
				}else if($arr == self :: _STATUS_NOT_EXIST_TOKEN){
					$status = self :: _STATUS_NOT_EXIST_TOKEN;
					$message = self :: _MESSAGE_NOT_EXIST_TOKEN;
				}else{
					$user_id = $arr[0]['iduser'];
										
					$arr1 = Followapi::viewFollowing($user_id);
					if($arr1 == self :: _STATUS_DB_ERROR){
						$status = self :: _STATUS_DB_ERROR;
						$message = self :: _MESSAGE_DB_ERROR;
					}else if($arr1 == self :: _STATUS_NOT_EXIST_USER){
						$status = self :: _STATUS_NOT_EXIST_USER;
						$message = self :: _MESSAGE_NOT_EXIST_USER;						
					}else{
						$status = self :: _STATUS_OK;
						$message = self :: _MESSAGE_OK;
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
					
			
			if($status == self :: _STATUS_OK){
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
				$status = self :: _STATUS_INVALID;
				$message = self :: _MESSAGE_INVALID;
			}else{
				//search token
				$arr = Followapi::searchToken($token);
				
				if($arr == self :: _STATUS_DB_ERROR){
					$status = self :: _STATUS_DB_ERROR;
					$message = self :: _MESSAGE_DB_ERROR;
				}else if($arr == self :: _STATUS_NOT_EXIST_TOKEN){					
					$status = self :: _STATUS_NOT_EXIST_TOKEN;
					$message = self :: _MESSAGE_NOT_EXIST_TOKEN;
				}else{
					
					$user_id = $arr[0]['iduser'];					
					$arr1 = Followapi::viewFollowed($user_id);
					if($arr1 == self :: _STATUS_DB_ERROR){
						$status = self :: _STATUS_DB_ERROR;
						$message = self :: _MESSAGE_DB_ERROR;
					}else if($arr1 == self :: _STATUS_NOT_EXIST_USER){
						$status = self :: _STATUS_NOT_EXIST_USER;
						$message = self :: _MESSAGE_NOT_EXIST_USER;						
					}else{
						$status = self :: _STATUS_OK;
						$message = self :: _MESSAGE_OK;
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
					
			
			if($status == self :: _STATUS_OK){
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