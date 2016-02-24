<?php

namespace infoweb\settings\controllers;

use infoweb\cms\helpers\ArrayHelper;
use Yii;
use yii\base\Exception;
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
                'class'   => VerbFilter::className(),
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
            'searchModel'     => $searchModel,
            'dataProvider'    => $dataProvider,
            'gridViewColumns' => $this->getGridViewColumns($searchModel, $dataProvider),
        ]);
    }

    /**
     * Creates a new Setting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        // Load the model with default values
        $model = new Setting([
            'type'          => Setting::TYPE_SYSTEM,
            'translateable' => 1,
            'template'      => 'text',
        ]);

        // The view params
        $params = $this->getDefaultViewParams($model);

        if (Yii::$app->request->getIsPost()) {

            $post = Yii::$app->request->post();

            // Ajax request, validate the models
            if (Yii::$app->request->isAjax) {

                return $this->validateModel($model, $post);

                // Normal request, save models
            } else {
                return $this->saveModel($model, $post);
            }
        }

        return $this->render('create', $params);
    }

    /**
     * Updates an existing Setting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // The view params
        $params = $this->getDefaultViewParams($model);

        if (Yii::$app->request->getIsPost()) {

            $post = Yii::$app->request->post();

            // Ajax request, validate the models
            if (Yii::$app->request->isAjax) {

                return $this->validateModel($model, $post);

                // Normal request, save models
            } else {
                return $this->saveModel($model, $post);
            }
        }

        return $this->render('update', $params);
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
        $label = $model->label;

        try {
            // Only Superadmin can delete
            if (!Yii::$app->user->can('Superadmin'))
                throw new Exception(Yii::t('app', 'You do not have the right permissions to delete this item'));

            $transaction = Yii::$app->db->beginTransaction();

            if (!$model->delete()) {
                throw new Exception(Yii::t('app', 'Error while deleting the node'));
            }

            $transaction->commit();

        } catch (Exception $e) {
            // Set flash message
            Yii::$app->getSession()->setFlash('setting-error', $e->getMessage());

            return $this->redirect(['index']);
        }

        // Set flash message
        Yii::$app->getSession()->setFlash('setting', Yii::t('app', '{item} has been deleted', ['item' => $label]));

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

    /**
     * Returns the columns that are used in the gridview
     *
     * @return  array
     */
    protected function getGridViewColumns($searchModel, $dataProvider)
    {
        // Build the gridview columns
        $gridViewColumns = [];

        // Add category column
        $gridViewColumns[] = [
            'class'         => 'kartik\grid\DataColumn',
            'label'         => Yii::t('app', 'Category'),
            'attribute'     => 'category.name',
            'value'         => 'category.name',
            'enableSorting' => true,
        ];

        // Add key column
        if (Yii::$app->user->can('Superadmin')) {
            $gridViewColumns[] = 'key';
        }

        // Add label column
        $gridViewColumns[] = 'label';

        // Add action column
        $actionColumn = [
            'class' => 'kartik\grid\ActionColumn',
            'width' => '80px',
        ];

        if (Yii::$app->user->can('Superadmin')) {
            $actionColumn['template'] = '{update} {delete}';
            $actionColumn['deleteOptions'] = ['title' => Yii::t('app', 'Delete'), 'data-toggle' => 'tooltip'];
        } else {
            $actionColumn['template'] = '{update}';
        }

        $actionColumn['updateOptions'] = ['title' => Yii::t('app', 'Update'), 'data-toggle' => 'tooltip'];
        $gridViewColumns[] = $actionColumn;

        return $gridViewColumns;
    }

    /**
     * Returns an array of the default params that are passed to a view
     *
     * @param Setting $model The model that has to be passed to the view
     * @return array
     */
    protected function getDefaultViewParams($model = null)
    {
        return [
            'model'      => $model,
            'categories' => SettingCategory::find()->all(),
            'module' => $this->module,
        ];
    }

    /**
     * Performs validation on the provided model and $_POST data
     *
     * @param \infoweb\pages\models\Page $model The page model
     * @param array $post The $_POST data
     * @return array
     */
    protected function validateModel($model, $post)
    {
        $languages = Yii::$app->params['languages'];

        // Populate the model with the POST data
        $model->load($post);

        // Create an array of translation models and populate them
        $translationModels = [];
        // Insert
        if ($model->isNewRecord) {
            foreach ($languages as $languageId => $languageName) {
                $translationModels[$languageId] = new SettingValue(['language' => $languageId]);
            }
            // Update
        } else {
            $translationModels = ArrayHelper::index($model->getTranslations()->all(), 'language');
        }
        Model::loadMultiple($translationModels, $post);

        // Validate the model and translation
        $response = array_merge(
            ActiveForm::validate($model),
            ActiveForm::validateMultiple($translationModels)
        );

        // Return validation in JSON format
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }

    protected function saveModel($model, $post)
    {
        // Wrap everything in a database transaction
        $transaction = Yii::$app->db->beginTransaction();

        // Get the params
        $params = $this->getDefaultViewParams($model);

        // Validate the main model
        if (!$model->load($post)) {
            return $this->render($this->action->id, $params);
        }

        // Add the translations
        foreach (Yii::$app->request->post('SettingValue', []) as $language => $data) {
            foreach ($data as $attribute => $translation) {
                $model->translate($language)->$attribute = $translation;
            }
        }

        // Save the main model
        if (!$model->save()) {
            return $this->render($this->action->id, $params);
        }

        $transaction->commit();

        // Set flash message
        if ($this->action->id == 'create') {
            Yii::$app->getSession()->setFlash('setting', Yii::t('app', '"{item}" has been created', ['item' => $model->label]));
        } else {
            Yii::$app->getSession()->setFlash('setting', Yii::t('app', '"{item}" has been updated', ['item' => $model->label]));
        }

        // Take appropriate action based on the pushed button
        if (isset($post['save-close'])) {
                return $this->redirect(['index']);
        } elseif (isset($post['save-add'])) {
            return $this->redirect(['create']);
        } else {
            return $this->redirect(['update', 'id' => $model->id]);
        }
    }
}
