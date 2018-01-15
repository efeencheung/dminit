<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EntityRepository extends BaseEntityRepository
{
    /**
     * 绑定数据到Entity
     */
    public function bind($entity, $data)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach($this->_class->fieldMappings as $field) {
            if (!isset($data[$field['fieldName']])) {
                continue; 
            }

            if ($field['type'] == 'datetime') {
                $data[$field['fieldName']] = new \DateTime($data[$field['fieldName']]);
            }

            $accessor->setValue($entity, $field['fieldName'], $data[$field['fieldName']]);
        }
        
        foreach ($this->_class->associationMappings as $association) {
            if (!isset($data[$association['fieldName']])) {
                continue; 
            }

            $associationData = $data[$association['fieldName']];
            $subRepository = $this->_em->getRepository($association['targetEntity']);

            if ($association['type'] == 2 && isset($associationData)) {
                $subEntity = $subRepository->find($associationData);
                $accessor->setValue($entity, $association['fieldName'], $subEntity);
            }

            if ($association['type'] == 8 ) {
                $subEntities = [];
                foreach ($associationData as $item) {
                    $subEntity = $subRepository->find($item);
                    $subEntities[] = $subEntity;
                }

                $accessor->setValue($entity, $association['fieldName'], $subEntities);
            }
        }
    }

    /**
     * 绑定二进制数据到Entity
     */
    public function bindAttachment($entity, $attachmentData)
    {
        if (!is_array($attachmentData)) {
            return;   
        }

        $accessor = PropertyAccess::createPropertyAccessor();
        foreach($attachmentData as $field => $value) {
            if (!$value) {
                continue;
            }
            $mapping = $this->_class->associationMappings[$field];
            $subEntity = new $mapping['targetEntity'];
            $accessor->setValue($subEntity, 'file', $value);
            $accessor->setValue($entity, $field, $subEntity);
        }
    }
}
