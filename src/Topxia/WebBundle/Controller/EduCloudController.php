<?php
namespace Topxia\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Respose;

class EduCloudController extends BaseController
{
    public function smsSendAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
/*            if ($this->getCloudSmsKey('sms_enabled') != '1') {
                return $this->createJsonResponse(array('error' => '短信服务被管理员关闭了'));
            }*/

            $currentUser = $this->getCurrentUser();
            $currentTime = time();


            $smsType = $request->request->get('sms_type');
            //$this->checkSmsType($smsType, $currentUser);

            $targetSession = $request->getSession()->get($smsType);
            $smsLastTime = $targetSession['sms_last_time'];
            $allowedTime = 120;
            
            if (!$this->checkLastTime($smsLastTime, $currentTime, $allowedTime)) {
                return $this->createJsonResponse(array('error' => '请等待120秒再申请', 'message' => "{$smsLastTime}|{$currentTime}"));
            }

            if (in_array($smsType, array('sms_bind','sms_registration'))) {
                $to = $request->request->get('to');

                $hasVerifiedMobile = (isset($currentUser['verifiedMobile'])&&(strlen($currentUser['verifiedMobile'])>0));
                if ($hasVerifiedMobile && ($to == $currentUser['verifiedMobile'])){
                    return $this->createJsonResponse(array('error' => "您已经绑定了该手机号码"));
                }

                if (!$this->getUserService()->isMobileUnique($to)) {
                    return $this->createJsonResponse(array('error' => "该手机号码已被其他用户绑定"));
                }
            }

            if ($smsType == 'sms_forget_password') {
                $targetUser = $this->getUserService()->getUserByVerifiedMobile($request->request->get('to'));
                if (empty($targetUser)){
                    return $this->createJsonResponse(array('error' => '用户不存在'));    
                }
                if ((!isset($targetUser['verifiedMobile']) || (strlen($targetUser['verifiedMobile']) == 0))) {
                    return $this->createJsonResponse(array('error' => '用户没有被绑定的手机号'));
                }
                if ($targetUser['verifiedMobile'] != $request->request->get('to')) {
                    return $this->createJsonResponse(array('error' => '手机与用户名不匹配'));
                }
                $to = $targetUser['verifiedMobile'];
            }

            if (in_array($smsType, array('sms_user_pay', 'sms_forget_pay_password'))) {
                $user = $currentUser->toArray();
                if ((!isset($user['verifiedMobile']) || (strlen($user['verifiedMobile']) == 0))) {
                    return $this->createJsonResponse(array('error' => '用户没有被绑定的手机号'));
                }
                if ($user['verifiedMobile'] != $request->request->get('to')) {
                    return $this->createJsonResponse(array('error' => '您输入的手机号，不是已绑定的手机'));
                }
                $to = $user['verifiedMobile'];
            }

            if (!$this->checkPhoneNum($to)){
                return $this->createJsonResponse(array('error' => "手机号错误:{$to}"));
            }

            $smsCode = $this->generateSmsCode();
            try {
                //$result = $this->getEduCloudService()->sendSms($to, $smsCode, $smsType);
                
                $result = $this->sendSms($to, $smsCode);

                if (!$result) {
                    return $this->createJsonResponse(array('error' => "发送失败, {$result}"));
                }
            } catch (\RuntimeException $e) {
                $message = $e->getMessage();
                return $this->createJsonResponse(array('error' => "发送失败, {$message}"));
            }

            $result['to'] = $to;
            $result['smsCode'] = $smsCode;
            $result['userId'] = $currentUser['id'];
            if ($currentUser['id'] != 0) {
                $result['nickname'] = $currentUser['nickname'];
            }
            $this->getLogService()->info('sms', $smsType, "userId:{$currentUser['id']},对{$to}发送用于{$smsType}的验证短信{$smsCode}", $result);

            $request->getSession()->set($smsType, array(
                'to' => $to,
                'sms_code' => $smsCode,
                'sms_last_time' => $currentTime
            ));            

            return $this->createJsonResponse(array('ACK' => 'ok'));
        }

        return $this->createJsonResponse(array('error' => 'GET method'));
    }

    private function sendSms($mobile, $code)
    {
        $app_id = "300129840000034964";
        $app_secret = "5084fe200a4e6f1ab83f8460e3340370";
        $url = "https://oauth.api.189.cn/emp/oauth2/v3/access_token";

        $param['grant_type'] ="grant_type=client_credentials";
        $param['app_id']= "app_id=".$app_id;
        $param['app_secret'] = "app_secret=".$app_secret;
        ksort($param);
        $plaintext = implode("&",$param);

        $result = $this->curl_post($url, $plaintext);
        $resultArray = json_decode($result,true);
        $access_token = $resultArray['access_token'];
        $timestamp = date('Y-m-d H:i:s');
        unset($param);
        $url = "http://api.189.cn/v2/dm/randcode/token?";

        $param['app_id']= "app_id=".$app_id;
        $param['access_token'] = "access_token=".$access_token;
        $param['timestamp'] = "timestamp=".$timestamp;
        ksort($param);
        $plaintext = implode("&",$param);
        $param['sign'] = "sign=".rawurlencode(base64_encode(hash_hmac("sha1", $plaintext, $app_secret, $raw_output=True)));
        ksort($param);
        $url .= implode("&",$param);
        $result = $this->curl_get($url);
        $resultArray = json_decode($result,true);
        $token = $resultArray['token'];
        unset($param);
        $url = "http://api.189.cn/v2/dm/randcode/sendSms";
        
        $param['app_id']= "app_id=".$app_id;
        $param['randcode'] = "randcode=".$code;
        $param['access_token'] = "access_token=".$access_token;
        $param['timestamp'] = "timestamp=".$timestamp;
        $param['token'] = "token=".$token;
        $param['phone'] = "phone=".$mobile;
        if(isset($exp_time))
            $param['exp_time'] = "exp_time=".$exp_time;
        ksort($param);
        $plaintext = implode("&",$param);
        $param['sign'] = "sign=".rawurlencode(base64_encode(hash_hmac("sha1", $plaintext, $app_secret, $raw_output=True)));
        ksort($param);
        $str = implode("&",$param);
        $result = $this->curl_post($url,$str);
        $resultArray = json_decode($result,true);

        return $resultArray;

    }
    public function curl_get($url='', $options=array()){
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            if (!empty($options)){
                curl_setopt_array($ch, $options);
            }
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }

       public function curl_post($url='', $postdata='', $options=array()){
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            if (!empty($options)){
                curl_setopt_array($ch, $options);
            }
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
 
    public function smsCheckAction(Request $request, $type)
    {
        $targetSession = $request->getSession()->get($type);
        if (strlen($request->query->get('value'))==0||strlen($targetSession['sms_code'])==0) {
            $response = array('success' => false, 'message' => '验证码错误');
        }
        if ($targetSession['sms_code'] == $request->query->get('value')) {
            $response = array('success' => true, 'message' => '验证码正确');
        } else {
            $response = array('success' => false, 'message' => '验证码错误');
        }        
        return $this->createJsonResponse($response);
    }

    public function cloudCallBackAction(Request $request)
    {
        $settings = $this->getSettingService()->get('storage', array());

        $data = $request->request->all();
        $webAccessKey = empty($settings['cloud_access_key']) ? '' : $settings['cloud_access_key'];

        if(!empty($data['accessKey']) && $data['accessKey'] == $webAccessKey && !empty($data['action'])) {

            $setting['message'] = empty($data['reason']) ? '' : $data['reason'];

            $setting['status'] = $data['action'];

            $this->getSettingService()->set('cloud_sms', $setting);

            return $this->createJsonResponse(array('status'=>'ok'));
        }

        return $this->createJsonResponse(array('error'=>'accessKey error!'));
    }

    private function generateSmsCode($length = 6)
    {
        $code = rand(0, 9);
        for ($i = 1; $i < $length; $i++) {
            $code = $code . rand(0, 9);
        }
        return $code;
    }

    private function checkPhoneNum($num)
    {
        return preg_match("/^1\d{10}$/", $num);
    }

    private function checkLastTime($smsLastTime, $currentTime, $allowedTime = 120)
    {
        if (!((strlen($smsLastTime) == 0) || (($currentTime - $smsLastTime) > $allowedTime))) {
            return false;
        }
        return true;
    }

    private function checkSmsType($smsType, $user)
    {
        if (!in_array($smsType, array('sms_bind','sms_user_pay', 'sms_registration', 'sms_forget_password', 'sms_forget_pay_password'))) {
            throw new \RuntimeException('不存在的sms Type');
        }

        if ((!$user->isLogin()) && (in_array($smsType, array('sms_bind','sms_user_pay', 'sms_forget_pay_password')))) {
            throw new \RuntimeException('用户未登录');
        }

        if ($this->getCloudSmsKey($smsType) != 'on' && !$this->getUserService()->isMobileRegisterMode()) {
            throw new \RuntimeException('该使用场景未开启');
        }
    }

    private function getCloudSmsKey($key)
    {
        $setting = $this->getSettingService()->get('cloud_sms', array());
        if (isset($setting[$key])){
            return $setting[$key];
        }
        return null;
    }

    protected function getEduCloudService()
    {
        return $this->getServiceKernel()->createService('EduCloud.EduCloudService');
    }

    protected function getLogService()
    {
        return $this->getServiceKernel()->createService('System.LogService');
    }

    protected function getSettingService()
    {
        return $this->getServiceKernel()->createService('System.SettingService');
    }

    protected function getUserService()
    {
        return $this->getServiceKernel()->createService('User.UserService');
    }
}
