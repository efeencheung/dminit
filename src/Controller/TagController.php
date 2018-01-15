<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Tag controller.
 *
 * @Route("tag")
 */
class TagController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb ->select('t')
            ->from('App:Tag', 't');

        $query = $qb->getQuery();
        $entities = $query->getResult();

        $serializer = $this->get('serializer');
        $entityData = array();
        foreach ($entities as $entity) {
            $entityDataItem = $serializer->normalize($entity, 'array');

            $entityData[] = $entityDataItem;
        }

        return new JsonResponse(array('entities'=>$entityData));
    }

    /**
     * 创建一个新标签.
     *
     * @Route("/create", name="tag_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entity = new Tag();
        $entityData = $request->request->get('entity');
        foreach ($entityData as $key => $value) {
            if (!is_array($value)) {
                call_user_func(array($entity, 'set'.ucfirst($key)), $value);
            } 
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            return new JsonResponse(array(
                '_status'=>'fail', 
                '_msg'=>$errors[0]->getMessage(), 
            ), 400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(array('_status'=>'success', '_msg'=>'标签添加成功'));
    }

    /**
     * 更新一个标签数据.
     *
     * @Route("/{id}", name="tag_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, Tag $entity)
    {
        $entityData = $request->request->get('entity');
        foreach ($entityData as $key => $value) {
            if (!is_array($value)) {
                call_user_func(array($entity, 'set'.ucfirst($key)), $value);
            } 
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($entity, null);
        if (count($errors) > 0) {
            return new JsonResponse(array(
                '_status'=>'fail', 
                '_msg'=>$errors[0]->getMessage(), 
            ), 400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(array('_status'=>'success', '_msg'=>'标签信息修改成功'));
    }

    /**
     * 显示一个标签数据.
     *
     * @Route("/{id}", name="tag_show")
     * @Method("GET")
     */
    public function showAction(Tag $entity)
    {
        $serializer = $this->get('serializer');
        $entityData = $serializer->normalize($entity, 'array');

        return new JsonResponse(array('_status'=>'success', 'entity'=>$entityData));
    }


    /**
     * 删除一个标签.
     *
     * @Route("/{id}", name="tag_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Tag $tag)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($tag);
        $em->flush();

        return new JsonResponse(array('_status'=>'success', '_msg'=>'标签删除成功'));
    }
}
