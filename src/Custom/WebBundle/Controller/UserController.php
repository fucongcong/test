<?php
namespace Custom\WebBundle\Controller;

use Topxia\WebBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Topxia\Common\ArrayToolkit;
use Topxia\Common\SimpleValidator;

class UserController extends BaseController
{
	public function registerAction(Request $request)
	{
		$data = $request ->request ->all();

		if(!isset($data['iloginname']) || !isset($data['igender']) || !isset($data['iemail']) || !isset($data['ipassword']) || !isset($data['isalt']) ||
			!isset($data['iregip']) || !isset($data['iregtime']) || !isset($data['iemailstatus']) || !isset($data['irealname'])) {

			return $this->createJsonResponse(['status' => "error", "info" => "缺少必要字段!"]);
		}
		if($data['igender'] == 1) {
			$sex = "male";
		 }else {
			$sex = "female";
		 }
		$registration = array(
			'nickname' => $data['iloginname'],
			'email' => $data['iemail'],
			'password' => $data['ipassword'],
			'salt' => $data['isalt'],
			'createdIp' => $data['iregip'],
			'createdTime' => $data['iregtime'],
			'emailVerified' => $data['iemailstatus'],
			'gender' => $sex,
			'truename' => $data['irealname'],
			);

	              if (!SimpleValidator::nickname($registration['nickname'])) {
	             		return $this->createJsonResponse(['status' => "error", "info" => "nickname error!"]);
		}
		if (!$this -> getUserService()->isNicknameAvaliable($registration['nickname'])) {
			return $this->createJsonResponse(['status' => "error", "info" => "昵称已存在"]);
		}
		if (!SimpleValidator::email($registration['email'])) {
			return $this->createJsonResponse(['status' => "error", "info" => "email error!"]);
		}
		if (!$this -> getUserService()->isEmailAvaliable($registration['email'])) {
			return $this->createJsonResponse(['status' => "error", "info" => "Email已存在"]);
		}
			
		$user = $this -> getUserService() -> register($registration);
		return $this->createJsonResponse(['status' => "success", "info" => ""]);

	}

	public function updateAction(Request $request)
	{
		$data = $request ->request ->all();
		if(!isset($data['iloginname'])) {

			return $this->createJsonResponse(['status' => "error", "info" => "缺少必要字段!"]);
		}
		$user = $this -> getUserService() -> getUserByNickname($data['iloginname']);

		if(!$user) {
			return $this->createJsonResponse(['status' => "error", "info" => "用户不存在!"]);
		}
		$profile = [];
		if (isset($data['igender'])) {

			if($data['igender'] == 1) {
				$profile['gender']  = "male";
			 }else {
				$profile['gender'] = "female";
			 }
		}

		if (isset($data['irealname'])) { 

			$profile['truename'] = $data['irealname'];
		}
		
		$this -> getUserService() -> updateUserProfile($user['id'], $profile);

		if (isset($data['ipassword'])) { 

			$this -> getUserService() -> updateUser($user['id'], array('password' => $data['ipassword']));
		}
	}

	public function loginAction(Request $request)
	{
		$data = $request ->request ->all();
		$user = $this -> getUserService() -> getUserByNickname($data['iloginname']);

		if(!$user) {

			return $this->createJsonResponse(['status' => "error", "info" => "用户不存在!"]);
		}

		$password = $data['ipassword'].$user['salt'];
		$password = md5($password);

		if($password == $user['password']) {

			$this -> authenticateUser($user);
			return $this->createJsonResponse(['status' => "success", "info" => ""]);
		} 

		return $this->createJsonResponse(['status' => "error", "info" => "密码错误!"]);
	}
  
}