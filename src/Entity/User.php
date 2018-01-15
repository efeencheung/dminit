<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("username", message="用户名已存在", groups={"create"})
 */
class User implements UserInterface, \Serializable
{

    /**
     * 会员所属用户组
     */
    const ROLE = array('ROLE_ADMIN'=>'管理员', 'ROLE_USER'=>'普通用户');

    /**
     * ID
     *
     * @var int
     *
     * @Groups({"list", "info"})
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * 用户名
     *
     * @var string
     *
     * @Assert\NotBlank(message="用户名是必填项，不能为空", groups={"create"})
     * @Assert\Regex(pattern="/^[0-9a-z]{4,18}$/", message="格式不正确，用户名由4-18位的数字和字母组成", groups={"create"})
     * @Groups({"list", "info"})
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * 密码
     * TODO 编辑时密码长度验证
     *
     * @var string
     *
     * @Assert\NotBlank(message="密码是必填项，不能为空", groups={"create"})
     * @Assert\Length(min=4, max=18, minMessage="密码长度为4-18位", maxMessage="密码长度为4-18位", groups={"create"})
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * 显示名称
     *
     * @var string
     *
     * @Assert\NotBlank(message="显示名称是必填项，不能为空", groups={"create", "edit"})
     * @Groups({"list", "info"})
     * @ORM\Column(name="showname", type="string", length=255)
     */
    private $showname;

    /**
     * 微信OpenId
     *
     * @var string
     *
     * @ORM\Column(name="openId", type="string", length=255, nullable=true)
     */
    private $openId;

    /**
     * 微信SessionKey
     *
     * @var string
     *
     * @ORM\Column(name="sessionKey", type="string", length=255, nullable=true)
     */
    private $sessionKey;

    /**
     * 角色
     *
     * @var int
     *
     * @Assert\NotBlank(message="至少选择一个用户角色", groups={"create", "edit"})
     * @Groups({"list"})
     * @ORM\Column(name="role", type="string", length=255)
     */
    private $role;

    /**
     * 创建时间
     *
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * 更新时间
     * 
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

	/** 
	 * 用户头像
     * 
	 * @var Picture            
	 *  
     * @Groups({"list", "info"})
	 * @ORM\ManyToOne(targetEntity="\App\Entity\Picture", cascade={"all"})
	 * @ORM\JoinColumn(name="picture_id", referencedColumnName="id")
	 */
	private $avatar;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set showname
     *
     * @param string $showname
     *
     * @return User
     */
    public function setShowname($showname)
    {
        $this->showname = $showname;

        return $this;
    }

    /**
     * Get showname
     *
     * @return string
     */
    public function getShowname()
    {
        return $this->showname;
    }

    /**
     * Set role
     *
     * @param integer $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get rolename
     *
     * @Groups({"list"})
     * @return string
     */
    public function getRolename()
    {
        $roles = json_decode($this->role, true);
        $rolenames = [];
        foreach ($roles as $role) {
            $rolenames[] = self::ROLE[$role];
        }
        return implode($rolenames, ', ');
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set avatar
     *
     * @param \App\Entity\Picture $avatar
     *
     * @return User
     */
    public function setAvatar(\App\Entity\Picture $avatar = null)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return \App\Entity\Picture
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * Set openId
     *
     * @param string $openId
     *
     * @return User
     */
    public function setOpenId($openId)
    {
        $this->openId = $openId;

        return $this;
    }

    /**
     * Get openId
     *
     * @return string
     */
    public function getOpenId()
    {
        return $this->openId;
    }

    /**
     * Set sessionKey
     *
     * @param string $sessionKey
     *
     * @return User
     */
    public function setSessionKey($sessionKey)
    {
        $this->sessionKey = $sessionKey;

        return $this;
    }

    /**
     * Get sessionKey
     *
     * @return string
     */
    public function getSessionKey()
    {
        return $this->sessionKey;
    }

    /**
     * @Groups({"list"})
     */
    public function getRoles()
    {
        return json_decode($this->role, true);
    }

    public function eraseCredentials()
    {
    }

	public function serialize()
	{
		return serialize(array(
			$this->id,
			$this->username,
			$this->password,
		));
	}

	public function unserialize($serialized)
	{
		list (
			$this->id,
			$this->username,
			$this->password,
		) = unserialize($serialized);
	}
}
