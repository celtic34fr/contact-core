<?php

namespace Celtic34fr\ContactCore\MySQLiEntity;

use Bolt\Enum\UserStatus;

class MSIUser
{
    private int $id;
    private string $displayName;
    private string $username;
    private string $email;
    private string $password;
    private string|null $plainPassword;
    private array $roles = [];
    private string $lastseenAt; // datetime
    private string $lastIp;
    private string $locale;
    private string $backendTheme;
    private string $status = UserStatus::ENABLED;
    private array $userAuthTokens; // UserAuthToken[]
    private string $avatar;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getLastseenAt(): string
    {
        return $this->lastseenAt;
    }

    /**
     * @param string $lastseenAt
     */
    public function setLastseenAt(string $lastseenAt): void
    {
        $this->lastseenAt = $lastseenAt;
    }

    /**
     * @return string
     */
    public function getLastIp(): string
    {
        return $this->lastIp;
    }

    /**
     * @param string $lastIp
     */
    public function setLastIp(string $lastIp): void
    {
        $this->lastIp = $lastIp;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getBackendTheme(): string
    {
        return $this->backendTheme;
    }

    /**
     * @param string $backendTheme
     */
    public function setBackendTheme(string $backendTheme): void
    {
        $this->backendTheme = $backendTheme;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getUserAuthTokens(): array
    {
        return $this->userAuthTokens;
    }

    /**
     * @param array $userAuthTokens
     */
    public function setUserAuthTokens(array $userAuthTokens): void
    {
        $this->userAuthTokens = $userAuthTokens;
    }

    /**
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }

}
