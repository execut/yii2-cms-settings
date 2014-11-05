<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\SwitchInput;
?>
<div class="tab-content default-tab">
    
    <?= $form->field($model, 'key')->textInput([
        'maxlength' => 255
    ]); ?>
    
    <?= $form->field($model, 'label')->textInput([
        'maxlength' => 255
    ]); ?>
    
    <?= $form->field($model, 'type')->dropDownList([
        'system'        => Yii::t('app', 'System'),
        'user-defined'  => Yii::t('app', 'User defined')
    ],[
        'options' => [
            'system' => ['disabled' => (Yii::$app->user->can('Superadmin')) ? false : true]
        ]
    ]); ?>
    
    <?= $form->field($model, 'category_id')->dropDownList(ArrayHelper::map($categories, 'id', 'name'),[
        'options' => [
            'system' => ['disabled' => (Yii::$app->user->can('Superadmin')) ? false : true]
        ]
    ]); ?>
    
    <?= $form->field($model, 'template')->dropDownList([
        'text' => 'Text'
    ]); ?>
    
    <?php echo $form->field($model, 'translateable')->widget(SwitchInput::classname(), [
        'inlineLabel' => false,
        'pluginOptions' => [
            'onColor' => 'success',
            'offColor' => 'danger',
            'onText' => Yii::t('app', 'Yes'),
            'offText' => Yii::t('app', 'No'),
        ]
    ]); ?>
</div>