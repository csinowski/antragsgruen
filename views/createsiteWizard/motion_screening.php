<?php
use yii\helpers\Html;

/**
 * @var \app\models\forms\SiteCreateForm $model
 * @var \Callable $t
 */

?>
<div class="step-pane active" id="panelMotionScreening" data-tab="stepMotions">
    <fieldset class="motionScreening">
        <legend>
            <span class="only-motion"><?= $t('screening_mot_title') ?></span>
            <span class="only-manifesto"><?= $t('screening_man_title') ?></span>
        </legend>
        <div class="description"><?= $t('screening_desc') ?></div>
        <div class="options">
            <label class="radio-label two-lines value-0">
                <span class="title"><?= $t('screening_no') ?></span>
                <span class="description"></span>
                <span class="input">
                    <?= Html::radio('SiteCreateForm[motionScreening]', !$model->motionScreening, ['value' => 0]); ?>
                </span>
            </label>
            <label class="radio-label two-lines value-1">
                <span class="title"><?= $t('screening_yes') ?></span>
                <span class="description"></span>
                <span class="input">
                    <?= Html::radio('SiteCreateForm[motionScreening]', $model->motionScreening, ['value' => 1]); ?>
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
