<?php


namespace prime\widgets\nestedselect;


use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class NestedSelect extends InputWidget
{
    public $placeholder;
    public $multiple = '(multiple)';
    public $selection;
    public $allowMultiple = true;

    public $items;

    public function init()
    {
        parent::init();
        $options = $this->options;
        Html::addCssClass($options, 'NestedSelect');
        $options['data']['multiple'] = $this->multiple;
        $options['data']['placeholder'] = $this->placeholder;
        $this->view->registerAssetBundle(AssetBundle::class);
        echo Html::beginTag('div', $options);
    }

    private function indent(string $out, int $level = 0)
    {
        echo str_repeat(' ', $level * 4 + 4) . $out . "\n";
    }

    private function renderScalar(string $value, string $label, array $stack)
    {

        array_push($stack, $label);
        $this->indent(Html::checkbox($this->name . '[]', in_array($value, $this->selection ?? []), [
            'value' => $value,
            'labelOptions' => [
                'class' => 'option',
            ],
            'label' => $label //implode(' / ', $stack)
        ]), count($stack));
    }

    private function renderGroup(string $label, array $items, array $stack)
    {
        array_push($stack, $label);
        $level = count($stack);
        $this->indent(Html::beginTag('div'), $level);
        $this->indent(Html::checkbox('', false, [
            'label' => $label,
            'labelOptions' => [
                'class' => 'group'
            ]

        ]));
        $this->renderOptions($items, $stack);
        $this->indent('</div>', $level);
    }

    private function renderOptions(array $items, array $stack = []): void
    {
        foreach($items as $value => $label) {
            if (is_scalar($label)) {
                $this->renderScalar($value, $label, $stack);
            } elseif (is_array($label)) {
                $this->renderGroup($value, $label, $stack);
            }
        }
    }

    public function run()
    {
        parent::run();
        echo Html::tag('span', null, [
            'class' => ['current'],
        ]);
        echo Html::beginTag('div', ['class' => 'options']);
        $this->renderOptions($this->items);
        $id = Json::encode($this->id);
        $this->view->registerJs("NestedSelect.updateTitle(document.getElementById($id));");
        echo Html::endTag('div');
        echo Html::endTag('div');
    }


}