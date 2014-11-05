<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;

$this->title = Yii::t('infoweb/settings', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-index">

    <?php // Title ?>
    <h1>
        <?= Html::encode($this->title) ?>
        <?php // Buttons ?>
        <div class="pull-right">
            <?= Html::a(Yii::t('app', 'Create {modelClass}', [
                'modelClass' => Yii::t('infoweb/settings', 'Setting'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>    
        </div>
    </h1>
    
    <?php // Flash messages ?>
    <?php echo $this->render('_flash_messages'); ?>

    <?php // Gridview ?>
    <?php Pjax::begin(['id'=>'grid-pjax']); ?>
    <?php echo GridView::widget([
        'dataProvider'=> $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'key',
            'label',
            'category.name'
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{update}',
                'updateOptions'=>['title' => Yii::t('app', 'Update'), 'data-toggle' => 'tooltip'],
                'width' => '50px',
            ],
        ],
        'responsive' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => 88],
        'hover' => true,
    ]);
    ?>
    <?php Pjax::end(); ?>

</div>