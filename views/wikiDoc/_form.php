<?php
/* @var $this WikiDocController */
/* @var $model WikiDoc */
/* @var $form CActiveForm */
?>


<div class="form">

    <?php
    foreach (Yii::app()->user->getFlashes() as $key => $message) {
        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
    }
    ?>

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'wiki-doc-form',
        'enableAjaxValidation' => false,
    )); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'category_id'); ?>
        <?php
        echo $form->dropDownList($model, 'category_id', CHtml::listData(WikiCategory::model()->findAll(['order' => 'title']), 'category_id', 'title'), ['empty' => '-Выберите-']
        );
        ?>
        <?php echo $form->error($model, 'category_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'title'); ?>
        <?php echo $form->textField($model, 'title', array('maxlength' => 255, 'style' => 'width:90%')); ?>
        <?php echo $form->error($model, 'title'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'content'); ?>
        <?php
        $this->widget('ext.editMe.widgets.ExtEditMe', [
            'model' => $model,
            'attribute' => 'content',
        ]);
        ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'city_id'); ?>
        <?php
        echo $form->dropDownList($model, 'city_id', CHtml::listData(City::model()->findAll([
            'order' => 't.title']), 'city_id', 'title'), [
            'prompt' => '-Выберите-',
            'ajax' => [
                'type' => 'POST',
                'url' => CController::createUrl('/User/loadcentres'),
                'update' => '#WikiDoc_centre_id',
                'data' => ['city_id' => 'js:this.value'],
            ]]);
        ?>
        <?php echo $form->error($model, 'city_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'centre_id'); ?>

        <?php echo $form->dropDownList($model, 'centre_id', CHtml::listData(Centre::model()->findAll('city_id=:city_id', [':city_id' => $model->city_id]), 'centre_id', 'address'), ['prompt' => '-Выберите центр-']); ?>

        <?php echo $form->error($model, 'centre_id'); ?>
    </div>


    <div class="row">
        <?php echo $form->labelEx($model, 'ip'); ?>
        <?php echo $form->textField($model, 'ip'); ?>
        <?php echo $form->error($model, 'ip'); ?>
    </div>


    <?php if (!$model->isNewRecord): ?>

        <div class="row">
            <?php echo $form->labelEx($model, 'note'); ?>
            <?php echo $form->textField($model, 'note', array('maxlength' => 255, 'style' => 'width:100%')); ?>
            <?php echo $form->error($model, 'note'); ?>
        </div>

    <?php endif; ?>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->