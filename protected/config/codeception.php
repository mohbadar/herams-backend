<?php

use yii\web\Application;

$config = require 'web.php';
$config['components']['limeSurvey'] = \prime\tests\_helpers\LimesurveyStub::class;
return $config;