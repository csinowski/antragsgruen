<?php
use app\models\forms\SiteCreateForm2;
use yii\helpers\Html;

/**
 * @var SiteCreateForm2 $model
 * @var \Callable $t
 */

?>
<div class="step-pane active" id="panelMotionWho" data-tab="stepMotion">
    <fieldset class="motionWho">
        <legend>
            <span class="only-motion"><?= $t('motwho_mot_title') ?></span>
            <span class="only-manifesto"><?= $t('motwho_man_title') ?></span>
        </legend>
        <div class="description">&nbsp;</div>
        <div class="options">
            <label class="radio-label">
                <span class="title"><?= $t('motwho_admins') ?></span>
                <span class="description"></span>
                <span class="input">
                    <?= Html::radio(
                        'SiteCreateForm2[motionsInitiatedBy]',
                        $model->motionsInitiatedBy == SiteCreateForm2::MOTION_INITIATED_ADMINS,
                        ['value' => SiteCreateForm2::MOTION_INITIATED_ADMINS]
                    ); ?>
                </span>
            </label>
            <label class="radio-label two-lines">
                <span class="title long"><?= $t('motwho_loggedin') ?></span>
                <span class="description"></span>
                <span class="input">
                    <?= Html::radio(
                        'SiteCreateForm2[motionsInitiatedBy]',
                        $model->motionsInitiatedBy == SiteCreateForm2::MOTION_INITIATED_LOGGED_IN,
                        ['value' => SiteCreateForm2::MOTION_INITIATED_LOGGED_IN]
                    ); ?>
                </span>
            </label>
            <label class="radio-label">
                <span class="title"><?= $t('motwho_all') ?></span>
                <span class="description"></span>
                <span class="input">
                    <?= Html::radio(
                        'SiteCreateForm2[motionsInitiatedBy]',
                        $model->motionsInitiatedBy == SiteCreateForm2::MOTION_INITIATED_ALL,
                        ['value' => SiteCreateForm2::MOTION_INITIATED_ALL]
                    ); ?>
                </span>
            </label>
        </div>
    </fieldset>
    <div class="navigation">
        <button class="btn btn-lg btn-prev"><span class="icon-chevron-left"></span> <?= $t('prev') ?></button>
        <button class="btn btn-lg btn-next btn-primary"><span class="icon-chevron-right"></span> <?= $t('next') ?>
        </button>
    </div>
</div>
