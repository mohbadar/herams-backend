<?php


namespace prime\components;


use prime\assets\PNotifyBundle;
use prime\assets\ToastBundle;
use yii\authclient\AuthAction;
use yii\base\Component;
use yii\base\Event;
use yii\base\ViewEvent;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Session;
use yii\web\View;

class NotificationService extends Component
{
    private $counter = 0;
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        Event::on(View::class, View::EVENT_BEFORE_RENDER, function (ViewEvent $event) {
            $this->renderNotifications($event);
        });
    }

    protected  function getSession(): Session
    {
        return \Yii::$app->get('session');
    }

    protected function renderNotifications(ViewEvent $event)
    {
        /** @var View $view */
        $view = $event->sender;

        if (!$view->context instanceof Controller) {
            return;
        }

        if (\Yii::$app->requestedAction instanceof AuthAction
            || !\Yii::$app->has('session', true)
        ) {
            return;
        }

        ToastBundle::register($view);

        $jsNotifications = [];
        foreach ($this->getSession()->getAllFlashes(true) as $flash) {
            $type = ArrayHelper::remove($flash, 'type', 'show');
            $flash['position'] = 'topRight';

            $config = json_encode($flash);


            $jsNotifications[] = "iziToast.$type($config);";
        }
        if (!empty($jsNotifications)) {
            $view->registerJs(implode("\n", $jsNotifications));
        }
    }

    public function info(string $message, string $title = null, string $key = null)
    {
        $this->getSession()->setFlash(
            $key ?? "Message" . $this->counter++,
            [
                'type' => 'info',
                'message' => $message,
                'title' => $title ?? \Yii::t('app', 'Info'),
            ]
        );
    }

    public function success(string $message, string $title = null, string $key = null)
    {
        $this->getSession()->setFlash(
            $key ?? "Message" . $this->counter++,
            [
                'type' => 'success',
                'message' => $message,
                'title' => $title ?? \Yii::t('app', 'Success!'),
            ]
        );
    }

    public function error(string $message, string $title = null, string $key = null)
    {
        $this->getSession()->setFlash(
            $key ?? "Message" . $this->counter++,
            [
                'type' => 'error',
                'message' => $message,
                'title' => $title ?? \Yii::t('app', 'Error!'),
            ]
        );
    }

    public function warn(string $message, string $title = null, string $key = null)
    {
        $this->getSession()->setFlash(
            $key ?? "Message" . $this->counter++,
            [
                'type' => 'warning',
                'message' => $message,
                'title' => $title ?? \Yii::t('app', 'Warning!'),
            ]
        );
    }
}