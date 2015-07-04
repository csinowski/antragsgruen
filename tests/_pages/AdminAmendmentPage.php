<?php

namespace app\tests\_pages;

use yii\codeception\BasePage;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class AdminAmendmentPage extends BasePage
{
    public $route = 'admin/amendment/update';

    /**
     *
     */
    public function saveForm()
    {
        $this->actor->submitForm('#amendmentUpdateForm', [], 'save');
    }
}
