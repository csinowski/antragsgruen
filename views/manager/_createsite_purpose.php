<?php
use app\models\forms\SiteCreateForm2;
use yii\helpers\Html;

/**
 * @var string[] $errors
 * @var SiteCreateForm2 $model
 * @var \Callable $t
 */

?>
<div class="step-pane active" id="panelPurpose" data-tab="stepPurpose">
    <fieldset class="wording">
        <?php
        if (count($errors) > 0) {
            echo '<div class="alert alert-danger" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>' . nl2br(Html::encode(implode("\n", $errors))) .
                '</div>';
        }
        ?>
        <legend><?=$t('purpose_title')?></legend>
        <div class="description"><?=$t('purpose_title')?></div>
        <div class="options">
            <label class="radio-label">
                <span class="title"><?=$t('purpose_motions')?></span>
                <span class="description"></span>
                <span class="input">
                    <?= Html::radio(
                        'SiteCreateForm2[wording]',
                        $model->wording == SiteCreateForm2::WORDING_MOTIONS,
                        ['value' => SiteCreateForm2::WORDING_MOTIONS, "data-wording-name" => "motion"]
                    ); ?>
                </span>
            </label>
            <label class="radio-label description-first">
                <span class="description"><?=$t('purpose_manifesto_desc')?></span>
                <span class="title"><?=$t('purpose_manifesto')?></span>
                <span class="input">
                    <?= Html::radio(
                        'SiteCreateForm2[wording]',
                        $model->wording == SiteCreateForm2::WORDING_MANIFESTO,
                        ['value' => SiteCreateForm2::WORDING_MANIFESTO, "data-wording-name" => "manifesto"]
                    ); ?>
                </span>
            </label>
        </div>
    </fieldset>
    <div class="navigation">
        <button class="btn btn-lg btn-next btn-primary"><span class="icon-chevron-right"></span> <?= $t('next') ?>
        </button>
    </div>
</div>
