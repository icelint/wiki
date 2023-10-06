<?php

/**
 * Управление документами.
 */
class WikiDocController extends Controller
{
    public $layout = '/layouts/column2';

    /**
     * Просмотр документа.
     * @param $id
     */
    public function actionView($id)
    {

        $model = WikiDoc::model()->resetScope()->findByPk($id);

        if ($model->type->class != "WikiDoc") {
            $class = $model->type->class;
            $model = $class::model()->resetScope()->findByPk($id);
        }

        $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Добавление документа.
     */
    public function actionCreate($class)
    {

        $type = WikiDocType::model()->find('class=:class', ['class' => $class]);

        $model = new $type->class;
        $model->type_id = $type->type_id;


        if (isset($_POST[$model->type->class])) {
            $model->attributes = $_POST[$model->type->class];

            foreach ((array)$model->getDocRelations() as $relation) {
                $model->$relation->attributes = $_POST[$relation];
            }

            if ($model->save()) {
                Yii::app()->user->setFlash('success', "Информация сохранена.");
                $this->redirect(['update', 'id' => $model->doc_id]);
            }
        }

        $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Обновление документа.
     * @param $id
     * @throws CHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        $class = $model->type->class;

        if (isset($_POST[$class])) {
            $model->attributes = $_POST[$class];

            foreach ((array)$model->getDocRelations() as $relation) {
                $model->$relation->attributes = $_POST[$relation];
            }

            if ($model->save()) {
                Yii::app()->user->setFlash('success', "Информация сохранена.");
            }
        } else {
            unset($model->note);
        }

        $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * Список инструкций.
     */
    public function actionAdmin()
    {
        $model = new WikiDoc('search');
        $model->unsetAttributes();

        $type = WikiDocType::model()->find('class=:class', ['class' => 'WikiDoc']);

        if (isset($_GET['WikiDoc']))
            $model->attributes = $_GET['WikiDoc'];
        $model->type_id = $type->type_id;

        $this->render('admin', [
            'model' => $model,
        ]);
    }

    public function loadModel($id)
    {
        $model = WikiDoc::model()->findByPk($id);

        if ($model->type->class != "WikiDoc") {
            $class = $model->type->class;
            $model = $class::model()->findByPk($id);
        }

        if ($model === null)
            throw new CHttpException(404, 'Страница не найдена.');

        return $model;
    }

    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'wiki-doc-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
