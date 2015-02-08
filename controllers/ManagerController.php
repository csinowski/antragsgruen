<?php

namespace app\controllers;

use app\models\db\Site;
use app\models\db\User;
use app\models\exceptions\AntragsgruenException;
use app\models\forms\SiteCreateForm;
use Yii;
use yii\db\Exception;
use yii\helpers\Html;

class ManagerController extends Base
{
    /**
     *
     */
    protected function addSidebar()
    {
        $sites = Site::getSidebarSites();

        $html = "<ul class='nav nav-list einsatzorte-list'>";
        $html .= "<li class='nav-header'>Aktuelle Einsatzorte</li>";
        foreach ($sites as $site) {
            $url = $this->createUrl(['consultation/index', "siteId" => $site->subdomain]);
            $html .= "<li>" . Html::a($site->title, $url) . "</li>\n";
        }
        $html .= '</ul>';
        $this->layoutParams->menusHtml[] = $html;

    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'column2';

        //$this->performLogin($this->createUrl("manager/index"));
        $this->addSidebar();
        return $this->render('index');
    }

    /**
     * @return null|User
     */
    protected function eligibleToCreateUser()
    {
        if (Yii::$app->user->isGuest) {
            return null;
        }

        /** @var User $user */
        $user = yii::$app->user->identity;

        if (!$user->isWurzelwerkUser()) {
            return null;
        }

        return $user;
    }

    /**
     * @return User
     */
    protected function requireEligibleToCreateUser()
    {
        $user = $this->eligibleToCreateUser();
        if (!$user) {
            $this->redirect($this->createUrl("manager/index"));
        }
        return $user;
    }

    /**
     * @param SiteCreateForm $model
     * @return Site
     * @throws \app\models\exceptions\DB
     */
    private function createSiteFromForm(SiteCreateForm $model)
    {
        $site = Site::createFromForm($model);
        //$consultation =
        return $site;
    }


    /**
     * @return string
     */
    public function actionCreatesite()
    {
        $this->requireEligibleToCreateUser();

        $this->layout = 'column2';
        $this->addSidebar();

        $model  = new \app\models\forms\SiteCreateForm();
        $errors = array();

        if (isset($_POST['create'])) {
            try {
                $model->setAttributes($_POST['SiteCreateForm']);
                if ($model->validate()) {
                    $site = $this->createSiteFromForm($model);
                    echo "SUCCESS";
                    var_dump($site);
                    die();
                } else {
                    foreach ($model->getErrors() as $message) {
                        foreach ($message as $message2) {
                            $errors[] = $message2;
                        }
                    }
                }
            } catch (Exception $e) {
                var_dump($e);
            }
        }

        return $this->render(
            'createsite',
            [
                "model"  => $model,
                "errors" => $errors
            ]
        );

    }

    /**
     * @param int $error_code
     * @return string
     */
    public function actionError($error_code = 0)
    {
        return $error_code;
    }
}