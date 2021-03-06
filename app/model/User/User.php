<?php

namespace App\Model\User;

use App\Model\ACL\Permission;
use App\Model\ACL\Resource;
use App\Model\ACL\Role;
use App\Model\Enums\ApplicationState;
use App\Model\Program\Block;
use App\Model\Program\Program;
use App\Model\Settings\CustomInput\CustomInput;
use App\Model\Structure\Subevent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Kdyby\Doctrine\Entities\Attributes\Identifier;


/**
 * Entita uživatele.
 *
 * @author Michal Májský
 * @author Jan Staněk <jan.stanek@skaut.cz>
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * Adresář pro ukládání profilových fotek.
     */
    const PHOTO_PATH = "/user_photos";

    use Identifier;

    /**
     * Uživatelské jméno skautIS.
     * @ORM\Column(type="string", unique=true, nullable=true, options={"collation":"utf8_bin"})
     * @var string
     */
    protected $username;

    /**
     * E-mail.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $email;

    /**
     * Přihlášky.
     * @ORM\OneToMany(targetEntity="Application", mappedBy="user", cascade={"persist"})
     * @var Collection
     */
    protected $applications;

    /**
     * Role.
     * @ORM\ManyToMany(targetEntity="\App\Model\ACL\Role", inversedBy="users")
     * @var Collection
     */
    protected $roles;

    /**
     * Přihlášené programy.
     * @ORM\ManyToMany(targetEntity="\App\Model\Program\Program", inversedBy="attendees")
     * @ORM\OrderBy({"start" = "ASC"})
     * @var Collection
     */
    protected $programs;

    /**
     * Lektorované bloky.
     * @ORM\OneToMany(targetEntity="\App\Model\Program\Block", mappedBy="lector", cascade={"persist"})
     * @var Collection
     */
    protected $lecturersBlocks;

    /**
     * Schválený.
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $approved = TRUE;

    /**
     * Jméno.
     * @ORM\Column(type="string")
     * @var string
     */
    protected $firstName;

    /**
     * Příjmení.
     * @ORM\Column(type="string")
     * @var string
     */
    protected $lastName;

    /**
     * Přezdívka.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $nickName;

    /**
     * Titul před jménem.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */

    protected $degreePre;

    /**
     * Titul za jménem.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $degreePost;

    /**
     * Zobrazované jméno - Příjmení Jméno (Přezdívka).
     * @ORM\Column(type="string")
     * @var string
     */
    protected $displayName;

    /**
     * Zobrazované jméno lektora, včetně titulů.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $lectorName;

    /**
     * Bezpečnostní kód.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $securityCode;

    /**
     * Propojený účet.
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $member = FALSE;

    /**
     * Jednotka.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $unit;

    /**
     * Pohlaví.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $sex;

    /**
     * Datum narození.
     * @ORM\Column(type="date", nullable=true)
     * @var \DateTime
     */
    protected $birthdate;

    /**
     * Id uživatele ve skautIS.
     * @ORM\Column(type="integer", unique=true, nullable=true, name="skautis_user_id")
     * @var int
     */
    protected $skautISUserId;

    /**
     * Id osoby ve skautIS.
     * @ORM\Column(type="integer", unique=true, nullable=true, name="skautis_person_id")
     * @var int
     */
    protected $skautISPersonId;

    /**
     * Datum prvního přihlášení.
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $firstLogin;

    /**
     * Datum posledního přihlášení.
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $lastLogin;

    /**
     * O mně.
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $about;

    /**
     * Ulice.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $street;

    /**
     * Město.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $city;

    /**
     * Poštovní směrovací číslo.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $postcode;

    /**
     * Stát.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $state;

    /**
     * Zúčastnil se.
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $attended = FALSE;

    /**
     * Příjezd.
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $arrival;

    /**
     * Odjezd.
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $departure;

    /**
     * Typ členství. NEPOUŽÍVÁ SE.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $membershipType;

    /**
     * Kategorie členství. NEPOUŽÍVÁ SE.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $membershipCategory;

    /**
     * Hodnoty vlastních polí přihlášky.
     * @ORM\OneToMany(targetEntity="\App\Model\User\CustomInputValue\CustomInputValue", mappedBy="user", cascade={"persist"})
     * @var Collection
     */
    protected $customInputValues;

    /**
     * Neveřejná poznámka.
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $note;

    /**
     * Fotka.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $photo;

    /**
     * Datum aktualizace fotky.
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $photoUpdate;


    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->applications = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->programs = new ArrayCollection();
        $this->lecturersBlocks = new ArrayCollection();
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param Collection $roles
     */
    public function setRoles(Collection $roles)
    {
        $this->roles->clear();
        foreach ($roles as $role)
            $this->roles->add($role);
    }

    /**
     * @param Role $role
     */
    public function addRole(Role $role)
    {
        if (!$this->isInRole($role))
            $this->roles->add($role);
    }

    /**
     * @param Role $role
     * @return bool
     */
    public function removeRole(Role $role)
    {
        return $this->roles->removeElement($role);
    }

    /**
     * Je uživatel v roli?
     * @param Role $role
     * @return bool
     */
    public function isInRole(Role $role)
    {
        return $this->roles->filter(function ($item) use ($role) {
            return $item == $role;
        })->count() != 0;
    }

    /**
     * Vrátí role uživatele oddělené čárkou.
     * @return string
     */
    public function getRolesText()
    {
        $rolesNames = [];
        foreach ($this->roles as $role) {
            $rolesNames[] = $role->getName();
        }
        return implode(', ', $rolesNames);
    }

    /**
     * Má uživatel oprávnění k prostředku?
     * @param $resource
     * @param $permission
     * @return bool
     */
    public function isAllowed($resource, $permission)
    {
        foreach ($this->roles as $r) {
            foreach ($r->getPermissions() as $p) {
                if ($p->getResource()->getName() == $resource && $p->getName() == $permission)
                    return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Je uživatel oprávněn upravovat blok?
     * @param Block $block
     * @return bool
     */
    public function isAllowedModifyBlock(Block $block)
    {
        if ($this->isAllowed(Resource::PROGRAM, Permission::MANAGE_ALL_PROGRAMS))
            return TRUE;

        if ($this->isAllowed(Resource::PROGRAM, Permission::MANAGE_OWN_PROGRAMS) && $block->getLector() === $this)
            return TRUE;

        return FALSE;
    }

    /**
     * @return bool
     */
    public function isAllowedRegisterPrograms()
    {
        return $this->isApproved() && $this->isAllowed(Resource::PROGRAM, Permission::CHOOSE_PROGRAMS);
    }

    /**
     * @return Collection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Vrátí platné přihlášky.
     * @return Collection|Application[]
     */
    public function getValidApplications(): Collection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->isNull('validTo'))
            ->orderBy(['applicationId' => 'ASC']);

        return $this->applications->matching($criteria);
    }

    /**
     * Vrátí nezrušené přihlášky.
     * @return Collection|Application[]
     */
    public function getNotCanceledApplications(): Collection
    {
        return $this->getValidApplications()->filter(function (Application $application) {
            return !$application->isCanceled();
        });
    }

    /**
     * Vrátí nezrušené přihlášky na rolí.
     * @return Collection|RolesApplication[]
     */
    public function getNotCanceledRolesApplications(): Collection
    {
        return $this->getNotCanceledApplications()->filter(function (Application $application) {
            return $application->getType() == Application::ROLES;
        });
    }

    /**
     * Vrátí nezrušené přihlášky na podakce.
     * @return Collection|SubeventsApplication[]
     */
    public function getNotCanceledSubeventsApplications(): Collection
    {
        return $this->getNotCanceledApplications()->filter(function (Application $application) {
            return $application->getType() == Application::SUBEVENTS;
        });
    }

    /**
     * Vrácí zaplacené přihlášky.
     * @return Collection
     */
    public function getPaidApplications()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->isNull('validTo'),
                Criteria::expr()->eq('state', ApplicationState::PAID)
            ));

        return $this->applications->matching($criteria);
    }

    /**
     * Vrátí přihlášky, které jsou zaplacené nebo zdarma.
     * @return Collection|Application[]
     */
    public function getPaidAndFreeApplications(): Collection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->isNull('validTo'),
                Criteria::expr()->orX(
                    Criteria::expr()->eq('state', ApplicationState::PAID),
                    Criteria::expr()->eq('state', ApplicationState::PAID_FREE)
                )
            ));

        return $this->applications->matching($criteria);
    }

    /**
     * Vrátí přihlášky čekající na platbu.
     * @return Collection|Application[]
     */
    public function getWaitingForPaymentApplications(): Collection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->isNull('validTo'),
                Criteria::expr()->eq('state', ApplicationState::WAITING_FOR_PAYMENT)
            ));

        return $this->applications->matching($criteria);
    }

    /**
     * Vrátí přihlášky rolí čekající na platbu.
     * @return Collection|RolesApplication[]
     */
    public function getWaitingForPaymentRolesApplications(): Collection
    {
        return $this->getWaitingForPaymentApplications()->filter(function (Application $application) {
            return $application->getType() == Application::ROLES;
        });
    }

    /**
     * Vrátí přihlášky podakcí čekající na platbu.
     * @return Collection|SubeventsApplication[]
     */
    public function getWaitingForPaymentSubeventsApplications(): Collection
    {
        return $this->getWaitingForPaymentApplications()->filter(function (Application $application) {
            return $application->getType() == Application::SUBEVENTS;
        });
    }

    /**
     * @return Collection
     */
    public function getPrograms()
    {
        return $this->programs;
    }

    /**
     * @param Collection $programs
     */
    public function setPrograms($programs)
    {
        $this->programs->clear();
        foreach ($programs as $program)
            $this->programs->add($program);
    }

    /**
     * @return Collection
     */
    public function getLecturersBlocks()
    {
        return $this->lecturersBlocks;
    }

    /**
     * @param Program $program
     */
    public function addProgram(Program $program)
    {
        if (!$this->programs->contains($program)) {
            $this->programs->add($program);
        }
    }

    /**
     * @param Program $program
     * @return bool
     */
    public function removeProgram(Program $program)
    {
        return $this->programs->removeElement($program);
    }

    /**
     * Má uživatel přihlášený program z bloku?
     * @param Block $block
     * @return bool
     */
    public function hasProgramBlock(Block $block)
    {
        return !$this->programs->filter(function (Program $program) use ($block) {
            return $program->getBlock() === $block;
        });
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        $this->updateDisplayName();
        $this->updateLectorName();
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        $this->updateDisplayName();
        $this->updateLectorName();
    }

    /**
     * @return string
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     * @param string $nickName
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;
        $this->updateDisplayName();
        $this->updateLectorName();
    }

    /**
     * @return string
     */
    public function getDegreePre()
    {
        return $this->degreePre;
    }

    /**
     * @param string $degreePre
     */
    public function setDegreePre($degreePre)
    {
        $this->degreePre = $degreePre;
        $this->updateLectorName();
    }

    /**
     * @return string
     */
    public function getDegreePost()
    {
        return $this->degreePost;
    }

    /**
     * @param string $degreePost
     */
    public function setDegreePost($degreePost)
    {
        $this->degreePost = $degreePost;
        $this->updateLectorName();
    }

    /**
     * @return string $displayName
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Aktualizuje zobrazované jméno.
     */
    private function updateDisplayName()
    {
        $this->displayName = $this->lastName . ' ' . $this->firstName;
        if ($this->nickName != NULL)
            $this->displayName .= ' (' . $this->nickName . ')';
    }

    /**
     * @return string
     */
    public function getLectorName()
    {
        return $this->lectorName;
    }

    /**
     * Aktualizuje jméno lektora.
     */
    public function updateLectorName()
    {
        $this->lectorName = '';
        if ($this->degreePre != NULL)
            $this->lectorName .= $this->degreePre . ' ';
        $this->lectorName .= $this->firstName . ' ' . $this->lastName;
        if ($this->degreePost != NULL)
            $this->lectorName .= ', ' . $this->degreePost;
        if ($this->nickName != NULL)
            $this->lectorName .= ' (' . $this->nickName . ')';
    }

    /**
     * @return string
     */
    public function getSecurityCode()
    {
        return $this->securityCode;
    }

    /**
     * @param string $securityCode
     */
    public function setSecurityCode($securityCode)
    {
        $this->securityCode = $securityCode;
    }

    /**
     * Má propojený účet?
     * @return bool
     */
    public function isMember()
    {
        return $this->member;
    }

    /**
     * @param bool $member
     */
    public function setMember($member)
    {
        $this->member = $member;
    }

    /**
     * Je bez skautIS účtu?
     * @return bool
     */
    public function isExternal()
    {
        return $this->username === NULL;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param string $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * @param \DateTime $birthdate
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->birthdate !== NULL ? (new \DateTime())->diff($this->birthdate)->y : NULL;
    }

    /**
     * @return int
     */
    public function getSkautISUserId()
    {
        return $this->skautISUserId;
    }

    /**
     * @param int $skautISUserId
     */
    public function setSkautISUserId($skautISUserId)
    {
        $this->skautISUserId = $skautISUserId;
    }

    /**
     * @return int
     */
    public function getSkautISPersonId()
    {
        return $this->skautISPersonId;
    }

    /**
     * @param int $skautISPersonId
     */
    public function setSkautISPersonId($skautISPersonId)
    {
        $this->skautISPersonId = $skautISPersonId;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTime $lastLogin
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @param string $about
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Vrátí adresu uživatele.
     * @return null|string
     */
    public function getAddress()
    {
        if (empty($this->street) || empty($this->city) || empty($this->postcode))
            return NULL;
        return $this->street . ', ' . $this->city . ', ' . $this->postcode;
    }

    /**
     * @return bool
     */
    public function isAttended()
    {
        return $this->attended;
    }

    /**
     * @param bool $attended
     */
    public function setAttended($attended)
    {
        $this->attended = $attended;
    }

    /**
     * @return \DateTime
     */
    public function getArrival()
    {
        return $this->arrival;
    }

    /**
     * @param \DateTime $arrival
     */
    public function setArrival($arrival)
    {
        $this->arrival = $arrival;
    }

    /**
     * @return \DateTime
     */
    public function getDeparture()
    {
        return $this->departure;
    }

    /**
     * @param \DateTime $departure
     */
    public function setDeparture($departure)
    {
        $this->departure = $departure;
    }

    /**
     * @return string
     */
    public function getMembershipType()
    {
        return $this->membershipType;
    }

    /**
     * @param string $membershipType
     */
    public function setMembershipType($membershipType)
    {
        $this->membershipType = $membershipType;
    }

    /**
     * @return string
     */
    public function getMembershipCategory()
    {
        return $this->membershipCategory;
    }

    /**
     * @param string $membershipCategory
     */
    public function setMembershipCategory($membershipCategory)
    {
        $this->membershipCategory = $membershipCategory;
    }

    /**
     * @return Collection
     */
    public function getCustomInputValues()
    {
        return $this->customInputValues;
    }

    /**
     * @param CustomInput $customInput
     * @return mixed
     */
    public function getCustomInputValue(CustomInput $customInput)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()
                ->eq('input', $customInput)
            );
        return $this->customInputValues->matching($criteria)->first();
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return \DateTime
     */
    public function getPhotoUpdate()
    {
        return $this->photoUpdate;
    }

    /**
     * @param \DateTime $photoUpdate
     */
    public function setPhotoUpdate($photoUpdate)
    {
        $this->photoUpdate = $photoUpdate;
    }

    /**
     * Je uživatel v roli, u které se eviduje příjezd a odjezd?
     * @return bool
     */
    public function hasDisplayArrivalDepartureRole()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('displayArrivalDeparture', TRUE));

        return !$this->roles->matching($criteria)->isEmpty();
    }

    /**
     * Je uživatel platící?
     * @return bool
     */
    public function isPaying()
    {
        return $this->getFee() != 0;
    }

    /**
     * Vrací poplatek uživatele.
     * @return int
     */
    public function getFee()
    {
        $fee = 0;
        foreach ($this->getNotCanceledApplications() as $application) {
            $fee += $application->getFee();
        }
        return $fee;
    }

    /**
     * Vrací částku, která zbývá uhradit.
     * @return int
     */
    public function getFeeRemaining()
    {
        $fee = 0;
        foreach ($this->getWaitingForPaymentApplications() as $application) {
            $fee += $application->getFee();
        }
        return $fee;
    }

    /**
     * Vrací, zda má uživatel nějakou roli, která nemá cenu podle podakcí.
     * @return bool
     */
    public function hasFixedFeeRole(): bool
    {
        return $this->roles->exists(function (int $key, Role $role) {return $role->getFee() !== NULL;});
    }

    /**
     * Vrácí, zda má uživatel zaplacenou přihlášku s podakcí.
     * @param Subevent $subevent
     * @return bool
     */
    public function hasPaidSubevent(Subevent $subevent)
    {
        foreach ($this->getPaidAndFreeApplications() as $application)
            if ($application->getType() == Application::SUBEVENTS && $application->getSubevents()->contains($subevent))
                return TRUE;

        return FALSE;
    }

    /**
     * Vrací datum přihlášení.
     * @return \DateTime|null
     */
    public function getRolesApplicationDate()
    {
        foreach ($this->getNotCanceledRolesApplications() as $application) {
            return $application->getApplicationDate();
        }
        return NULL;
    }

    /**
     * Vrací datum poslední platby.
     * @return \DateTime|null
     */
    public function getLastPaymentDate()
    {
        $maxDate = NULL;
        foreach ($this->getValidApplications() as $application) {
            if ($maxDate === NULL || $maxDate < $application->getPaymentDate())
                $maxDate = $application->getPaymentDate();
        }
        return $maxDate;
    }

    /**
     * Vrací podakce uživatele.
     * @return Collection|Subevent[]
     */
    public function getSubevents(): Collection
    {
        $subevents = new ArrayCollection();

        foreach ($this->getNotCanceledSubeventsApplications() as $application)
            foreach ($application->getSubevents() as $subevent)
                $subevents->add($subevent);

        return $subevents;
    }

    /**
     * Vrátí podakce uživatele oddělené čárkou.
     * @return string
     */
    public function getSubeventsText(): string
    {
        $subeventsNames = $this->getSubevents()->map(function (Subevent $subevent) {return $subevent->getName();});
        return implode(', ', $subeventsNames->toArray());
    }

    /**
     * Vrací, zda je uživatel přihlášen na podakci.
     * @param Subevent $subevent
     * @return bool
     */
    public function hasSubevent(Subevent $subevent)
    {
        return $this->getSubevents()->contains($subevent);
    }

    /**
     * Vrací zda uživatel zaplatil první registraci.
     * @return bool
     */
    public function hasPaidAnyApplication()
    {
        return !$this->getPaidApplications()->isEmpty();
    }

    /**
     * Vrací zda uživatel zaplatil všechny registrace.
     * @return bool
     */
    public function hasPaidEveryApplication()
    {
        return $this->getValidApplications()->forAll(function (int $key, Application $application) {
            return $application->getState() != ApplicationState::WAITING_FOR_PAYMENT;
        });
    }

    /**
     * Vrátí variabilní symboly oddělené čárkou.
     * @return string
     */
    public function getVariableSymbolsText()
    {
        $variableSymbols = $this->getNotCanceledApplications()->map(function (Application $application) {
            return $application->getVariableSymbolText();
        });
        return implode(', ', $variableSymbols->toArray());
    }
}
