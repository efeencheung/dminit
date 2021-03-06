<?php

namespace App\EventListener;

use App\Concept\UploadAbleInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class UploadListener
{
    private $fs;

    public function __construct(FileSystem $fs)
    {
        $this->fs = $fs;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof UploadAbleInterface) {
            return;
        }

        $this->uploadFile($entity);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof UploadAbleInterface) {
            return;
        }

        $this->uploadFile($entity);
        $this->removeOldFile($entity);
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof UploadAbleInterface) {
            return;
        }

        $this->removeFile($entity);
    }

    protected function uploadFile($entity)
    {
        $file = $entity->getFile();

        if (!$file instanceof UploadedFile) {
            return;
        }

        $filename = sha1(uniqid(mt_rand(), true)) . '.' . $file->guessClientExtension(); // 生成一个唯一文件名称
        $webPath = '/upload/' . $entity->getTypeName() . '/' . substr($filename, 0, 2); // Web路径
        $filePath = realpath(__DIR__ . '/../../public') . $webPath; // 文件系统路径

        if (!$this->fs->exists($filePath)) {
           $this->fs->mkdir($filePath);
        }

        $file->move($filePath, $filename);

        $entity->setFilename($filename);
    }

    protected function removeFile($entity)
    {
        $filename = $entity->getFilename();

        $webPath = '/upload/' . $entity->getTypeName() . '/' . substr($filename, 0, 2); // Web路径
        $filePath = realpath(__DIR__ . '/../../public') . $webPath; // 文件系统路径

        if (!$this->fs->exists($filePath)) {
           return;
        }

        /* 避免BUG误删文件夹 */
        if (is_dir($filePath)) {
            return;
        }

        $this->fs->remove($filePath);
    }

    protected function removeOldFile($entity)
    {
        $filename = $entity->getTempFilename();

        $webPath = '/upload/' . $entity->getTypeName() . '/' . substr($filename, 0, 2); // Web路径
        $filePath = realpath(__DIR__ . '/../../public') . $webPath; // 文件系统路径

        if (!$this->fs->exists($filePath)) {
           return;
        }

        /* 避免BUG误删文件夹 */
        if (is_dir($filePath)) {
            return;
        }
        $this->fs->remove($filePath);
    }
}
