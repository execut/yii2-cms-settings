<?php if (Yii::$app->getSession()->hasFlash('setting')): ?>
<div class="alert alert-success">
    <p><?= Yii::$app->getSession()->getFlash('setting') ?></p>
</div>
<?php endif; ?>

<?php if (Yii::$app->getSession()->hasFlash('setting-error')): ?>
<div class="alert alert-danger">
    <p><?= Yii::$app->getSession()->getFlash('setting-error') ?></p>
</div>
<?php endif; ?>