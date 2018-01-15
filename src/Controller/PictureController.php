<?php

namespace App\Controller;

use App\Entity\Picture;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("picture")
 */
class PictureController extends Controller
{
    /**
     * @Route("/{id}", name="picture_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, Picture $entity)
    {
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
                '_msg'=>$errors[0]->getMessage() 
            ), 400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(array('_status'=>'success', '_msg'=>'图片信息更新成功'));
    }

    /**
     * @Route("/upload", name="picture_upload")
     * @Method("POST")
     */
    public function uploadAction(Request $request)
    {
        $file = $request->files->get('file');

        $entity = new Picture();
        $entity->setFile($file);

        $validator = $this->get('validator');
        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            return new JsonResponse(array('_status'=>'failed', '_msg'=>'照片尺寸太大，请压缩后上传'));
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(array('_status'=>'success', 'data'=>array('id'=>$entity->getId())));
    }

    /**
     * @Route("/{id}/cache")
     * @Method("GET")
     */
    public function cacheAction(Picture $picture, Request $request)
    {
        $filter = $request->query->get('filter');
        $path = $picture->getWebPath();

        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $resolvedPath = $imagineCacheManager->getBrowserPath($path, $filter);

        return $this->redirect($resolvedPath);
    }
}
