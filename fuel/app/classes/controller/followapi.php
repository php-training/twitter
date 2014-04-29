<?php
use \Model\Followapi;
class Controller_Followapi extends Controller_Rest
{
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
	
	//function convert array to xml
	function write_xml( XMLWriter $xml, $data ) {
		foreach( $data as $key => $value ) {
			if( is_array( $value )) {
				$xml->startElement( $key );
				$this->write_xml( $xml, $value );
				$xml->endElement( );
				continue;
			}
			$xml->writeElement( $key, $value );
		}
	}
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
						$info['error'] = array( 'status' => $status , 'message' => $message );				
						foreach($arr1 as $key => $value ){
							$info['users']['user' . $key] = $value;
							if($value['status'] == 1){
								$info['users']['user' . $key]['status'] = 'follow';
							}else{
								$info['users']['user' . $key]['status'] = 'unfollow';
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
						$info['error'] = array( 'status' => $status , 'message' => $message );			
						foreach($arr1 as $key => $value ){
							$info['users']['user' . $key] = $value;
							if($value['status'] == 1){
								$info['users']['user'. $key]['status'] = 'follow';
							}else{
								$info['users']['user'. $key]['status'] = 'unfollow';
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
}