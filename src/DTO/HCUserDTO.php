<?php

namespace InteractiveSolutions\HoneycombNewCore\DTO;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use InteractiveSolutions\HoneycombNewCore\Models\Acl\HCRoles;

class HCUserDTO extends HCBaseDTO
{
    private $userId;
    private $email;
    private $activatedAt;
    /**
     * @var Carbon
     */
    private $last_login;
    /**
     * @var Carbon
     */
    private $last_visited;
    /**
     * @var Carbon
     */
    private $last_activity;

    /**
     * @var HCRoles
     */
    private $roles;

    /**
     * HCUserDTO constructor.
     *
     * @param string $userId
     * @param string $email
     * @param Carbon|null $activatedAt
     * @param Carbon|null $last_login
     * @param Carbon|null $last_visited
     * @param Carbon|null $last_activity
     * @param Collection $roles
     */
    public function __construct(
        string $userId,
        string $email,
        Carbon $activatedAt = null,
        Carbon $last_login = null,
        Carbon $last_visited = null,
        Carbon $last_activity = null,
        Collection $roles
    ) {
        $this->userId = $userId;
        $this->email = $email;
        $this->activatedAt = $activatedAt;
        $this->last_login = $last_login;
        $this->last_visited = $last_visited;
        $this->last_activity = $last_activity;
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getActivated(): array
    {
        if ($this->activatedAt) {
            return ['id' => true];
        }

        return ['id' => false];
    }

    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return array
     */
    public function jsonData(): array
    {
        $data = [
            'id' => $this->getUserId(),
            'email' => $this->getEmail(),
            'is_active' => $this->getActivated(),
            'roles' => $this->getRoles(),
        ];

        return $data;
    }
}