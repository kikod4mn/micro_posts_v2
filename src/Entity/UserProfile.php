<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserProfileRepository")
 */
class UserProfile
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="bigint")
	 */
	protected $id;
	
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
	 * @return null|int
	 */
	public function getId(): ?int
	{
		return $this->id;
	}
	
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
		$this->bio = $bio;
		
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
