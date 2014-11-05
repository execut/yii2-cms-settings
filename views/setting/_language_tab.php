<div class="tab-content language-tab">
    <?= $form->field($model, "[{$model->language}]value")->textArea([
        'rows' => 5,
        'name' => "SettingValue[{$model->language}][value]"
    ]); ?>
</div>