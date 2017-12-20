<?php

namespace InteractiveSolutions\HoneycombNewCore\DTO;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use InteractiveSolutions\HoneycombNewCore\Models\Acl\HCRoles;

class HCUserDTO extends HCBaseDTO
{
    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $email;

    /**
     * @var Carbon|null
     */
    private $activatedAt;

    /**
     * @var Carbon|null
     */
    private $lastLogin;

    /**
     * @var Carbon|null
     */
    private $lastVisited;

    /**
     * @var Carbon|null
     */
    private $lastActivity;

    /**
     * @var Collection
     */
    private $roles;

    /**
     * HCUserDTO constructor.
     *
     * @param string $userId
     * @param string $email
     * @param Carbon|null $activatedAt
     * @param Carbon|null $lastLogin
     * @param Carbon|null $lastVisited
     * @param Carbon|null $lastActivity
     * @param Collection|null $roles
     */
    public function __construct(
        string $userId,
        string $email,
        Carbon $activatedAt = null,
        Carbon $lastLogin = null,
        Carbon $lastVisited = null,
        Carbon $lastActivity = null,
        Collection $roles = null
    ) {
        $this->userId = $userId;
        $this->email = $email;
        $this->activatedAt = $activatedAt;
        $this->lastLogin = $lastLogin;
        $this->lastVisited = $lastVisited;
        $this->lastActivity = $lastActivity;
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return array
     */
    public function getActivated(): array
    {
        if ($this->activatedAt) {
            return [
                ['id' => true],
            ];
        }

        return [
            ['id' => false],
        ];
    }

    /**
     * @return Carbon|null
     */
    public function getActivatedAt(): ? string
    {
        if ($this->activatedAt) {
            return $this->activatedAt->toDateTimeString();
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getLastLogin(): ? string
    {
        if ($this->lastLogin) {
            return $this->lastLogin->toDateTimeString();
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getLastVisited(): ? string
    {
        if ($this->lastVisited) {
            return $this->lastVisited->toDateTimeString();
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getLastActivity(): ? string
    {
        if ($this->lastActivity) {
            return $this->lastActivity->toDateTimeString();
        }

        return null;
    }

    /**
     * @return Collection|HCRoles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return array
     */
    protected function jsonData(): array
    {
        $data = [
            'id' => $this->getUserId(),
            'activated_at' => $this->getActivatedAt(),
            'last_login' => $this->getLastLogin(),
            'last_visited' => $this->getLastVisited(),
            'last_activity' => $this->getLastActivity(),
            'email' => $this->getEmail(),
            'is_active' => $this->getActivated(),
            'roles' => $this->getRoles(),
        ];

        return $data;
    }
}
