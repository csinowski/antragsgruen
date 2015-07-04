<?php

use app\components\LineSplitter;
use app\models\db\ConsultationMotionType;
use app\models\db\Motion;
use \app\components\opendocument\Spreadsheet;

/**
 * @var $this yii\web\View
 * @var Motion[] $motions
 * @var bool $textCombined
 * @var ConsultationMotionType $motionType
 */

/** @var \app\controllers\Base $controller */
$controller   = $this->context;
$consultation = $controller->consultation;

$DEBUG = false;

/** @var \app\models\settings\AntragsgruenApp $params */
$params = \yii::$app->params;

$tmpZipFile   = $params->tmpDir . uniqid("zip-");
$templateFile = \yii::$app->basePath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'OpenOffice-Template.ods';
copy($templateFile, $tmpZipFile);

$zip = new ZipArchive();
if ($zip->open($tmpZipFile) !== true) {
    die("cannot open <$tmpZipFile>\n");
}

$content = $zip->getFromName('content.xml');
$doc     = new Spreadsheet($content);


$currCol = $firstCol = 1;


$hasTags = ($consultation->tags > 0);


$COL_PREFIX    = $currCol++;
$COL_INITIATOR = $currCol++;
$COL_TEXTS     = [];
if ($textCombined) {
    $COL_TEXTS[] = $currCol++;
} else {
    foreach ($motionType->motionSections as $section) {
        $COL_TEXTS[$section->id] = $currCol++;
    }
}
if ($hasTags) {
    $COL_TAGS = $currCol++;
}
$COL_CONTACT   = $currCol++;
$COL_PROCEDURE = $currCol++;


// Title

$doc->setCell(1, $firstCol, Spreadsheet::TYPE_TEXT, 'Antragsübersicht');
$doc->setCellStyle(1, $firstCol, [], [
    'fo:font-size'   => '16pt',
    'fo:font-weight' => 'bold',
]);
$doc->setMinRowHeight(1, 1.5);


// Heading

$doc->setCell(2, $COL_PREFIX, Spreadsheet::TYPE_TEXT, 'Antragsnr.');
$doc->setCellStyle(2, $COL_PREFIX, [], ["fo:font-weight" => "bold"]);

$doc->setCell(2, $COL_INITIATOR, Spreadsheet::TYPE_TEXT, 'AntragstellerIn');
$doc->setColumnWidth($COL_INITIATOR, 6);

if ($textCombined) {
    $doc->setCell(2, $COL_TEXTS[0], Spreadsheet::TYPE_TEXT, 'Text');
    $doc->setColumnWidth($COL_TEXTS[0], 10);
} else {
    foreach ($motionType->motionSections as $section) {
        $doc->setCell(2, $COL_TEXTS[$section->id], Spreadsheet::TYPE_TEXT, $section->title);
        $doc->setColumnWidth($COL_TEXTS[$section->id], 10);
    }
}
if (isset($COL_TAGS)) {
    $doc->setCell(2, $COL_TAGS, Spreadsheet::TYPE_TEXT, 'Schlagworte');
    $doc->setColumnWidth($COL_TAGS, 6);
}
$doc->setCell(2, $COL_CONTACT, Spreadsheet::TYPE_TEXT, 'Kontakt');
$doc->setColumnWidth($COL_CONTACT, 6);
$doc->setCell(2, $COL_PROCEDURE, Spreadsheet::TYPE_TEXT, 'Verfahren');
$doc->setColumnWidth($COL_PROCEDURE, 6);

$doc->drawBorder(1, $firstCol, 2, $COL_PROCEDURE, 1.5);

// Motions


// @TODO


$content = $doc->create();

if ($DEBUG) {
    $doc->debugOutput();
}

$zip->deleteName("content.xml");
$zip->addFromString("content.xml", $content);
$zip->close();


readfile($tmpZipFile);
unlink($tmpZipFile);