<?php
use \Model\Followapi;
class Controller_Followapi extends Controller_Rest
{
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
			if($token == "" || $user_id_followed == "" || !is_numeric($user_id_followed)){
				$status = 401;
				$message = "Invalid Argument";
			}else{
				//search token
				$arr = Followapi::searchToken($token);
				if($arr == 500){
					$status = 500;
					$message = "Internal Server Error";
				}else if($arr == 403){
					$status = 403;
					$message = "Not existed Token";
				}else{
					$user_id_follow = $arr[0]['id'];
					
					//search user id followed in table users
					$arr1 = Followapi::searchUser($user_id_followed);
					
					if($user_id_follow == $user_id_followed){//if user is follow and user is followed is one
						$arr1 = 401;
					}
					if($arr1 == 401){
						$status = 401;
						$message = "Invalid Argument";
					}else if($arr1 == 500){
						$status = 500;
						$message = "Internal Server Error";
					}else if($arr1 == 402){
						$status = 402;
						$message = "Not Existed User";
					}else{
						//check exist follow in table follow
						$arr2 = Followapi::isExistFollow($user_id_follow, $user_id_followed);
						
						$current_status = $arr2[0]['status'];
						if($arr2 == 500){
							$status = 500;
							$message = "Internal Server Error";
						}else if($arr2 == 402){//if not exist follow
							//insert into table follow						
							$follow_status = '1';
							$arr4 = Followapi::insertFollow($user_id_follow, $user_id_followed, $follow_status);
							if($arr4 == 500){
								$status = 500;
								$message = "Internal Server Error";
							}else{
								$status = 200;
								$message = "Normal";
							}
						}else{//if exist follow
							//update follow with other status						
							$arr3 = Followapi::updateFollow($user_id_follow, $user_id_followed, $current_status);
							if($arr3 == 500){
								$status = 500;
								$message = "Internal Server Error";
							}else{
								$status = 200;
								$message = "Normal";
							}
						}
					}
				}
			}
			$data = array(
						'status'=> $status, 
						'message' => $message);
			//$info['status'] = $status;
			//$info['message'] = $message;
			$info['error'] = array( 'status' => $status , 'message' => $message );
			$xml = new XmlWriter();
			$xml->openMemory();
			$xml->startDocument( '1.0', 'utf-8' );
			$xml->startElement( 'response') ;
			 
			$this->write_xml( $xml, $info );
			 
			$xml->endElement();
			echo $xml->outputMemory( true );		
		}catch (\Exception $e) {
		  echo $e->getMessage();
		}
		
	}
	public function action_viewfollowing(){
		try{
			$token = Input::post('token');
				
			//delete white space
			$token = trim($token) ;
			
			//check invalid value
			if($token == ""){
				$status = 401;
				$message = "Invalid Argument";
				$info['error'] = array( 'status' => $status , 'message' => $message );
			}else{
				//search token
				$arr = Followapi::searchToken($token);
				if($arr == 500){
					$status = 500;
					$message = "Internal Server Error";
					$info['error'] = array( 'status' => $status , 'message' => $message );
				}else if($arr == 403){
					$status = 403;
					$message = "Not Existed Token";
					$info['error'] = array( 'status' => $status , 'message' => $message );
				}else{
					$user_id = $arr[0]['id'];
										
					$arr1 = Followapi::viewFollowing($user_id);
					if($arr1 == 500){
						$status = 500;
						$message = "Internal Server Error";
						$info['error'] = array( 'status' => $status , 'message' => $message );
					}else if($arr1 == 402){
						$status = 402;
						$message = "Not Existed User";
						$info['error'] = array( 'status' => $status , 'message' => $message );
					}else{
						$status = 200;
						$message = "Normal";
						$info['error'] = array( 'status' 	=> $status,
										'message' 	=> $message
										);				
						foreach($arr1 as $key => $value ){
							$info['users']['user' . $key] = $value;
						}
					}
				}
			}		
			
			$xml = new XmlWriter();
			$xml->openMemory();
			$xml->startDocument( '1.0', 'utf-8' );
			$xml->startElement('response') ;
			$this->write_xml( $xml, $info );
			$xml->endElement();
			echo $xml->outputMemory( true );		
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
			if($token == ""){
				$status = 401;
				$message = "Invalid Argument";
				$info['error'] = array( 'status' => $status , 'message' => $message );
			}else{
				//search token
				$arr = Followapi::searchToken($token);
				
				if($arr == 500){
					$status = 500;
					$message = "Internal Server Error";
					$info['error'] = array( 'status' => $status , 'message' => $message );
				}else if($arr == 403){					
					$status = 403;
					$message = "Not Existed Token";
					$info['error'] = array( 'status' => $status , 'message' => $message );
				}else{
					
					$user_id = $arr[0]['id'];					
					$arr1 = Followapi::viewFollowed($user_id);
					if($arr1 == 500){
						$status = 500;
						$message = "Internal Server Error";
						$info['error'] = array( 'status' => $status , 'message' => $message );
					}else if($arr1 == 402){
						$status = 402;
						$message = "Not Existed User";
						$info['error'] = array( 'status' => $status , 'message' => $message );
					}else{
						$status = 200;
						$message = "Normal";
						$info['error'] = array( 'status' 	=> $status,
										'message' 	=> $message
										);				
						foreach($arr1 as $key => $value ){
							$info['users']['user' . $key] = $value;
						}
					}
					
				}
			}		
			
			$xml = new XmlWriter();
			$xml->openMemory();
			$xml->startDocument( '1.0', 'utf-8' );
			$xml->startElement('response') ;
			$this->write_xml( $xml, $info );
			$xml->endElement();
			echo $xml->outputMemory( true );		
		}catch (\Exception $e) {
		  echo $e->getMessage();
		}
	}
}