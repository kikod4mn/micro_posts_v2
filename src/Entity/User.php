<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\HasSlug;
use App\Entity\Concerns\HasTimestamps;
use App\Entity\Concerns\IsTrashable;
use App\Entity\Concerns\HasUuid;
use App\Entity\Concerns\UserConcerns\HasBlogPosts;
use App\Entity\Concerns\UserConcerns\HasMicroPosts;
use App\Entity\Contracts\Sluggable;
use App\Entity\Contracts\TimeStampable;
use App\Entity\Contracts\Trashable;
use App\Entity\Contracts\Uniqable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields="uuid", message="How did this happen???? Uuid should be unique!!")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="This e-mail is already in use")
 * @UniqueEntity(fields="username", message="This username is already in use")
 */
class User extends AbstractEntity implements UserInterface, TimeStampable, Trashable, Uniqable, Sluggable
{
	use HasUuid, HasTimestamps, IsTrashable, HasSlug, HasBlogPosts, HasMicroPosts;
	
	/**
	 * @var string
	 */
	const SLUGGABLE_FIELD = 'fullname';
	
	/** @var string */
	public const ROLE_USER = 'ROLE_USER';
	
	/** @var string */
	public const ROLE_MODERATOR = 'ROLE_MODERATOR';
	
	/** @var string */
	public const ROLE_ADMINISTRATOR = 'ROLE_ADMINISTRATOR';
	
	/** @var string */
	public const ROLE_SUPER_ADMINISTRATOR = 'ROLE_SUPER_ADMINISTRATOR';
	
	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 * @Assert\NotBlank()
	 * @var string
	 */
	protected $username;
	
	/**
	 * @ORM\Column(type="string", nullable=false)
	 * @var string
	 */
	protected $password;
	
	/**
	 * @Assert\NotBlank()
	 * @var string
	 */
	protected $plainPassword;
	
	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 * @Assert\NotBlank()
	 * @Assert\Email()
	 * @var string
	 */
	protected $email;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank()
	 * @var string
	 */
	protected $fullname;
	
	/**
	 * @ORM\Column(type="string", nullable=false)
	 * @var string
	 */
	protected $role = self::ROLE_USER;
	
	/**
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="following")
	 * @var User[]|Collection
	 */
	protected $followers;
	
	/**
	 * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
	 * @ORM\JoinTable(name="following", joinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *      },
	 *      inverseJoinColumns={
	 *          @ORM\JoinColumn(name="following_user_id", referencedColumnName="id")
	 *      }
	 * )
	 * @var User[]|Collection
	 */
	protected $following;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="author")
	 * @var Notification[]|Collection
	 */
	protected $notifications;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Picture", mappedBy="author")
	 * @var Picture[]|Collection
	 */
	protected $pictures;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Gallery", mappedBy="author")
	 * @var Gallery[]|Collection
	 */
	protected $galleries;
	
	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 * @var bool
	 */
	protected $activated = false;
	
	/**
	 * @ORM\Column(type="string", nullable=true, length=64)
	 * @var string
	 */
	protected $accountConfirmationToken;
	
	/**
	 * @ORM\Column(type="string", nullable=true, length=64)
	 * @var string
	 */
	protected $passwordResetToken;
	
	/**
	 * @Groups({"administer"})
	 * @ORM\Column(type="boolean", nullable=false)
	 * @var boolean
	 */
	protected $forcedPasswordChange = false;
	
	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\UserPreferences", inversedBy="user", cascade={"all"})
	 * @var UserPreferences|Collection
	 */
	protected $preferences;
	
	/**
	 * @Groups({"administer", "user-with-posts", "user-with-comments", "user-with-followers", "user-with-following"})
	 * @ORM\OneToOne(targetEntity="App\Entity\UserProfile", inversedBy="user", cascade={"all"})
	 * @var UserProfile|Collection
	 */
	protected $profile;
	
	/**
	 * User constructor.
	 */
	public function __construct()
	{
		$this->blogPosts            = new ArrayCollection();
		$this->blogPostsLiked       = new ArrayCollection();
		$this->blogCommentsLiked    = new ArrayCollection();
		$this->reportedBlogPosts    = new ArrayCollection();
		$this->reportedBlogComments = new ArrayCollection();
		
		$this->microPosts            = new ArrayCollection();
		$this->microPostsLiked       = new ArrayCollection();
		$this->microCommentsLiked    = new ArrayCollection();
		$this->reportedMicroPosts    = new ArrayCollection();
		$this->reportedMicroComments = new ArrayCollection();
		
		$this->notifications = new ArrayCollection();
		$this->pictures      = new ArrayCollection();
		$this->galleries     = new ArrayCollection();
		$this->followers     = new ArrayCollection();
		$this->following     = new ArrayCollection();
	}
	
	/**
	 * @return string
	 */
	public function getUsername(): ?string
	{
		return $this->username;
	}
	
	/**
	 * @param  string  $username
	 * @return User
	 */
	public function setUsername(string $username): self
	{
		$this->username = $this->cleanString($username);
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getPassword(): ?string
	{
		return $this->password;
	}
	
	/**
	 * @param  string  $password
	 * @return User
	 */
	public function setPassword(string $password): self
	{
		$this->password = $password;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getPlainPassword(): ?string
	{
		return $this->plainPassword;
	}
	
	/**
	 * @param  string  $plainPassword
	 * @return User
	 */
	public function setPlainPassword(string $plainPassword): self
	{
		$this->plainPassword = $plainPassword;
		
		return $this;
	}
	
	/**
	 * Use password encoder default salting technique. Return null here.
	 * @return null|string
	 */
	public function getSalt(): ?string
	{
		return null;
	}
	
	/**
	 * @return string
	 */
	public function getEmail(): ?string
	{
		return $this->email;
	}
	
	/**
	 * @param  string  $email
	 * @return User
	 */
	public function setEmail(string $email): self
	{
		$this->email = $this->cleanString($email);
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getFullname(): ?string
	{
		return $this->fullname;
	}
	
	/**
	 * @param  string  $fullname
	 * @return User
	 */
	public function setFullname(string $fullname): self
	{
		$this->fullname = $this->cleanString($fullname);
		
		return $this;
	}
	
	/**
	 * UserInterface function
	 * @return null|array
	 */
	public function getRoles(): ?array
	{
		return [$this->role];
	}
	
	/**
	 * @return string
	 */
	public function getRole(): ?string
	{
		return $this->role;
	}
	
	/**
	 * @param  string  $role
	 * @return User
	 */
	public function setRole(string $role): self
	{
		$this->role = $role;
		
		return $this;
	}
	
	/**
	 * @return null|Collection|User[]
	 */
	public function getFollowers(): ?Collection
	{
		return $this->followers;
	}
	
	/**
	 * @return null|Collection|User[]
	 */
	public function getFollowing(): ?Collection
	{
		return $this->following;
	}
	
	/**
	 * Follow the user if already not following.
	 * @param  User  $userToFollow
	 * @return User
	 */
	public function follow(User $userToFollow): self
	{
		if ($this->following->contains($userToFollow)) {
			
			return $this;
		}
		$this->following->add($userToFollow);
		
		return $this;
	}
	
	/**
	 * Un-follow the user if following.
	 * @param  User  $userToUnFollow
	 * @return User
	 */
	public function unFollow(User $userToUnFollow): self
	{
		if ($this->following->contains($userToUnFollow)) {
			$this->following->removeElement($userToUnFollow);
			
			return $this;
		}
		
		return $this;
	}
	
	/**
	 * @return null|Collection|Notification[]
	 */
	public function getNotifications(): ?Collection
	{
		return $this->notifications;
	}
	
	/**
	 * @return null|bool
	 */
	public function isActivated(): bool
	{
		return $this->activated;
	}
	
	/**
	 * Activate a user manually.
	 */
	public function activate(): void
	{
		$this->activated = true;
	}
	
	/**
	 * Deactivate a user manually.
	 */
	public function deActivate(): void
	{
		$this->activated = false;
	}
	
	/**
	 * @return string
	 */
	public function getAccountConfirmationToken(): ?string
	{
		return $this->accountConfirmationToken;
	}
	
	/**
	 * @param  null|string  $accountConfirmationToken
	 * @return User
	 */
	public function setAccountConfirmationToken(?string $accountConfirmationToken): self
	{
		$this->accountConfirmationToken = $accountConfirmationToken;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getPasswordResetToken(): ?string
	{
		return $this->passwordResetToken;
	}
	
	/**
	 * @param  string  $passwordResetToken
	 * @return User
	 */
	public function setPasswordResetToken(?string $passwordResetToken): self
	{
		$this->passwordResetToken = $passwordResetToken;
		
		return $this;
	}
	
	/**
	 * @return null|bool
	 */
	public function getForcedPasswordChange(): ?bool
	{
		return $this->forcedPasswordChange;
	}
	
	/**
	 * @param  bool  $forcedPasswordChange
	 * @return User
	 */
	public function setForcedPasswordChange(bool $forcedPasswordChange): self
	{
		$this->forcedPasswordChange = $forcedPasswordChange;
		
		return $this;
	}
	
	/**
	 * @return null|Collection|UserPreferences
	 */
	public function getPreferences(): ?Collection
	{
		return $this->preferences;
	}
	
	/**
	 * @param  UserPreferences  $preferences
	 * @return User
	 */
	public function setPreferences(UserPreferences $preferences): self
	{
		$this->preferences = $preferences;
		
		return $this;
	}
	
	/**
	 * @return null|Collection|UserProfile
	 */
	public function getProfile(): ?Collection
	{
		return $this->profile;
	}
	
	/**
	 * @param  UserProfile  $profile
	 * @return User
	 */
	public function setProfile(UserProfile $profile): self
	{
		$this->profile = $profile;
		
		return $this;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function eraseCredentials(): void
	{
		$this->plainPassword = '';
	}
}
