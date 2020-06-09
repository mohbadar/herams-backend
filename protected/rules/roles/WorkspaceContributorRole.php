<?php
declare(strict_types=1);

namespace prime\rules\roles;


use prime\models\ar\Permission;
use prime\models\ar\User;
use prime\models\ar\Workspace;
use SamIT\abac\interfaces\AccessChecker;
use SamIT\abac\interfaces\Environment;
use SamIT\abac\interfaces\Rule;

class WorkspaceContributorRole implements Rule
{

    /**
     * @inheritDoc
     */
    public function getPermissions(): array
    {
        return [
            Permission::PERMISSION_LIMESURVEY,
            Permission::PERMISSION_EXPORT
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTargetNames(): array
    {
        return [Workspace::class];
    }

    /**
     * @inheritDoc
     */
    public function getSourceNames(): array
    {
        return [User::class];
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'have the correct role';
    }

    /**
     * @inheritDoc
     */
    public function execute(
        object $source,
        object $target,
        string $permission,
        Environment $environment,
        AccessChecker $accessChecker
    ): bool {
        return in_array(get_class($target), $this->getTargetNames())
            && in_array(get_class($source), $this->getSourceNames())
            && in_array($permission, $this->getPermissions())
            && $accessChecker->check($source, $target, Permission::ROLE_WORKSPACE_CONTRIBUTOR);
    }
}