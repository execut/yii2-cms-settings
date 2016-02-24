<div class="tab-content default-language-tab">
    <?= $form->field($model, "[{$model->language}]value")->textArea([
        'rows' => 5,
        'name' => "SettingValue[{$model->language}][value]",
        'data-duplicateable' => Yii::$app->getModule('settings')->allowContentDuplication ? 'true' : 'false'
    ]); ?>
</div>