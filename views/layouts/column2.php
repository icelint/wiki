<?php $this->beginContent('application.views.layouts.main'); ?>
<?php

$this->menu = [
    ['label' => 'Документация', 'url' => ['/wiki/WikiDoc/admin/'], 'visible' => Yii::app()->user->checkAccess('wiki.WikiDoc.admin')],
];
?>

    <div class="row-fluid">
        <div class="span12  horizontal-menu">
            <?php
            $this->widget('zii.widgets.CMenu', [
                'items' => $this->menu,
            ]);
            ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12" id="content">
            <?php echo $content; ?>
        </div><!-- content -->
    </div>

<?php $this->endContent(); ?>