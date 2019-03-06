<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Enum\CurrencyEnum;
use App\Enum\LocaleEnum;
use App\Enum\RoleEnum;
use App\Enum\ThemeEnum;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="koi_user")
 * @UniqueEntity(fields={"email"}, message="error.email.not_unique")
 * @UniqueEntity(fields={"username"}, message="error.username.not_unique")
 */
class User implements UserInterface, BreadcrumbableInterface
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, unique=true)
     * @Assert\Regex(pattern="/^[a-z\d_]{2,32}$/i", message="error.username.incorrect")
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email()
     */
    protected $email;

    /**
     * @var string
     */
    protected $salt;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @var string
     * @Assert\Regex(pattern="/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Za-z]).*$/", message="error.password.incorrect")
     */
    protected $plainPassword;

    /**
     * @var Medium
     * @ORM\OneToOne(targetEntity="Medium", cascade={"all"})
     */
    protected $avatar;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $theme;

    /**
     * @var string
     * @ORM\Column(type="string", length=3)
     */
    private $currency;

    /**
     * @var string
     * @ORM\Column(type="string", length=2)
     */
    private $locale;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $timezone;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $diskSpaceUsed;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $diskSpaceAllowed;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $visibility;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Collection", mappedBy="owner", cascade={"remove"})
     */
    private $collections;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="owner", cascade={"remove"})
     */
    private $tags;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Wishlist", mappedBy="owner", cascade={"remove"})
     */
    private $wishlists;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Template", mappedBy="owner", cascade={"remove"})
     */
    private $templates;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Log", mappedBy="user", cascade={"remove"})
     */
    private $logs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Album", mappedBy="owner", cascade={"remove"})
     */
    private $albums;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    private $lastDateOfActivity;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->roles = ['ROLE_USER'];
        $this->diskSpaceAllowed = 536870912;
        $this->diskSpaceUsed = 0;
        $this->enabled = false;
        $this->theme = ThemeEnum::THEME_TEAL;
        $this->currency = CurrencyEnum::CURRENCY_EUR;
        $this->locale = LocaleEnum::LOCALE_EN;
        $this->visibility = VisibilityEnum::VISIBILITY_PRIVATE;
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

    /**
     * @return User
     */
    public function getOwner(): ?self
    {
        return $this;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    /**
     * Set theme.
     *
     * @param string $theme
     *
     * @return User
     */
    public function setTheme(string $theme) : self
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme.
     *
     * @return string
     */
    public function getTheme() : string
    {
        return $this->theme;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername() : ?string
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username) : self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email) : self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail() : ?string
    {
        return $this->email;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return User
     */
    public function setEnabled(bool $enabled) : self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function isEnabled() : bool
    {
        return $this->enabled;
    }

    /**
     * Get salt.
     *
     * @return string
     */
    public function getSalt() : ?string
    {
        return $this->salt;
    }

    /**
     * Set salt.
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt(?string $salt) : self
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get encrypted password.
     *
     * @return string
     */
    public function getPassword() : ?string
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password) : self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get encrypted password.
     *
     * @return string
     */
    public function getPlainPassword() : ?string
    {
        return $this->plainPassword;
    }

    /**
     * Set plainPassword.
     *
     * @param string $plainPassword
     *
     * @return User
     */
    public function setPlainPassword(?string $plainPassword) : self
    {
        $this->plainPassword = $plainPassword;
        $this->password = $plainPassword;

        return $this;
    }

    /**
     * Get roles.
     *
     * @return array
     */
    public function getRoles() : array
    {
        return $this->roles;
    }

    /**
     * Set roles.
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles) : self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Add a role.
     *
     * @param string $role
     *
     * @return User
     */
    public function addRole(string $role) : self
    {
        $role = strtoupper($role);
        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Remove a role.
     *
     * @param string $role
     *
     * @return User
     */
    public function removeRole(string $role) : self
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * Set diskSpaceUsed.
     *
     * @param int $diskSpaceUsed
     *
     * @return User
     */
    public function setDiskSpaceUsed(int $diskSpaceUsed) : self
    {
        $this->diskSpaceUsed = $diskSpaceUsed;

        return $this;
    }

    /**
     * increase diskSpaceUsed.
     *
     * @param int $value
     *
     * @return User
     */
    public function increaseDiskSpaceUsed(int $value) : self
    {
        $this->diskSpaceUsed += $value;

        return $this;
    }

    /**
     * decrease diskSpaceUsed.
     *
     * @param int $value
     *
     * @return User
     */
    public function decreaseDiskSpaceUsed(int $value) : self
    {
        $this->diskSpaceUsed -= $value;

        return $this;
    }

    /**
     * Get diskSpaceUsed.
     *
     * @return int
     */
    public function getDiskSpaceUsed() : int
    {
        return $this->diskSpaceUsed;
    }

    /**
     * Set locale.
     *
     * @param string $locale
     *
     * @return User
     */
    public function setLocale(string $locale) : self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale() : string
    {
        return $this->locale;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return User
     */
    public function setCurrency(string $currency) : self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency() : ?string
    {
        return $this->currency;
    }

    /**
     * Set avatar.
     *
     * @param Medium $avatar
     *
     * @return User
     */
    public function setAvatar(Medium $avatar = null) : self
    {
        if ($avatar === null) {
            return $this;
        }

        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Alias for setAvatar.
     *
     * @param Medium $avatar
     *
     * @return User
     */
    public function setImage(Medium $avatar = null) : self
    {
        return $this->setAvatar($avatar);
    }

    /**
     * Set diskSpaceAllowed.
     *
     * @param int $diskSpaceAllowed
     *
     * @return User
     */
    public function setDiskSpaceAllowed(int $diskSpaceAllowed) : self
    {
        $this->diskSpaceAllowed = $diskSpaceAllowed;

        return $this;
    }

    /**
     * Get diskSpaceAllowed.
     *
     * @return int
     */
    public function getDiskSpaceAllowed() : int
    {
        return $this->diskSpaceAllowed;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt) : self
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
    public function setUpdatedAt($updatedAt) : self
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
     * Set lastDateOfActivity
     *
     * @param \DateTime $lastDateOfActivity
     *
     * @return User
     */
    public function setLastDateOfActivity($lastDateOfActivity) : self
    {
        $this->lastDateOfActivity = $lastDateOfActivity;

        return $this;
    }

    /**
     * Get $lastDateOfActivity
     *
     * @return \DateTime
     */
    public function getLastDateOfActivity()
    {
        return $this->lastDateOfActivity;
    }

    /**
     * Get avatar
     *
     * @return \App\Entity\Medium
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @return string
     */
    public function getVisibility() : string
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     * @return User
     */
    public function setVisibility(string $visibility) : self
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Set timezone.
     *
     * @param string $timezone
     *
     * @return User
     */
    public function setTimezone(string $timezone) : self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTimezone() : ?string
    {
        return $this->timezone;
    }
}
