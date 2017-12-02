<?php

use common\components\widgets\ajaxmodal\AjaxModalWidget;
use yii\bootstrap\Modal;


/** @var AjaxModalWidget $widget */
Modal::begin([
    'id'            => $widget->modalId,
    'size'          => $widget->size,
    'header'        => $widget->header,
    'headerOptions' => $widget->headerOptions,
])
?>
    <div class="modalContent"></div>
<?php Modal::end() ?>