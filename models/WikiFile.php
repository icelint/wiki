<?php

/**
 * Таблица "mod_wiki_file". Файлы документа.
 *
 * Столбцы:
 * @property integer $file_id PK.
 * @property integer $doc_id FK, документ.
 * @property string $path Путь к файлу.
 * @property string $original Исходное имя файла.
 *
 * Отношения:
 * @property WikiDoc $doc Документ.
 */
class WikiFile extends CActiveRecord
{

    public function tableName()
    {
        return 'mod_wiki_file';
    }

    public function rules()
    {
        return [
            ['doc_id, path', 'required'],
            ['doc_id', 'numerical', 'integerOnly' => true],
            ['path,access', 'length', 'max' => 255],
            ['file_id, doc_id, path', 'safe', 'on' => 'search'],
        ];
    }

    public function relations()
    {
        return [
            'doc' => [self::BELONGS_TO, 'WikiDoc', 'doc_id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file_id' => 'PK',
            'doc_id' => 'FK, документа',
            'path' => 'Путь к файлу',
        ];
    }

    public function search()
    {

        $criteria = new CDbCriteria;

        $criteria->compare('file_id', $this->file_id);
        $criteria->compare('doc_id', $this->doc_id);
        $criteria->compare('path', $this->path, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }

    /**
     * Проверка разрешения на скачивание файла.
     * @param $id PK файла.
     * @return bool Разрешение.
     */
    public function isDownloadAllowed($id)
    {

        $model = self::model()->findByPk($id);

        if ($model) {
            $doc = WikiDoc::model()->findByPk($model->doc_id);
            return WikiDoc::checkAccess($doc);
        }

        return false;
    }


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
