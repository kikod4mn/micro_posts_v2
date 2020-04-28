<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserPreferencesRepository")
 */
class UserPreferences
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="bigint")
	 */
	protected $id;
	
	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="preferences")
	 * @var User
	 */
	protected $user;
	
	/**
	 * @ORM\Column(type="string", length=4)
	 * @Assert\Length(min="2", max="2")
	 */
	protected $locale;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 * @var bool
	 */
	protected $shouldReceiveEmailOnNewMessage;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 * @var bool
	 */
	protected $shouldReceiveEmailOnNewComments;
	
	/**
	 * @return null|int
	 */
	public function getId(): ?int
	{
		return $this->id;
	}
	
	/**
	 * @return null|User
	 */
	public function getUser(): ?User
	{
		return $this->user;
	}
	
	/**
	 * @param  User  $user
	 * @return UserPreferences
	 */
	public function setUser(User $user): self
	{
		$this->user = $user;
		
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getLocale(): ?string
	{
		return $this->locale;
	}
	
	/**
	 * @param  string  $locale
	 * @return UserPreferences
	 */
	public function setLocale(string $locale): self
	{
		$this->locale = $locale;
		
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function shouldReceiveEmailOnNewMessage(): bool
	{
		return $this->shouldReceiveEmailOnNewMessage ?? false;
	}
	
	/**
	 * @param  bool  $shouldReceiveEmailOnNewMessage
	 * @return UserPreferences
	 */
	public function allowEmailOnNewMessage(bool $shouldReceiveEmailOnNewMessage): self
	{
		$this->shouldReceiveEmailOnNewMessage = $shouldReceiveEmailOnNewMessage;
		
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function shouldReceiveEmailOnNewComments(): bool
	{
		return $this->shouldReceiveEmailOnNewComments ?? false;
	}
	
	/**
	 * @param  bool  $shouldReceiveEmailOnNewComments
	 * @return UserPreferences
	 */
	public function setShouldReceiveEmailOnNewComments(bool $shouldReceiveEmailOnNewComments): self
	{
		$this->shouldReceiveEmailOnNewComments = $shouldReceiveEmailOnNewComments;
		
		return $this;
	}
}
