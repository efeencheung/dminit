<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * user controller.
 *
 * @route("user")
 */
class UserController extends Controller
{
    /**
     * @Route("/")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $page = $request->query->get('page');
        if (!$page) {
            $page = 1;
        }

        $limit = $request->query->get('limit');
        if (! $limit) {
            $limit = 10;
        }

        $offset = ($page-1) * $limit;
        
        $qb = $em->createQueryBuilder();
        $qb ->select('count(u)')
            ->from('App:User', 'u');

        $keywords = $request->query->get('keywords');
        if ($keywords) {
            $qb ->andWhere('u.username like :keywords')
                ->setParameter('keywords', '%' . $keywords . '%');
        } 

        $query = $qb->getQuery();
        $total = $query->getSingleScalarResult();
        $count = ceil($total/$limit);

        $qb ->select('u')
            ->orderBy('u.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $qb->getQuery();
        $entities = $query->getResult();

        $entityData = [];
        $serializer = $this->get('serializer');
        foreach ($entities as $entity) {
            $entityDataItem = $serializer->normalize($entity, 'array', ['groups' => ['list']]);
            $entityData[] = $entityDataItem;
        }

        return new JsonResponse(['entities'=>$entityData, 'pageCount'=>$count]);
    }

    /**
     * 创建新用户.
     *
     * @Route("/create", name="user_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $entityData = $request->request->get('entity');
        $attachmentData = $request->files->get('entity');

        /* 验证两次输入密码是否一致 */
        $confirmPassword = $request->request->get('confirm_password');
        if ($confirmPassword != $entityData['password']) {
            return new JsonResponse([
                '_status'=>'fail', 
                '_msg'=>'两次密码输入不一致'
            ], 400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->getRepository('App:User')->bind($entity, $entityData);
        $em->getRepository('App:User')->bindAttachment($entity, $attachmentData);

        $validator = $this->get('validator');
        $errors = $validator->validate($entity, null, ['create']);
        if (count($errors) > 0) {
            return new JsonResponse([
                '_status'=>'failed', 
                '_msg'=>$errors[0]->getMessage(), 
                '_field'=>$errors[0]->getPropertyPath()
            ], 400);
        }

        $encoder = $this->container->get('security.password_encoder');
        $newPassword = $encoder->encodePassword($entity, $entityData['password']);
        $entity->setPassword($newPassword);

        $em->persist($entity);
        $em->flush();

        return new JsonResponse(['_status'=>'success', '_msg'=>'用户添加成功']);
    }

    /**
     * 更新用户数据.
     *
     * @Route("/{id}", name="user_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, User $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $entityData = $request->request->get('entity');
        $attachmentData = $request->files->get('entity');

        if (isset($entityData['password']) && $entityData['password']) {
            /* 验证两次输入密码是否一致 */
            $confirmPassword = $request->request->get('confirm_password');
            if ($confirmPassword != $entityData['password']) {
                return new JsonResponse(array(
                    '_status'=>'fail', 
                    '_msg'=>'两次密码输入不一致'
                ), 400);
            }
        } else {
            unset($entityData['password']);
        }

        $em->getRepository('App:User')->bind($entity, $entityData);
        $em->getRepository('App:User')->bindAttachment($entity, $attachmentData);

        $validator = $this->get('validator');
        $errors = $validator->validate($entity, null, ['edit']);
        if (count($errors) > 0) {
            return new JsonResponse([
                '_status'=>'failed', 
                '_msg'=>$errors[0]->getMessage(), 
                '_field'=>$errors[0]->getPropertyPath()
            ], 400);
        }

        if (isset($entityData['password']) && $entityData['password']) {
            $encoder = $this->container->get('security.password_encoder');
            $newPassword = $encoder->encodePassword($entity, $entityData['password']);
            $entity->setPassword($newPassword);
        }

        $em->persist($entity);
        $em->flush();

        return new JsonResponse(['_status'=>'success', '_msg'=>'用户信息修改成功']);
    }

    /**
     * 获取用户数据
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(User $entity)
    {
        $serializer = $this->get('serializer');
        $entityData = $serializer->normalize($entity, 'array', ['groups'=>['list']]);

        return new JsonResponse(['_status'=>'success', 'data'=>$entityData]);
    }


    /**
     * 删除用户.
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, User $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return new JsonResponse(['_status'=>'success', '_msg'=>'用户删除成功']);
    }
}
