<?php

namespace InteractiveSolutions\HoneycombNewCore\DTO;

use Carbon\Carbon;
use InteractiveSolutions\HoneycombAcl\Models\Acl\Roles;

class HCUserDTO extends BaseDTO
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
     * @var Roles
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
     * @param Roles $roles
     */
    public function __construct(
        string $userId,
        string $email,
        Carbon $activatedAt = null,
        Carbon $last_login = null,
        Carbon $last_visited = null,
        Carbon $last_activity = null,
        Roles $roles
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
    protected function jsonData(): array
    {
        $dto->forget(['email'. 'is_active'])->jsonData();
        $dto->only(['email'. 'is_active'])->jsonData();

        $data = [
            'id' => $this->getUserId(),
            'email' => $this->getEmail(),
            'is_active' => $this->getActivated(),
            'roles' => $this->getRoles(),
        ];

        array_forget($data, $forget);

        return $data;
    }
}