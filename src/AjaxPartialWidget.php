<?php

namespace rebzden\ajaxpartial;


use yii\base\Widget;
use yii\helpers\Json;
use yii\web\View;

class AjaxPartialWidget extends Widget
{
    /**
     * [
     *   'selector' => '.form-selector',
     *   'reload'   => [
     *              [
     *                 'url'       => Url::to(['/question-section/create', 'id' => $model->id]),
     *                 'container' => '.new-section-container'
     *              ],
     *              [
     *                  'url'       => Url::to(['/question-template/sections', 'id' => $model->id]),
     *                  'container' => '.sections-container'
     *              ]
     *    ],
     *    'onSubmit' => new JsExpression("function(response){ doSomething()}")
     *    'afterSubmit' => new JsExpression("function(response){ doSomething()}")
     * ]
     * @var array
     */
    public $formSelectors = [];

    /**
     * [
     *   'selector' => '.new-section',
     *   'reload'   => [
     *              [
     *                 'url'       => Url::to(['/question-section/create', 'id' => $model->id]),
     *                 'container' => '.new-section-container'
     *              ],
     *              [
     *                  'container' => '.sections-container',
     *                  'useElementHref' => true
     *              ]
     *    ],
     * ],
     * [
     *   'selector' => '.new-section',
     *   'sendAjax'  => true,
     *   'method'   => 'POST',
     *   'url'       => Url::to(['/question-section/create', 'id' => $model->id]), //Leave empty to use element href attribute
     *   'afterSend' => new JsExpression("function(data){toast.showMessage(data)}"),
     *   'reload'   => [
     *              [
     *                 'url'       => Url::to(['/question-section/create', 'id' => $model->id]),
     *                 'container' => '.new-section-container'
     *              ],
     *              [
     *                  'container' => '.sections-container',
     *                  'useElementHref' => true
     *              ],
     *              [
     *                  //NO CONTAINER - USE MODAL
     *                  'useElementHref' => true
     *              ]
     *    ],
     * ]
     * @var array
     */
    public $triggerSelectors = [];

    /**
     * [
     *     [
     *          'url'       => Url::to(['/guest-preferences/create', 'id' => $model->id]),
     *          'container' => '.new-question-container'
     *     ],
     *     [
     *          'url'       => Url::to(['/guest-preferences/create', 'id' => $model->id]),
     *          'container' => '.new-question-container',
     *          'after'
     *     ]
     * ]
     * @var array
     */
    public $loadSelectors = [];

    public $modalId = 'ajaxpartial-modal';
    public $header;
    public $headerOptions = ['class' => 'big'];
    public $size = 'modal-lg';

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $this->registerAssets();
        return parent::run();
    }

    /**
     * @param View $view
     */
    private function renderModal($view)
    {
        $view->beginBlock('modalContainer');
        echo $this->render('ajaxpartial', ['widget' => $this]);
        $view->endBlock();
    }

    protected function registerAssets()
    {
        $view = $this->getView();
        $this->renderModal($view);
        AjaxPartialWidgetAsset::register($view);

        $clickSelectors = Json::encode($this->triggerSelectors);
        $loadSelectors = Json::encode($this->loadSelectors);
        $formSelectors = Json::encode($this->formSelectors);
        $_options = Json::encode([
            'selectors' => [
                'clickSelectors' => $this->triggerSelectors,
                'loadSelectors'  => $this->loadSelectors,
                'formSelectors'  => $this->formSelectors,
            ],
            'modal'     => [
                'template' => $view->blocks['modalContainer'],
                'id'       => $this->modalId
            ]
        ]);

        $view->registerJs("
        if (typeof ajaxPartial === 'undefined') {
            ajaxPartial = new AjaxPartial({$_options})
        }else{
            ajaxPartial.addLoad({$loadSelectors});
            ajaxPartial.addClick({$clickSelectors});
            ajaxPartial.addAjaxForm({$formSelectors});
        }");
    }
}