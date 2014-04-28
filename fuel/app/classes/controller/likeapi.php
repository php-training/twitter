<?php 
use \Model\Likeapi;

class Controller_Likeapi extends Controller_Rest
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     PHP-Training Team
 * @license    MIT License
 * @copyright  do.hoc@mulodo.com
 * @link       http://fuelphp.com
 */
 
/**
 * The LikeAPI Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
{

	private $idmsg = '';
	private $token = '';
	
	public function action_index()
	{
		if(Input::method() == 'POST')
		{
			//format
			$this->format = 'xml';
			$this->idmsg = Input::post('idmsg');
			$this->token = Input::post('token');

			//validation
			if(!empty($this->token))
			{

				$view = Likeapi::checkToken($this->token);
				//check token
				if(count($view->current()) > 0)
				{
					$user = $view->current();
					//check post
					$post = Likeapi::checkPost($this->idmsg);
					if(count($post->current()) > 0)
					{
						$like = Likeapi::checkLike($this->idmsg, $user['id']);
						if(count($like->current()) > 0)
						{
							if(Likeapi::deleteLike($this->idmsg, $user['id']))
							{
								return $this->response(
									array(
										'error' => array(
											'status' 	=> '200',
											'message' 	=> 'Delete Successfully',
										)
									)
								);
							} else {
								return $this->response(
									array(
										'error' => array(
											'status' 	=> '500',
											'message' 	=> 'Database Error',
										)
									)
								);
							}
						} else {
							if(Likeapi::insertLike($this->idmsg, $user['id']))
							{
								return $this->response(
									array(
										'error' => array(
											'status' 	=> '200',
											'message' 	=> 'Insert Successfully',
										)
									)
								);
							} else {
								return $this->response(
									array(
										'error' => array(
											'status' 	=> '500',
											'message' 	=> 'Database Error',
										)
									)
								);
							}
						}
					} else {
						return $this->response(
							array(
								'error' => array(
									'status' 	=> '401',
									'message' 	=> 'This post does not exist',
								)
							)
						);
					}
				} else {
					return $this->response(
						array(
							'error' => array(
								'status' 	=> '401',
								'message' 	=> 'Please login before like this post',
							)
						)
					);
				}
			} else {
				return $this->response(
					array(
						'error' => array(
							'status' 	=> '401',
							'message' 	=> 'Please login before like this post',
						)
					)
				);
			}
			
			
		}
	}
}
?>