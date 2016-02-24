<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => Yii::t('infoweb/settings', 'Setting'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('infoweb/settings', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
        'module' => $module,
    ]) ?>

</div>