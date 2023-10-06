<h2>Редакции документа</h2>

<?php

$this->widget('zii.widgets.grid.CGridView', [
    'id' => 'docs-grid',
    'dataProvider' => $model->resetScope()->searchLogs(),
    'ajaxUpdate' => false,
    'template' => '{items}',
    'columns' => [
        [
            'value' => '($data->doc_id == ' . $model->doc_id . ') ? $data->doc_id.\' <i class="icon-chevron-left"> </i>\' : $data->doc_id',
            'name' => 'doc_id',
            'type' => 'raw'
        ],

        [
            'name' => 'user_id',
            'value' => '$data->user->name." ".$data->user->surname'
        ],

        [
            'header' => 'Дата',
            'value' => '$data->updated'
        ],

        'note',

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
    ]
]);



