<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Workspace\Workspace;

interface EntityWithWorkspaceInterface
{
    public function getWorkspace(): ?Workspace;
    public function setWorkspace(?Workspace $workspace): self;

}
