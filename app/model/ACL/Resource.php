<?php

namespace App\Model\ACL;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;


/**
 * Entita prostředek.
 *
 * @author Michal Májský
 * @author Jan Staněk <jan.stanek@skaut.cz>
 * @ORM\Entity(repositoryClass="ResourceRepository")
 * @ORM\Table(name="resource")
 */
class Resource
{
    /**
     * Administrace.
     */
    const ADMIN = 'admin';

    /**
     * Web.
     */
    const CMS = 'cms';

    /**
     * Role.
     */
    const ACL = 'acl';

    /**
     * Program.
     */
    const PROGRAM = 'program';

    /**
     * Nastavení.
     */
    const CONFIGURATION = 'configuration';

    /**
     * Uživatelé.
     */
    const USERS = 'users';

    /**
     * Mailing.
     */
    const MAILING = 'mailing';

    public static $resources = [
        self::ADMIN,
        self::CMS,
        self::ACL,
        self::PROGRAM,
        self::CONFIGURATION,
        self::USERS,
        self::MAILING
    ];

    use Identifier;

    /**
     * Název prostředku.
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    protected $name;

    /**
     * Oprávnění s tímto prostředkem.
     * @ORM\OneToMany(targetEntity="\App\Model\ACL\Permission", mappedBy="resource", cascade={"persist"})
     * @var Collection
     */
    protected $permissions;


    /**
     * Resource constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->permissions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Collection
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}
