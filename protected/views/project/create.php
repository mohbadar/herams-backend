<?php
declare(strict_types=1);

use app\components\ActiveForm;
use app\components\Form;
use kartik\select2\Select2;
use prime\components\View;
use prime\models\ar\Project;
use prime\models\forms\project\Create;
use prime\objects\enums\ProjectVisibility;
use prime\widgets\FormButtonsWidget;
use prime\widgets\Section;

/**
 * @var View $this
 * @var Create $model
 */

$this->params['breadcrumbs'][] = [
    'label' => \Yii::t('app', 'Admin dashboard'),
    'url' => ['/admin']
];
$this->params['breadcrumbs'][] = [
    'label' => \Yii::t('app', 'Projects'),
    'url' => ['/project']
];
$this->title = Yii::t('app', 'Create project');

Section::begin()
    ->withHeader($this->title);

$form = ActiveForm::begin([
    "type" => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => [
        'showLabels' => true,
        'defaultPlaceholder' => false,
        'labelSpan' => 3
    ]
]);
echo Form::widget([
    'form' => $form,
    'model' => $model,
    "attributes" => [
        'title' => [
            'type' => Form::INPUT_TEXT,
        ],
        'base_survey_eid' => [
            'type' => Form::INPUT_WIDGET,
            'widgetClass' => Select2::class,
            'options' => [
                'data' => $model->dataSurveyOptions(),
            ],
        ],
        'visibility' => [
            'type' => Form::INPUT_DROPDOWN_LIST,
            'items' => ProjectVisibility::toArray()
        ],
        FormButtonsWidget::embed([
            'buttons' =>  [
                ['label' => \Yii::t('app', 'Create project'), 'style' => 'primary'],
            ]
        ])
    ],
]);
$form->end();

Section::end();
