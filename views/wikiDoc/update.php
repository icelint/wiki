<div class="row">
    <?php
    echo CHtml::link('Просмотр', ['view', 'id' => $model->doc_id], ['class' => 'btn btn-info', 'target' => '_blank']);
    ?>
</div>

<h1>Редактирование документа №<?php echo $model->doc_id; ?></h1>

<?php
$class = get_class($model);
$this->renderPartial('_form', array('model' => $model));
?>

<?php include "logs.php"; ?>