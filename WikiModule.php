<?php

/**
 * Модуль "База знаний IT". База инструкций и случаев технической поддержки, которые требуют внимания.
 * Доступна для редактирования всем сотрудникам отдела ИТ. Инженерам региональных центров доступны инструкции.
 */
class WikiModule extends CWebModule
{
	public function init()
	{
        
        $path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.wiki.assets'));
        Yii::app()->getClientScript()->registerCssFile($path . '/css/default.min.css');
        Yii::app()->getClientScript()->registerScriptFile($path . '/js/highlight.min.js', CClientScript::POS_END);           
        Yii::app()->clientScript->registerScript('highlight', '
            hljs.initHighlightingOnLoad();
        ');        
        
		$this->setImport(array(
			'wiki.models.*',
			'wiki.components.*',
		));
	}
}
