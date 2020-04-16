<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Enum\DateFormatEnum;
use App\Enum\ImageTypeEnum;
use App\Enum\LocaleEnum;
use App\Enum\RoleEnum;
use App\Enum\ThemeEnum;
use App\Enum\VisibilityEnum;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="koi_user", indexes={
 *     @ORM\Index(name="idx_user_visibility", columns={"visibility"})
 * })
 * @UniqueEntity(fields={"email"}, message="error.email.not_unique")
 * @UniqueEntity(fields={"username"}, message="error.username.not_unique")
 */
class User implements UserInterface, BreadcrumbableInterface
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidInterface $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, unique=true)
     * @Assert\Regex(pattern="/^[a-z\d_]{2,32}$/i", message="error.username.incorrect")
     */
    private ?string $username = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email()
     */
    private ?string $email = null;

    /**
     * @var string
     */
    private ?string $salt = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $password;

    /**
     * @var string
     * @Assert\Regex(pattern="/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Za-z]).*$/", message="error.password.incorrect")
     */
    private ?string $plainPassword = null;

    /**
     * @var Image
     * @ORM\OneToOne(targetEntity="Image", cascade={"all"}, orphanRemoval=true)
     */
    private ?Image $avatar = null;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private bool $enabled;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private array $roles;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $theme;

    /**
     * @var string
     * @ORM\Column(type="string", length=3)
     */
    private string $currency;

    /**
     * @var string
     * @ORM\Column(type="string", length=2)
     */
    private string $locale;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private ?string $timezone = null;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $dateFormat;

    /**
     * @var int
     * @ORM\Column(type="bigint", options={"default"=0})
     */
    private int $diskSpaceUsed;

    /**
     * @var int
     * @ORM\Column(type="bigint", options={"default"=268435456})
     * @Assert\GreaterThanOrEqual(propertyPath="diskSpaceUsed")
     */
    private int $diskSpaceAllowed;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $visibility;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Collection", mappedBy="owner", cascade={"remove"})
     */
    private DoctrineCollection $collections;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="owner", cascade={"remove"})
     */
    private DoctrineCollection $tags;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="TagCategory", mappedBy="owner", cascade={"remove"})
     */
    private DoctrineCollection $tagCategories;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Wishlist", mappedBy="owner", cascade={"remove"})
     */
    private DoctrineCollection $wishlists;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Template", mappedBy="owner", cascade={"remove"})
     */
    private DoctrineCollection $templates;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Log", mappedBy="user", cascade={"remove"})
     */
    private DoctrineCollection $logs;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Album", mappedBy="owner", cascade={"remove"})
     */
    private DoctrineCollection $albums;

    /**
     * @var DoctrineCollection
     * @ORM\OneToMany(targetEntity="Inventory", mappedBy="owner", cascade={"remove"})
     */
    private DoctrineCollection $inventories;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="date", nullable=true)
     */
    private ?\DateTimeInterface $lastDateOfActivity = null;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime", nullable=true)
     */
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->roles = ['ROLE_USER'];
        $this->diskSpaceAllowed = 536870912;
        $this->diskSpaceUsed = 0;
        $this->enabled = false;
        $this->theme = ThemeEnum::THEME_TEAL;
        $this->currency = 'EUR';
        $this->locale = LocaleEnum::LOCALE_GB;
        $this->visibility = VisibilityEnum::VISIBILITY_PRIVATE;
        $this->dateFormat = DateFormatEnum::FORMAT_HYPHEN_YMD;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUsername() ?? '';
    }

    public function isAdmin()
    {
        return \in_array(RoleEnum::ROLE_ADMIN, $this->roles, true);
    }

    public function getDateFormatForJs() : string
    {
        return DateFormatEnum::MAPPING[$this->dateFormat][DateFormatEnum::CONTEXT_JS];
    }

    public function getDateFormatForForm() : string
    {
        return DateFormatEnum::MAPPING[$this->dateFormat][DateFormatEnum::CONTEXT_FORM];
    }

    public function getOwner(): ?self
    {
        return $this;
    }

    public function eraseCredentials()
    {
    }

    public function getUsername() : ?string
    {
        return $this->username;
    }

    public function getSalt() : ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt) : self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getPassword() : ?string
    {
        return $this->password;
    }

    public function setPassword(string $password) : self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword() : ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword) : self
    {
        $this->plainPassword = $plainPassword;
        $this->password = $plainPassword;

        return $this;
    }

    public function getRoles() : array
    {
        return $this->roles;
    }

    public function setRoles(array $roles) : self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role) : self
    {
        $role = strtoupper($role);
        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role) : self
    {
        if (false !== $key = \array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = \array_values($this->roles);
        }

        return $this;
    }

    public function increaseDiskSpaceUsed(int $value) : self
    {
        $this->diskSpaceUsed += $value;

        return $this;
    }

    public function decreaseDiskSpaceUsed(int $value) : self
    {
        $this->diskSpaceUsed -= $value;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    public function setDateFormat(string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    public function getDiskSpaceUsed(): ?int
    {
        return $this->diskSpaceUsed;
    }

    public function setDiskSpaceUsed(int $diskSpaceUsed): self
    {
        $this->diskSpaceUsed = $diskSpaceUsed;

        return $this;
    }

    public function getDiskSpaceAllowed(): ?int
    {
        return $this->diskSpaceAllowed;
    }

    public function setDiskSpaceAllowed(int $diskSpaceAllowed): self
    {
        $this->diskSpaceAllowed = $diskSpaceAllowed;

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getLastDateOfActivity(): ?\DateTimeInterface
    {
        return $this->lastDateOfActivity;
    }

    public function setLastDateOfActivity(?\DateTimeInterface $lastDateOfActivity): self
    {
        $this->lastDateOfActivity = $lastDateOfActivity;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAvatar(): ?Image
    {
        return $this->avatar;
    }

    public function setAvatar(?Image $avatar): self
    {
        $avatar->setType(ImageTypeEnum::TYPE_AVATAR);
        $this->avatar = $avatar;

        return $this;
    }
}
