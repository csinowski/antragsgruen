<?php
/**
 * @var \yii\web\View $this
 * @var string $content
 */

$this->beginContent('@app/views/layouts/main.php');

/** @var \app\controllers\Base $controller */
$controller = $this->context;
$params     = $controller->layoutParams;

$row_classes = array("row", "antragsgruen-content");

/*
if (isset($controller->text_comments) && $controller->text_comments) {
    $row_classes[] = "text_comments";
}
*/

$menus = array();
if ($params->menu) {
    $menus[] = array("name" => "Aktionen", "items" => $controller->layoutParams->menu);
}
foreach ($params->multimenu as $m) {
    $menus[] = $m;
}
/*
foreach ($menus as $menu) {
$this->widget('bootstrap.widgets.TbMenu', array(
    'type'  => 'list',
    'items' => array_merge(array(
        array('label' => $menu["name"]),
    ), $menu["items"]),
));
}
*/

?>


    <div class="<?= implode(" ", $row_classes) ?>">
        <div class="col-md-9 well">
            <?php echo $content; ?>
        </div>
        <div class="col-md-3" id="sidebar">
            <?= $params->presidebarHtml ?>
            <div class="well visible-desktop">
                <?= implode("", $params->menusHtml) ?>
            </div>
        </div>
    </div>

<?php $this->endContent();