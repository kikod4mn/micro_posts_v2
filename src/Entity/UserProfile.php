<?php

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\HasUuid;
use App\Entity\Contracts\Uniqable;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="`user_profile`")
 * @UniqueEntity(fields="uuid", message="How did this happen???? Uuid should be unique!!")
 * @ORM\Entity(repositoryClass="App\Repository\UserProfileRepository")
 */
class UserProfile extends AbstractEntity implements Uniqable
{
	use HasUuid;
	
	/**
	 * @ORM\OneToOne(targetEntity="User", mappedBy="profile")
	 * @var User
	 */
	protected $user;
	
	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var null|string
	 */
	protected $avatar;
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var null|string
	 */
	protected $bio;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 * @var DateTime
	 */
	protected $birthday;
	
	/**
	 * @return User
	 */
	public function getUser(): ?User
	{
		return $this->user;
	}
	
	/**
	 * @param  User  $user
	 * @return UserProfile
	 */
	public function setUser(User $user): self
	{
		$this->user = $user;
		
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getAvatar(): ?string
	{
		return $this->avatar;
	}
	
	/**
	 * @param  string  $avatar
	 * @return UserProfile
	 */
	public function setAvatar(string $avatar): self
	{
		$this->avatar = $avatar;
		
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getBio(): ?string
	{
		return $this->bio;
	}
	
	/**
	 * @param  string  $bio
	 * @return UserProfile
	 */
	public function setBio(string $bio): self
	{
		$this->bio = $this->cleanString($bio);
		
		return $this;
	}
	
	/**
	 * @return null|DateTime
	 */
	public function getBirthday(): ?DateTime
	{
		return $this->birthday;
	}
	
	/**
	 * @param  DateTime  $birthday
	 * @return UserProfile
	 */
	public function setBirthday(DateTime $birthday): self
	{
		$this->birthday = $birthday;
		
		return $this;
	}
}
