<?php
declare(strict_types=1);

/**
 * @var \prime\models\ar\Project $project
 * @var \prime\components\View $this
 */

use app\components\ActiveForm;
use app\components\Form;
use League\ISO3166\ISO3166;
use prime\helpers\Icon;
use prime\models\ar\Permission;
use prime\widgets\FormButtonsWidget;
use prime\widgets\menu\ProjectTabMenu;
use prime\widgets\Section;
use yii\helpers\Html;
use function iter\chain;
use function iter\func\nested_index;
use function iter\map;
use function iter\toArrayWithKeys;

$this->title = $project->title;

$this->beginBlock('tabs');
echo ProjectTabMenu::widget([
    'project' => $project,
]);
$this->endBlock();


Section::begin([
    'subject' => $project,
    'header' => \Yii::t('app', 'Project settings'),

]);
/** @var ActiveForm $form */
$form = ActiveForm::begin([
    'method' => 'PUT',
    "type" => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => [
        'showLabels' => true,
        'defaultPlaceholder' => false,
        'labelSpan' => 3
    ]
]);

echo Form::widget([
    'form' => $form,
    'model' => $project,
    "attributes" => [
        'title' => [
            'type' => Form::INPUT_TEXT,
        ],
        'latitude' => [
            'type' => Form::INPUT_TEXT
        ],
        'longitude' => [
            'type' => Form::INPUT_TEXT
        ],
        'status' => [
            'type' => Form::INPUT_DROPDOWN_LIST,
            'items' => $project->statusOptions()
        ],
        'visibility' => [
            'type' => Form::INPUT_DROPDOWN_LIST,
            'items' => $project->visibilityOptions()
        ],
        'country' => [
            'type' => Form::INPUT_WIDGET,
            'widgetClass' => \kartik\select2\Select2::class,
            'options' => [
                'data' => toArrayWithKeys(chain(
                    ['' => \Yii::t('app', '(Not set)')],
                    map(nested_index('name'), (new ISO3166())->iterator(ISO3166::KEY_ALPHA3))
                )),

            ]
        ],
        'typemapAsJson' => [
            'type' => Form::INPUT_TEXTAREA,
            'options' => [
                'rows' => 10
            ]
        ],
        'overridesAsJson' => [
            'type' => Form::INPUT_TEXTAREA,
            'options' => [
                'rows' => 10
            ]
        ],
        FormButtonsWidget::embed([
            'buttons' => [
                [
                    'label' => \Yii::t('app', 'Update project'),
                    'options' => ['class' => 'btn btn-primary'],
                ]
            ]
        ])
    ]
]);
$form->end();
Section::end();
Section::begin()
    ->withHeader(\Yii::t('app', 'Delete project'))
    ->forDangerousAction()
;


echo Html::tag('p', \Yii::t('app', 'This will permanently delete the project and all its workspaces.'));
echo Html::tag('p', \Yii::t('app', 'This action cannot be undone.'));
echo Html::tag('p', Html::tag('em', \Yii::t('app', 'Are you ABSOLUTELY SURE you wish to delete this project?')));

echo \prime\widgets\ButtonGroup::widget([
    'buttons' => [
        [
            'visible' => \Yii::$app->user->can(Permission::PERMISSION_DELETE, $project),
            'icon' => Icon::trash(),
            'label' => \Yii::t('app', 'Delete'),
            'link' => ['project/delete', 'id' => $project->id],
            'style' => 'delete',
            'linkOptions' => [
                'data-method' => 'delete',
                'title' => \Yii::t('app', 'Delete project'),
                'data-confirm' => \Yii::t('app', 'Are you sure you wish to remove this project from the system?'),
            ]
        ]
    ]
]);

Section::end();
Section::begin()
    ->withHeader(\Yii::t('app', 'Empty project'))
    ->forDangerousAction()
;


echo Html::tag('p', \Yii::t('app', 'This will permanently delete all workspaces in the project.'));
echo Html::tag('p', \Yii::t('app', 'This action cannot be undone.'));
echo Html::tag('p', Html::tag('em', \Yii::t('app', 'Are you ABSOLUTELY SURE you wish to delete all workspaces?')));

echo \prime\widgets\ButtonGroup::widget([
    'buttons' => [
        [
            'visible' => \Yii::$app->user->can(Permission::PERMISSION_DELETE_ALL_WORKSPACES, $project),
            'icon' => Icon::trash(),
            'label' => \Yii::t('app', 'Delete all workspaces'),
            'link' => ['project/delete-workspaces', 'id' => $project->id],
            'style' => 'delete',
            'linkOptions' => [
                'data-method' => 'delete',
                'title' => \Yii::t('app', 'Delete all workspaces in project'),
                'data-confirm' => \Yii::t('app', 'Are you sure you wish to remove the workspaces from the system?'),
            ]
        ]
    ]
]);

Section::end();
