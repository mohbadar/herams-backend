<?php


namespace prime\controllers\workspace;


use prime\components\NotificationService;
use prime\exceptions\NoGrantablePermissions;
use prime\models\ar\Workspace;
use prime\models\forms\Share as ShareForm;
use prime\models\permissions\Permission;
use SamIT\abac\AuthManager;
use yii\base\Action;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\User;

class Share extends Action
{
    public function run(
        NotificationService $notificationService,
        Request $request,
        AuthManager $abacManager,
        User $user,
        int $id
    )
    {
        $workspace = Workspace::findOne(['id' => $id]);
        if (!isset($workspace)) {
            throw new NotFoundHttpException();
        }
        if (!($user->can(Permission::PERMISSION_SHARE, $workspace))) {
            throw new ForbiddenHttpException('You are not allowed to share this workspace');
        }
        try {
            $model = new ShareForm($workspace, $abacManager, $user->identity, [
                Permission::PERMISSION_LIMESURVEY,
                Permission::PERMISSION_EXPORT,
                Permission::PERMISSION_SHARE,
//                Permission::ROLE_WORKSPACE_CONTRIBUTOR,
//                Permission::ROLE_WORKSPACE_OWNER,
                Permission::PERMISSION_ADMIN,
            ]);
        } catch (NoGrantablePermissions $e) {
            $notificationService->error('There are no permissions that you can share for this workspace');
            return $this->controller->redirect($request->getReferrer());
        }

        if ($request->isPost && $model->load($request->bodyParams) && $model->validate()) {
            $model->createRecords();
            $notificationService->success(\Yii::t('app',
                "Workspace <strong>{modelName}</strong> has been shared with: <strong>{users}</strong>",
                [
                    'modelName' => $workspace->title,
                    'users' => implode(', ', array_map(function ($model) {
                        return $model->name;
                    }, $model->getUsers()->all()))
                ])

            );
            return $this->controller->refresh();
        }

        return $this->controller->render('share', [
            'model' => $model,
            'workspace' => $workspace
        ]);
    }
}