<?php

/**
 * Таблица "mod_wiki_category". Категория документа.
 *
 * Столбцы:
 * @property integer $category_id PK
 * @property string $title Название категории.
 *
 */
class WikiCategory extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mod_wiki_category';
    }

    public function rules()
    {
        return [
            ['title', 'required'],
            ['title', 'length', 'max' => 255],
            ['category_id, title', 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'category_id' => 'PK',
            'title' => 'Категория страниц',
        ];
    }

    public function search()
    {

        $criteria = new CDbCriteria;

        $criteria->compare('category_id', $this->category_id);
        $criteria->compare('title', $this->title, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }
}
