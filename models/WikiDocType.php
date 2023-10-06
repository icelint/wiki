<?php

/**
 * Таблица "mod_wiki_type". Тип документа.
 *
 * Столбцы:
 * @property integer $type_id PK.
 * @property string $title Название.
 *
 */
class WikiDocType extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mod_wiki_doc_type';
    }

    public function rules()
    {
        return [
            ['title', 'required'],
            ['type_id, class, title', 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'type_id' => 'PK',
            'title' => 'Название',
        ];
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('type_id', $this->type_id);
        $criteria->compare('class', $this->class, true);
        $criteria->compare('title', $this->title, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }
}
