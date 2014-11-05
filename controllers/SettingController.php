<?php

namespace infoweb\settings\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\base\Model;
use infoweb\settings\models\Setting;
use infoweb\settings\models\SettingValue;
use infoweb\settings\models\SettingCategory;
use infoweb\settings\models\SettingSearch;

/**
 * SettingController implements the CRUD actions for Setting model.
 */
class SettingController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
            ],
        ];
    }

    /**
     * Lists all Setting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SettingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Setting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $languages = Yii::$app->params['languages'];

        // Load the model with default values
        $model = new Setting([
            'type' => Setting::TYPE_SYSTEM,
            'translateable' => 1,
            'template' => 'text'
        ]);
        
        // Get all the categories
        $categories = SettingCategory::find()->all();
        
        if (Yii::$app->request->getIsPost()) {
            
            $post = Yii::$app->request->post();
            
            // Ajax request, validate the models
            if (Yii::$app->request->isAjax) {
                               
                // Populate the model with the POST data
                $model->load($post);
                
                // Create an array of translation models
                $translationModels = [];
                
                foreach ($languages as $languageId => $languageName) {
                    $translationModels[$languageId] = new SettingValue(['language' => $languageId]);
                }
                
                // Populate the translation models
                Model::loadMultiple($translationModels, $post);

                // Validate the model and translation and alias models
                $response = array_merge(
                    ActiveForm::validate($model),
                    ActiveForm::validateMultiple($translationModels)
                );
                
                // Return validation in JSON format
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            
            // Normal request, save models
            } else {
                // Wrap the everything in a database transaction
                $transaction = Yii::$app->db->beginTransaction();                
                
                // Save the main model
                if (!$model->load($post) || !$model->save()) {
                    return $this->render('create', [
                        'model' => $model,
                        'categories' => $categories
                    ]);
                }
                
                // Save the translations
                foreach ($languages as $languageId => $languageName) {
                    
                    $data = $post['SettingValue'][$languageId];
                    
                    // Set the translation language and attributes                    
                    $model->language    = $languageId;
                    $model->value       = $data['value'];
                    
                    if (!$model->saveTranslation()) {
                        return $this->render('create', [
                            'model' => $model,
                            'categories' => $categories
                        ]);    
                    }                       
                }
                
                $transaction->commit();

                // Set flash message
                Yii::$app->getSession()->setFlash('setting', Yii::t('app', '"{item}" has been created', ['item' => $model->label]));
                
                // Take appropriate action based on the pushed button
                if (isset($post['close'])) {
                    return $this->redirect(['index']);
                } elseif (isset($post['new'])) {
                    return $this->redirect(['create']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }   
            }    
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => $categories
        ]);
    }

    /**
     * Updates an existing Setting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $languages = Yii::$app->params['languages'];
        $model = $this->findModel($id);
        
        // Get all the categories
        $categories = SettingCategory::find()->all();
        
        if (Yii::$app->request->getIsPost()) {
            
            $post = Yii::$app->request->post();
            
            // Ajax request, validate the models
            if (Yii::$app->request->isAjax) {
                               
                // Populate the model with the POST data
                $model->load($post);
                
                // Create an array of translation models
                $translationModels = [];
                
                foreach ($languages as $languageId => $languageName) {
                    $translationModels[$languageId] = $model->getTranslation($languageId);
                }
                
                // Populate the translation models
                Model::loadMultiple($translationModels, $post);

                // Validate the model and translation and alias models
                $response = array_merge(
                    ActiveForm::validate($model),
                    ActiveForm::validateMultiple($translationModels)
                );
                
                // Return validation in JSON format
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            
            // Normal request, save models
            } else {
                // Wrap the everything in a database transaction
                $transaction = Yii::$app->db->beginTransaction();                
                
                // Save the main model
                if (!$model->load($post) || !$model->save()) {
                    return $this->render('update', [
                        'model' => $model,
                        'categories' => $categories
                    ]);
                } 
                
                // Save the translation models and seo tags
                foreach ($languages as $languageId => $languageName) {
                    
                    // Save the translation
                    $data = $post['SettingValue'][$languageId];
                    
                    $model->language    = $languageId;
                    $model->value       = $data['value'];
                    
                    if (!$model->saveTranslation()) {
                        return $this->render('update', [
                            'model' => $model,
                            'categories' => $categories
                        ]);    
                    }                     
                }
                
                $transaction->commit();

                // Set flash message
                Yii::$app->getSession()->setFlash('setting', Yii::t('app', '"{item}" has been updated', ['item' => $model->label]));
              
                // Take appropriate action based on the pushed button
                if (isset($post['close'])) {
                    return $this->redirect(['index']);                    
                } elseif (isset($post['new'])) {
                    return $this->redirect(['create']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }    
            }    
        }

        return $this->render('update', [
            'model' => $model,
            'categories' => $categories
        ]);
    }

    /**
     * Deletes an existing Setting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        try {                    
            // Only Superadmin can delete
            if (!Yii::$app->user->can('Superadmin'))
                throw new \yii\base\Exception(Yii::t('app', 'You do not have the right permissions to delete this item'));
        
            $transaction = Yii::$app->db->beginTransaction();
            $model->delete();
            $transaction->commit();    
        } catch (\yii\base\Exception $e) {
            // Set flash message
            Yii::$app->getSession()->setFlash('setting-error', $e->getMessage());
    
            return $this->redirect(['index']);        
        }        
        
        // Set flash message
        Yii::$app->getSession()->setFlash('setting', Yii::t('app', '{item} has been deleted', ['item' => $model->label]));

        return $this->redirect(['index']);
    }

    /**
     * Finds the Setting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Setting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Setting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested item does not exist'));
        }
    }
}
