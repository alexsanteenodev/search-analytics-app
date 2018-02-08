<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Bouquet */

$this->title = 'Новый букет';
?>
<div class="bouquet-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('form', [
        'models' => $models
    ]) ?>

</div>
