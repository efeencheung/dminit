<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Article;
use App\Util\TextareaImageUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * article controller.
 *
 * @route("article")
 */
class ArticleController extends Controller
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
        $qb ->select('count(a)')
            ->from('App:Article', 'a');

        $type = $request->query->get('type');
        if ($type) {
            $qb ->andWhere('a.type = :type')
                ->setParameter('type', $type);
        }
        
        $keywords = $request->query->get('keywords');
        if ($keywords) {
            $qb ->andWhere('a.title like :keywords')
                ->setParameter('keywords', '%' . $keywords . '%');
        } 

        $locale = $request->query->get('locale');
        if ($locale) {
            $qb ->andWhere('a.locale = :locale')
                ->setParameter('locale', $locale);
        }
        
        $tagId = $request->query->get('tid');
        if ($tagId) {
            $qb ->innerJoin('a.tags', 'tg')
                ->andWhere('tg.id = :tag_id')
                ->setParameter('tag_id', $tagId); 
        }   

        $query = $qb->getQuery();
        $total = $query->getSingleScalarResult();
        $count = ceil($total/$limit);

        $qb ->select('a')
            ->orderBy('a.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $qb->getQuery();
        $entities = $query->getResult();

        $entityData = [];
        $serializer = $this->get('serializer');
        foreach ($entities as $entity) {
            $entityDataItem = $serializer->normalize($entity, 'array');
            $entityDataItem['publishedAt'] = $entity->getPublishedAt()->format('Y-m-d H:i:s');
            if ($entity->getType() == 2) {
                $pictures = $entity->getPictures()->slice(0, 3);
                $entityDataItem['pictures'] = $serializer->normalize($pictures, 'array'); 
            }

            $entityData[] = $entityDataItem;
        }

        return new JsonResponse(['entities'=>$entityData, 'pageCount'=>$count]);
    }

    /**
     * 创建新资讯.
     *
     * @Route("/create", name="article_create")
     * @Method("POST")
     */
    public function createAction(Request $request, TextareaImageUtil $textareaImageUtil)
    {
        $entity = new Article();
        $entityData = $request->request->get('entity');
        $attachmentData = $request->files->get('entity');

        $em = $this->getDoctrine()->getManager();
        $em->getRepository('App:Article')->bind($entity, $entityData);
        $em->getRepository('App:Article')->bindAttachment($entity, $attachmentData);

        $validator = $this->get('validator');
        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            return new JsonResponse([
                '_status'=>'failed', 
                '_msg'=>$errors[0]->getMessage(), 
                '_field'=>$errors[0]->getPropertyPath()
            ], 400);
        }

        /* 处理图文内容中的图片 */
        $content = $textareaImageUtil->replaceImageString($entity->getContent());
        $entity->setContent($content);

        $em->persist($entity);
        $em->flush();

        return new JsonResponse(['_status'=>'success', '_msg'=>'资讯添加成功']);
    }

    /**
     * 更新资讯数据.
     *
     * @Route("/{id}", name="article_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, TextareaImageUtil $textareaImageUtil, Article $entity)
    {
        $entityData = $request->request->get('entity');
        $attachmentData = $request->files->get('entity');

        $em = $this->getDoctrine()->getManager();
        $em->getRepository('App:Article')->bind($entity, $entityData);
        $em->getRepository('App:Article')->bindAttachment($entity, $attachmentData);

        $validator = $this->get('validator');
        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            return new JsonResponse([
                '_status'=>'failed', 
                '_msg'=>$errors[0]->getMessage(), 
                '_field'=>$errors[0]->getPropertyPath()
            ], 400);
        }

        /* 处理图文内容中的图片 */
        $content = $textareaImageUtil->replaceImageString($entity->getContent());
        $entity->setContent($content);

        $em->persist($entity);
        $em->flush();

        return new JsonResponse(['_status'=>'success', '_msg'=>'资讯信息修改成功']);
    }

    /**
     * 获取资讯数据
     *
     * @Route("/{id}", name="article_show")
     * @Method("GET")
     */
    public function showAction(Article $entity)
    {
        $serializer = $this->get('serializer');
        $entityData = $serializer->normalize($entity, 'array');
        $tags = [];
        foreach ($entityData['tags'] as $tag) {
            $tags[] = (string) $tag['id'];
        }
        $entityData['tags'] = $tags;
        $entityData['publishedAt'] = $entity->getPublishedAt()->format("Y-m-d H:i:s");

        return new JsonResponse(['_status'=>'success', 'data'=>$entityData]);
    }


    /**
     * 删除资讯.
     *
     * @Route("/{id}", name="article_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Article $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return new JsonResponse(['_status'=>'success', '_msg'=>'资讯删除成功']);
    }

    /**
     * 添加资讯图片.
     *
     * @Route("/{id}/picture", name="article_picture_add")
     * @Method("POST")
     */
    public function pictureAddAction(Request $request, Article $entity)
    {
        $file = $request->files->get('file');
        $picture = new Picture();
        $picture->setFile($file);

        /* 验证图片格式的合法性 */
        $validator = $this->get('validator');
        $errors = $validator->validate($picture);
        if (count($errors) > 0) {
            return new JsonResponse([
                '_status'=>'fail', 
                '_msg'=>$errors[0]->getMessage(), 
                '_field'=>$errors[0]->getPropertyPath()
            ], 400);
        }
        /* 保存并上传图片 */
        $em = $this->getDoctrine()->getManager();
        $em->persist($picture);
        $em->flush();

        $entity->addPicture($picture);
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(['_status'=>'success', '_msg'=>'图片上传成功']);
    }

    /**
     * 资讯图片列表.
     *
     * @Route("/{id}/picture", name="article_picture_list")
     * @Method("GET")
     */
    public function pictureListAction(Article $entity)
    {
        $photos = $entity->getPictures();

        $photoData = [];
        $serializer = $this->get('serializer');
        foreach ($photos as $photo) {
            $photoDataItem = $serializer->normalize($photo, 'array');
            $photoData[] = $photoDataItem;
        }

        return new JsonResponse(['_status'=>'success', 'photos'=>$photoData]);
    }

    /**
     * 资讯图片删除.
     *
     * @Route("/{id}/picture/{pid}", name="article_picture_delete")
     * @Method("DELETE")
     */
    public function pictureDeleteAction(Article $entity, $pid)
    {
        $em = $this->getDoctrine()->getManager();
        $picture = $em->getRepository('App:Picture')->find($pid);

        $entity->removePicture($picture);
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(['_status'=>'success', '_msg'=>'图片删除成功']);
    }
}
