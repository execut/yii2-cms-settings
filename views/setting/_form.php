<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

?>

<div class="setting-form">

    <?php
    // Init the form
    $form = ActiveForm::begin([
        'id'                     => 'setting-form',
        'options'                => ['class' => 'tabbed-form'],
        'enableAjaxValidation'   => true,
        'enableClientValidation' => false,
    ]);

    // Initialize the tabs
    $tabs = [];

    // Add the default tab
    if ($model->translateable == true) {
        $tabs[] = [
            'label'   => Yii::t('app', 'General'),
            'content' => $this->render('_default_tab', [
                'model' => $model,
                'form'  => $form,
            ]),
            'active'  => true,
        ];
    }

    // Add the main tabs
    $tabs[] = [
        'label'   => Yii::t('app', 'Data'),
        'content' => $this->render('_data_tab', [
            'model'      => $model,
            'module'     => $module,
            'form'       => $form,
            'categories' => $categories,
        ]),
    ];

    // Display the tabs
    echo Tabs::widget(['items' => $tabs]);
    ?>

    <div class="form-group buttons">
        <?= $this->render('@infoweb/cms/views/ui/formButtons', ['model' => $model]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>