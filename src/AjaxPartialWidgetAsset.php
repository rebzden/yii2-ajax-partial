<?php

namespace rebzden\ajaxpartial;


use yii\web\AssetBundle;

class AjaxPartialWidgetAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public $sourcePath = __DIR__ . '/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'css/ajaxpartial.css'
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/ajaxpartial.js'
    ];

    /**
     *
     * @inheritdoc
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}