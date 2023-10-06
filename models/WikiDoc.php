<?php

/**
 * Таблица "mod_wiki_doc". Базовый документ. Могут быть разные типы документов, которые наследуются от данной модели и содержат дочерние таблицы.
 * При редактировании документа в эту же таблицу записывается старая версия документа.
 *
 * Столбцы:
 * @property integer $doc_id PK.
 * @property string $title Название.
 * @property string $content Содержание.
 * @property integer $category_id FK, категория.
 * @property integer $user_id FK, пользователь, который создал документ.
 * @property string $created Дата создания.
 * @property string $updated Дата обновления.
 * @property integer $type_id Тип документа (Инструкции, случаи тех. поддержки..).
 * @property integer $source_doc_id FK, исходный PK(для журнала изменений).
 * @property integer $centre_id FK, центр.
 * @property integer $city_id FK, город.
 *
 * Отношения:
 * @property WikiComment[] $сomments Комментарии к документу.
 * @property WikiCategory $category Категория документа.
 * @property Users $user Пользователь, создавший документ.
 * @property WikiDocType $type Тип документа.
 * @property City $city Город.
 * @property Centre $centre Центр.
 */
class WikiDoc extends CActiveRecord
{
    //Переменная для хранеия предыдущей модели перед сохранением. Для журналирования.
    protected $oldModel;

    //Переменная для хранения дочерних моделей. Для журналирования.
    protected $docRelations;

    //Критерии поиска.
    protected $searchCriteria;

    public function getDocRelations()
    {
        return $this->docRelations;
    }

    //По-умолчанию модель используется для актуальных версий документов.
    public function defaultScope()
    {
        return ['condition' => "source_doc_id IS NULL"];
    }

    public function behaviors()
    {
        return [
            'datetimeI18NBehavior' => ['class' => 'ext.DateTimeI18NBehavior'],
            'CCompare' => ['class' => 'application.behaviors.CCompare'],
        ];
    }

    public function tableName()
    {
        return 'mod_wiki_doc';
    }

    /**
     * Находит текущую версию документа (для журнала).
     * @return WikiDoc Документ.
     */
    public function findCurrentEdition()
    {
        $criteria = new CDbCriteria;
        $criteria->params = [':doc_id' => $this->source_doc_id];
        $currentEdition = $this->resetScope()->find($criteria);

        return $currentEdition;
    }

    /**
     * Проверка разрешения на просмотр. Также используется для проверки разрешения на создание нового документа.
     * @param $id PK заявки.
     * @return bool Разрешение.
     */
    public function isViewAllowed($id)
    {
        $model = self::model()->findByPk($id);
        return self::checkAccess($model);
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Проверка доступа к документу.
     * @param $doc WikiDoc Документ.
     * @return bool Результат проверки.
     */
    public static function checkAccess($doc)
    {

        if (is_a($doc, "WikiDoc")) {
            if (Yii::app()->user->checkAccess("IT") || (!$doc->source_doc_id && !$doc->city_id)) {
                return true;
            }
        }

        return false;
    }

    public function rules()
    {

        return [
            ['title, content, category_id', 'required'],
            ['category_id, user_id, type_id, source_doc_id, centre_id, city_id', 'numerical', 'integerOnly' => true],
            ['title,note,ip', 'length', 'max' => 255],
            ['content, updated', 'safe'],

            ['doc_id, title, content, category_id, user_id, created, updated, type_id, source_doc_id', 'safe', 'on' => 'search'],
            ['doc_id, title, content, category_id, user_id, created, updated, type_id, source_doc_id', 'safe', 'on' => 'log'],
        ];
    }

    public function relations()
    {
        return [
            'comments' => [self::HAS_MANY, 'WikiDocComment', 'doc_id'],
            'category' => [self::BELONGS_TO, 'WikiCategory', 'category_id'],
            'user' => [self::BELONGS_TO, 'User', 'user_id'],
            'type' => [self::BELONGS_TO, 'WikiDocType', 'type_id'],
            'centre' => [self::BELONGS_TO, 'Centre', 'centre_id'],
            'city' => [self::BELONGS_TO, 'City', 'city_id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'doc_id' => '№',
            'title' => 'Заголовок',
            'content' => 'Содержание',
            'category_id' => 'Категория',
            'user_id' => 'Пользователь',
            'created' => 'Создан',
            'updated' => 'Обновлен',
            'type_id' => 'Тип',
            'source_doc_id' => 'Исх id',
            'note' => 'Примечание к редакции',
            'city_id' => 'Город',
            'centre_id' => 'Центр',
            'ip' => 'IP'
        ];
    }

    public function afterSave()
    {

        if (isset($this->oldModel)) {
            $logModel = $this->oldModel->cloneModel();
            $logModel->source_doc_id = $this->doc_id;
            $logModel->save();
        }

        return parent::afterSave();
    }

    public function beforeSave()
    {

        $this->user_id = Yii::app()->user->getId();

        if ($this->isNewRecord) {
            if ($this->scenario != 'log') {
                $this->created = $this->updated = date('d.m.Y H:i:s');
            }
        } else {
            //Cохраняем старую модель.
            $this->oldModel = WikiDoc::model()->findByPk($this->getPrimaryKey());
            $this->updated = date('d.m.Y H:i:s');
        }

        return parent::beforeSave();
    }

    /**
     * Поиск журнала изменений документа.
     * @return CActiveDataProvider
     */
    public function searchLogs()
    {
        $criteria = new CDbCriteria;

        $criteria->condition = 'source_doc_id=:doc_id OR doc_id=:doc_id';

        if ($this->source_doc_id) {
            $criteria->params = [':doc_id' => $this->source_doc_id];
        } else {
            $criteria->params = [':doc_id' => $this->doc_id];
        }

        $criteria->order = 'updated DESC';

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }

    public function search()
    {
        $this->prepareSearch();

        return new CActiveDataProvider($this, [
            'criteria' => $this->searchCriteria,
        ]);
    }

    /**
     * Подготовка критериев поиска.
     */
    protected function prepareSearch()
    {
        $this->searchCriteria = new CDbCriteria;
        $this->searchCriteria->with = ['user'];

        $this->searchCriteria->compare('doc_id', $this->doc_id);
        $this->searchCriteria->compare('title', $this->title, true);
        $this->searchCriteria->compare('content', $this->content, true);
        $this->searchCriteria->compare('category_id', $this->category_id);
        $this->searchCriteria->compare('user_id', $this->user_id);
        $this->searchCriteria->compare('created', $this->created, true);
        $this->searchCriteria->compare('updated', $this->updated, true);
        $this->searchCriteria->compare('type_id', $this->type_id);
        $this->searchCriteria->compare('source_doc_id', $this->source_doc_id);

        //Для не-сотрудников отдела ИТ доступны только те документы, у которых не указан город.
        if (!Yii::app()->user->checkAccess('IT')) {
            $user = User::model()->findByPk(Yii::app()->user->getId());
            $this->searchCriteria->condition = 'city_id IS NULL';
        }

    }
}
