<?php

use app\components\UrlHelper;
use app\models\db\Motion;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var Motion $motion
 * @var string $mode
 * @var \app\controllers\Base $controller
 */

$this->title = Yii::t('motion', $mode == 'create' ? 'Start a Motion' : 'Edit Motion');

$controller = $this->context;
$controller->layoutParams->addBreadcrumb($this->title);
$controller->layoutParams->addBreadcrumb('Bestätigen');


echo '<h1>' . Yii::t('motion', 'Motion submitted') . '</h1>';

// @TODO
//echo $text = $antrag->veranstaltung->getStandardtext("antrag_eingereicht")->getHTMLText();

echo Html::beginForm(UrlHelper::createUrl('consultation/index'), 'post', ['id' => 'motionConfirmedForm']);
echo '<p><button type="submit" class="btn btn-success">Zurück zur Startseite</button></p>';
echo Html::endForm();