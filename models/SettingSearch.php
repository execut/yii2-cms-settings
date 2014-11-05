<?php

namespace infoweb\settings\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use infoweb\settings\models\Setting;
use infoweb\settings\models\SettingValue;

/**
 * SettingSearch represents the model behind the search form about `app\models\Setting`.
 */
class SettingSearch extends Setting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['key', 'label', 'category.name'], 'safe'],
        ];
    }
    
    public function attributes()
    {
        // Add related fields to searchable attributes
        return array_merge(parent::attributes(), ['category.name']);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Setting::find();
        
        $query->andFilterWhere(['language' => Yii::$app->language]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        
        // Join the entity model as a relation
        $query->joinWith(['category']);
        
        // enable sorting for the label attribute
        $dataProvider->sort->attributes['label'] = [
            'asc' => ['label' => SORT_ASC],
            'desc' => ['label' => SORT_DESC],
        ];
        
        // enable sorting for the related column
        $dataProvider->sort->attributes['category.name'] = [
            'asc' => ['category.name' => SORT_ASC],
            'desc' => ['category.name' => SORT_DESC],
        ];
        
        $dataProvider->sort->defaultOrder = ['category.name' => SORT_ASC, 'label' => SORT_ASC];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'key' => $this->key
        ]);

        $query
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['LIKE', 'category.name', $this->getAttribute('category.name')]);

        return $dataProvider;
    }
}
