<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    /**
     * @param Request $request
     *
     * @return JsonResponse never null
     */
    public function onLogoutSuccess(Request $request)
    {
        return new JsonResponse(['_status'=>'success', '_msg'=>'成功退出'], Response::HTTP_UNAUTHORIZED);
    }
}
