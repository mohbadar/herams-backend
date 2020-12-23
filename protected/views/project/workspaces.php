<?php

/**
 * @var \yii\data\ActiveDataProvider $workspacesDataProvider
 * @var \prime\models\search\Workspace $workspaceSearch
 * @var int $closedCount
 * @var \yii\web\View $this
 * @var \prime\models\ar\Project $project
 *
 */

use kartik\grid\GridView;
use prime\models\ar\Permission;
use prime\widgets\FavoriteColumn\FavoriteColumn;
use prime\widgets\menu\TabMenu;
use prime\helpers\Icon;
use yii\helpers\Url;
use yii\helpers\Html;

$this->params['breadcrumbs'][] = [
    'label' => $project->title,
    'url' => ['project/workspaces', 'id' => $project->id]
];
$this->title = $project->title;


echo Html::beginTag('div', ['class' => "main layout-{$this->context->layout} controller-{$this->context->id} action-{$this->context->action->id}"]);

$tabs = [
    [
        'url' => ['project/workspaces', 'id' => $project->id],
        'title' => \Yii::t('app', 'Workspaces') . " ({$project->workspaceCount})"
    ]
];

if (\Yii::$app->user->can(Permission::PERMISSION_ADMIN, $project)) {
    $tabs[] =
        [
            'url' => ['project/pages', 'id' => $project->id],
            'title' => \Yii::t('app', 'Dashboard settings')
        ];
    $tabs[] =
        [
            'url' => ['project/update', 'id' => $project->id],
            'title' => \Yii::t('app', 'Project settings')
        ];
}
if (\Yii::$app->user->can(Permission::PERMISSION_SHARE, $project)) {
    $tabs[] =
        [
            'url' => ['project/share', 'id' => $project->id],
            'title' => \Yii::t('app', 'Users') . " ({$project->contributorCount})"
        ];
}
if (\Yii::$app->user->can(Permission::PERMISSION_SURVEY_BACKEND, $project)) {
    $tabs[] =
        [
            'url' => ['/admin/limesurvey'],
            'title' => \Yii::t('app', 'Backend administration')
        ];
}

echo TabMenu::widget([
    'tabs' => $tabs,
    'currentPage' => $this->context->action->uniqueId
]);



echo Html::beginTag('div', ['class' => "content"]);

echo Html::beginTag('div', ['class' => 'action-group']);

if (app()->user->can(Permission::PERMISSION_MANAGE_WORKSPACES, $project)) {
    echo Html::a(Icon::add() . \Yii::t('app', 'Create workspace'), Url::to(['workspace/create', 'project_id' => $project->id]), ['class' => 'btn btn-primary btn-icon']);
    echo Html::a(Icon::up_arrow_1() . \Yii::t('app', 'Import workspaces'), Url::to(['workspace/import', 'project_id' => $project->id]), ['class' => 'btn btn-default btn-icon']);
}
if (app()->user->can(Permission::PERMISSION_EXPORT, $project)) {
    echo Html::a(Icon::download_2() . \Yii::t('app', 'Download'), Url::to(['project/export', 'project_id' => $project->id]), ['class' => 'btn btn-default btn-icon']);
}

echo Html::endTag('div');

echo GridView::widget([
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            // Just links in the header.
            'linkSelector' => 'th a'
        ]
    ],
    //'layout' => "{items}\n{pager}",
    'filterModel' => $workspaceSearch,
    'dataProvider' => $workspaceProvider,
    'columns' => [
        [
            'class' => FavoriteColumn::class
        ],
        [
            'attribute' => 'id',
        ],
        [
            'label' => 'title',
            'attribute' => 'title',
            'content' => function ($workspace) {
                return (\Yii::$app->user->can(Permission::PERMISSION_SURVEY_DATA, $workspace)) ?
                    Html::a(
                        $workspace->title,
                        ['workspace/limesurvey', 'id' => $workspace->id],
                        [
                            'title' => $workspace->title,
                        ]
                    ) : $workspace->title;
            }
        ],
        [
            'attribute' => 'latestUpdate',
        ],
        [
            'attribute' => 'contributorCount'
        ],
        [
            'attribute' => 'facilityCount',
        ],
        [
            'attribute' => 'responseCount'
        ],
        'actions' => require('workspaces/actions.php')
    ]
]);

echo Html::endTag('div');
echo Html::endTag('div');
