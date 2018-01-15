<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Storage, 支持无状态请求情况下的Key => Value 存储
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Storage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="k", type="string", length=255)
     */
    private $k;

    /**
     * @var string
     *
     * @ORM\Column(name="v", type="string", length=255)
     */
    private $v;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set k
     *
     * @param string $k
     *
     * @return Storage
     */
    public function setK($k)
    {
        $this->k = $k;

        return $this;
    }

    /**
     * Get k
     *
     * @return string
     */
    public function getK()
    {
        return $this->k;
    }

    /**
     * Set v
     *
     * @param string $v
     *
     * @return Storage
     */
    public function setV($v)
    {
        $this->v = $v;

        return $this;
    }

    /**
     * Get v
     *
     * @return string
     */
    public function getV()
    {
        return $this->v;
    }
}

