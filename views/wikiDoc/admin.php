<?php
/* @var $this WikiDocController */
/* @var $model WikiDoc */
?>

<h1>Документация</h1>

<?php if (Yii::app()->user->checkAccess("IT")): ?>
    <div class="row">
        <?php
        echo CHtml::link('Новый документ', ['create', 'class' => 'WikiDoc'], ['class' => 'btn btn-info']);
        ?>
    </div>
<?php endif; ?>


<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'wiki-doc-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        [
            'name' => 'doc_id',
            'htmlOptions' => array('style' => 'width: 5%')
        ],

        [
            'name' => 'category_id',
            'value' => '$data->category->title',
            'filter' => CHtml::listData(WikiCategory::model()->findAll(), 'category_id', 'title'),

        ],
        [
            'name' => 'city_id',
            'value' => 'isset($data->city->title) ? $data->city->title : ""',
            'filter' => CHtml::listData(City::model()->findAll(['order' => 'title']), 'city_id', 'title'),
            'visible' => WikiDoc::model()->isViewAllowed($model->getPrimaryKey()),
        ],

        'title',
        [
            'name' => 'user_id',
            'filter' => false,
            'header' => 'Последняя редакция',
            'value' => '$data->user->surname." ".$data->user->name."<br>".$data->updated',
            'type' => 'raw'
        ],
        [
            'class' => 'CButtonColumn',
            'header' => 'Просмотр',
            'buttons' => [
                'view' => [
                    'label' => '<i class="icon-eye-open"> </i>',
                    'options' => ['class' => 'btn btn-info', 'title' => 'Просмотр документа'],
                    'url' => 'Yii::app()->createUrl("wiki/WikiDoc/view", array("id"=>$data->doc_id))',
                    'imageUrl' => false,
                ],
            ],
            'template' => '{view}',
        ],
        [
            'class' => 'CButtonColumn',
            'header' => 'Изменение',
            'visible' => 'WikiDoc::model()->isViewAllowed($model->getPrimaryKey())',
            'buttons' => [
                'update' => [
                    'label' => '<i class="icon-pencil"> </i>',
                    'options' => ['class' => 'btn btn-info', 'title' => 'Изменить документ'],
                    'url' => 'Yii::app()->createUrl("wiki/WikiDoc/update", array("id"=>$data->doc_id))',
                    'imageUrl' => false,
                ],
            ],
            'template' => '{update}',
        ],
    ),
));