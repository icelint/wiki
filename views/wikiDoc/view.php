<?php
if ($model->source_doc_id) {
    ?>
    <div class="alert alert-danger">Внимание! Старая
        редакция. <?php echo CHtml::link('Последняя редакция', ['view', 'id' => $model->source_doc_id]); ?>.
    </div>
    <?php
} else {
    if (Yii::app()->user->checkAccess("IT")):
        ?>
        <div class="row voffset3">
            <?php echo CHtml::link('Редактировать', ['update', 'id' => $model->doc_id], ['class' => 'btn btn-info']); ?>
        </div>
    <?php
    endif;

}

?>

    <h1>№<?php echo $model->doc_id; ?>: <?php echo $model->title; ?></h1>

    <div class="row voffset3">
        <?php

        //Если выводится запись журнала
        if ($model->source_doc_id) {
            $currentEdition = $model->findCurrentEdition();

            if ($currentEdition->created) {
                $createdBy = $currentEdition->user->name . ' ' . $currentEdition->user->surname . ', ' . $currentEdition->created;
            }
        } else {
            $createdBy = $model->user->name . ' ' . $model->user->surname . ', ' . $model->created;
        }


        if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_view_' . $model->type->class . '.php')) {
            include '_view_' . $model->type->class . '.php';
        } else {
            $this->widget('zii.widgets.CDetailView', array(
                'data' => $model,
                'attributes' => array(
                    [
                        'label' => 'Ссылка на документ',
                        'value' => Yii::app()->params['intranetUrl'] . '/wiki/WikiDoc/view/id/' . $model->getPrimaryKey(),
                    ],
                    [
                        'name' => 'category_id',
                        'value' => $model->category->title,
                    ],
                    [
                        'label' => 'Создан',
                        'value' => $createdBy,
                    ],
                    [
                        'label' => 'Эта редакция',
                        'value' => $model->user->name . ' ' . $model->user->surname . ', ' . $model->updated,
                        'visible' => $model->created != $model->updated
                    ],
                    [
                        'name' => 'ip',
                        'value' => isset($model->ip) ? $model->ip : '',
                    ],
                    [
                        'name' => 'city.title',
                        'value' => isset($model->city->title) ? $model->city->title : '',
                    ],

                    [
                        'label' => 'Центр',
                        'name' => 'centre.title',
                        'value' => isset($model->centre->address) ? $model->centre->address : '',
                    ],

                ),
            ));
        }

        ?>
    </div>


    <div class="row voffset3">
        <?php
        echo $model->content;
        ?>
    </div>


<?php

if ($model->created != $model->updated && Yii::app()->user->checkAccess("IT")) {
    include "logs.php";
}
