<?php
declare(strict_types=1);

namespace prime\controllers;

use prime\components\Controller;
use prime\controllers\survey\AjaxSave;
use prime\controllers\survey\Create;
use prime\controllers\survey\Update;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class SurveyController extends Controller
{
    public $layout = self::LAYOUT_ADMIN_TABS;

    public function actions(): array
    {
        return [
            'ajax-save' => AjaxSave::class,
            'create' => Create::class,
            'update' => Update::class,
        ];
    }

    public function behaviors(): array
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'verb' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'ajax-save' => ['post'],
                        'create' => ['get'],
                        'update' => ['get'],
                    ]
                ],
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ]
                ],
            ]
        );
    }
}
