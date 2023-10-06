<?php

Yii::app()->clientScript->registerScript('ckeditor_message', '
	  window.parent.CKEDITOR.tools.callFunction("'. $CKEditorFuncNum . '", "", "'.$message.'");
	', CClientScript::POS_END);