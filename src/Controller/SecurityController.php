<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Picture;
use App\Entity\Storage;
use App\Util\Sms;
use App\Wechat\WXBizDataCrypt;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class SecurityController extends Controller
{
    /**
     * @Route("/loggeduser", name="loggeduser")
     * @Method("GET")
     */
    public function loggedUserAction()
    {
        $user = $this->getUser();
        $serializer = $this->get('serializer');
        $userData = $serializer->normalize($user, 'array', ['groups'=>['info']]);

        return new JsonResponse(['_status'=>'success', 'data'=>$userData]);
    }

    /**
     * @Route("/wxlogin", name="wxlogin")
     * @Method("GET")
     */
    public function wxloginAction(Request $request)
    {
        $code = $request->query->get('wxappcode');
        $client = new Client(array('base_uri'=>'https://api.weixin.qq.com'));
        $response = $client->request('GET', '/sns/jscode2session', array(
            'verify'=>false,
            'query'=>array(
                'appid'=>'wxe56d01d0b519e3eb',
                'secret'=>'92e974d93b8b4f968bf4abc0f6e7c71c',
                'js_code'=>$code,
                'grant_type'=>'authorization_code'
            )
        ));

        $content = $response->getBody()->getContents();
        $result = json_decode($content, true);
        if (! isset($result['openid'])) {
            return new JsonResponse(['_status'=>'fail', '_msg'=>'校验失败'], 401);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')->findOneBy(array('openId'=>$result['openid']));
        if (!$user instanceof User) {
            $user = new User();
            $user->setUsername($result['openid']);
            $user->setPassword($result['openid']);
            $user->setShowname('微信用户');
            $user->setRole(json_encode(['ROLE_USER']));
            $user->setOpenId($result['openid']);
        } 
        $user->setSessionKey($result['session_key']);
        $em->persist($user);
        $em->flush();

        $data = ['openId' => $result['openid']];
        return new JsonResponse($data);
    }

    /**
     * @Route("/wxuser/status", name="wxuser_status")
     * @Method("GET")
     */
    public function wxuserStatusAction()
    {
        $user = $this->getUser();

        if (preg_match('/^1[3-8][0-9]{9}$/', $user->getUsername())) {
            $isBound = true;
        } else {
            $isBound = false;
        }

        if ($user->getShowname() == '微信用户') {
            $hasInfo = false; 
        } else {
            $hasInfo = true;
        }

        $data = ['isBound' => $isBound, 'hasInfo' => $hasInfo];

        return new JsonResponse($data);
    }

    /**
     * @Route("/wxuser/updateinfo", name="wxuser_updateinfo")
     * @Method("POST")
     */
    public function wxuserUpdateInfoAction(Request $request) 
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $showname = $request->request->get('showname');
        $avatarUrl = $request->request->get('avatarUrl');
        $filepath = realpath(__DIR__ . '/../../public/upload') . '/remotetmp.jpeg';
        file_put_contents($filepath, file_get_contents($avatarUrl));
        $file = new UploadedFile($filepath, 'remotetmp.jpeg', 'image/jpeg', null, null, true);
        $picture = new Picture();
        $picture->setFile($file);
        $em->persist($picture);
        $em->flush();

        $user->setShowname($showname);
        $user->setAvatar($picture);
        $em->persist($user);
        $em->flush();
        
        $serializer = $this->get('serializer');
        $userData = $serializer->normalize($user, 'array', ['groups'=>['info']]);

        return new JsonResponse(['_status' => 'success', '_msg' => '用户信息更新成功', 'data' => $userData]);
    }

    /**
     * @Route("/wxuser/bind", name="wxuser_quickbind")
     * @Method("POST")
     */
    public function wxuserBindAction(Request $request) 
    {
        $user = $this->getUser();
        $openId = $user->getOpenId();
        $sessionKey = $user->getSessionKey();

        $phoneNumber = $request->request->get('phone_number');
        $verifyCode = $request->request->get('verify_code');

        if ($user->getUsername() == $phoneNumber) {
            return new JsonResponse(['_status'=>'fail', '_msg'=>'当前手机号已绑定成功，请勿重复绑定'], 400);
        }

        $em = $this->getDoctrine()->getManager();
        $storage = $em->getRepository('App:Storage')->findOneBy(array('k'=>$phoneNumber));
        if (!$storage instanceof Storage) {
            return new JsonResponse(['_status'=>'fail', '_msg'=>'验证码不正确'], 400);
        }
        $code = $storage->getV();
        if ($code != $verifyCode) {
            return new JsonResponse(['_status'=>'fail', '_msg'=>'验证码不正确'], 400);
        }

        $tmpUser = $em->getRepository('App:User')->findOneBy(array('username'=>$phoneNumber));
        if ($tmpUser instanceof User) {
            $em->remove($user);
            $em->flush();

            $tmpUser->setOpenId($openId);
            $tmpUser->setSessionKey($sessionKey);
            $em->persist($tmpUser);
            $em->flush();
        } else {
            $user->setUsername($phoneNumber);
            $em->persist($user);
            $em->flush();
        }

        return new JsonResponse(array('_status'=>'success', '_msg'=>'手机已绑定'));
    }

    /**
     * @Route("/wxuser/bind/verifycode", name="wxuser_bind_verifycode")
     * @Method("GET")
     */
    public function verifyCodeAction(Request $request, Sms $sms)
    {
        $phoneNumber = $request->query->get('phone_number');

        $em = $this->getDoctrine()->getManager();
        $storage = $em->getRepository('App:Storage')->findOneBy(array('k'=>$phoneNumber));
        if (! $storage instanceof Storage) {
            $storage = new Storage();
            $storage->setK($phoneNumber);
        }

        $code = rand(1000, 9999);

        $storage->setV($code);
        $em->persist($storage);
        $em->flush();

        $result = $sms->send($phoneNumber, '马拉松大满贯组委会', 'SMS_76620649', array('code'=>$code));

        if ($result) {
            return new JsonResponse(array('_status'=>'success'));
        } else {
            return new JsonResponse(array('_status'=>'fail'));
        }
    }

    /**
     * 微信用户快速绑定手机
     *
     * @Route("/wxuser/quickbind", name="wxuser_quickbind")
     * @Method("POST")
     */
    public function wxuserQuickBindAction(Request $request) 
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $appid = 'wxe56d01d0b519e3eb';
        $openId = $user->getOpenId();
        $sessionKey = $user->getSessionKey();

        $iv = $request->request->get('iv');
        $encryptedData = $request->request->get('encryptedData');
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            $rawData = json_decode($data, true);
        } else {
            return new JsonResponse(['_status'=>'fail', '_msg'=>'绑定失败，请稍后再试或使用普通方式绑定'], 400);
        }

        $phoneNumber = $rawData['purePhoneNumber'];
        /* 判定当前绑定手机与请求的手机号码是否相同 */
        if ($user->getUsername() == $phoneNumber) {
            return new JsonResponse(['_status'=>'fail', '_msg'=>'当前手机号已绑定成功，请勿重复绑定'], 400);
        }

        $tmpUser = $em->getRepository('App:User')->findOneBy(array('username'=>$phoneNumber));
        if ($tmpUser instanceof User) {
            $em->remove($user);
            $em->flush();

            $tmpUser->setOpenId($openId);
            $tmpUser->setSessionKey($sessionKey);
            $em->persist($tmpUser);
            $em->flush();
        } else {
            $user->setUsername($phoneNumber);
            $em->persist($user);
            $em->flush();
        }

        return new JsonResponse(array('_status'=>'success', '_msg'=>'手机已绑定'));
    }
}
