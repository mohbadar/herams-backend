<?php

use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use prime\helpers\Icon;
use prime\models\ar\Permission;
use prime\models\ar\Project;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\Html;
use yii\helpers\Url;
use prime\widgets\menu\TabMenu;

$this->params['breadcrumbs'][] = [
    'label' => \Yii::t('app', 'Projects'),
    'url' => ['/project/index']
];
$this->title = \Yii::t('app', 'Projects');
//$this->params['breadcrumbs'][] = ['label' => $this->title];
/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $projectProvider
 */

echo Html::beginTag('div', ['class' => "main layout-{$this->context->layout} controller-{$this->context->id} action-{$this->context->action->id}"]);


echo Html::beginTag('div', ['class' => "content no-tab"]);

if (app()->user->can(Permission::PERMISSION_ADMIN)) {
    echo Html::beginTag('div', ['class' => 'action-group']);
    echo Html::a(Icon::download() . \Yii::t('app', 'Create project'), Url::to(['project/create']), ['class' => 'btn btn-primary btn-icon']);
    echo Html::endTag('div');
}

echo GridView::widget([
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            // Just links in the header.
            'linkSelector' => 'th a'
        ]
    ],
    'dataProvider' => $projectProvider,
    'filterModel' => $projectSearch,
    //'layout' => "{items}\n{pager}",
    'columns' => [
        'id',
        'title',
        [
            'label' => \Yii::t('app', '# Workspaces'),
            'attribute'  => 'workspaceCount',
        ],
        [
            'label' => \Yii::t('app', '# Contributors'),
            'attribute' => 'contributorCount'
        ],
        [
            'label' => \Yii::t('app', '# Health facilities'),
            'attribute' => 'facilityCount'
        ],
        [
            'label' => \Yii::t('app', '# Responses'),
            'attribute' => 'responseCount'
        ],
        'actions' => [
            'class' => ActionColumn::class,
            'width' => 6 * 25 . 'px',
            'template' => '{view} {workspaces} {update} {pages} {share} {remove} {export}',
            'visibleButtons' => [
                'pages' => function (Project $project) {
                    return app()->user->can(Permission::PERMISSION_MANAGE_DASHBOARD, $project);
                },
                'view' => function (Project $project) {
                    return $project->pageCount > 0 && app()->user->can(Permission::PERMISSION_READ, $project);
                },
                'update' => function (Project $project) {
                    return app()->user->can(Permission::PERMISSION_WRITE, $project);
                },
                'share' => function (Project $project) {
                    return app()->user->can(Permission::PERMISSION_SHARE, $project);
                },
                'remove' => function (Project $project) {
                    return app()->user->can(Permission::PERMISSION_DELETE, $project);
                },
                'export' => function (Project $project) {
                    return app()->user->can(Permission::PERMISSION_EXPORT, $project);
                },
            ],
            'buttons' => [
                'workspaces' => function ($url, Project $model, $key) {
                    return Html::a(
                        Icon::list(),
                        ['project/workspaces', 'id' => $model->id],
                        ['title' => \Yii::t('app', 'Workspaces')]
                    );
                },
                'view' => function ($url, Project $model, $key) {
                    return Html::a(
                        Icon::project(),
                        ['project/view', 'id' => $model->id],
                        ['title' => \Yii::t('app', 'Project dashboard')]
                    );
                },
                'pages' => function ($url, Project $model, $key) {
                    return Html::a(
                        Icon::paintBrush(),
                        ['project/pages', 'id' => $model->id],
                        ['title' => \Yii::t('app', 'Edit dashboard')]
                    );
                },
                'update' => function ($url, Project $model, $key) {
                    return Html::a(
                        Icon::edit(),
                        ['project/update', 'id' => $model->id],
                        [
                            'title' => \Yii::t('app', 'Edit')
                        ]
                    );
                },
                'share' => function ($url, Project $model, $key) {
                    return Html::a(
                        Icon::share(),
                        $url,
                        [
                            'title' => \Yii::t('app', 'Share')
                        ]
                    );
                },
                'remove' => function ($url, Project $model, $key) {
                    return Html::a(
                        Icon::delete(),
                        ['project/delete', 'id' => $model->id],
                        [
                            'data-method' => 'delete',
                            'title' => \Yii::t('app', 'Delete'),
                            'data-confirm' => \Yii::t('app', 'Are you sure you wish to remove this project from the system?')
                        ]
                    );
                },
                'export' => function ($url, Project $model, $key) {
                    return Html::a(
                        Icon::download(),
                        $url,
                        [
                            'title' => \Yii::t('app', 'Download'),
                        ]
                    );
                }
            ]
        ]
    ]
]);

echo Html::endTag('div');
echo Html::endTag('div');
