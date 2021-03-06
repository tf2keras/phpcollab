<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../tasks/deletetasks.php

use phpCollab\Assignments\Assignments;
use phpCollab\Projects\Projects;
use phpCollab\Tasks\Tasks;

$checkSession = "true";
include_once '../includes/library.php';


if (!isset($_GET["id"]) || $_GET["id"] == "") {
    phpCollab\Util::headerFunction($_SERVER['HTTP_REFERER']);
}
$id = $_GET["id"];

$tasks = new Tasks();
$assignments = new Assignments();
$projects = new Projects();

$strings = $GLOBALS["strings"];

if ($_GET["action"] == "delete") {
    $id = str_replace("**", ",", $id);

    $listTasks = $tasks->getTasksById($id);

    foreach ($listTasks as $listTask) {
        if ($fileManagement == "true") {
            phpCollab\Util::deleteDirectory("../files/" . $listTask["tas_project"] . "/" . $listTask["tas_id"]);
        }
    }
    $tasks->deleteTasks($id);
    $assignments->deleteAssignments($id);
    $tasks->deleteSubTasks($id);

    //recompute number of completed tasks of the project
    $projectDetail = $projects->getProjectById($listTasks["tas_project"][0]);

    phpCollab\Util::projectComputeCompletion(
        $listTasks->tas_project[$i],
        $tableCollab["projects"]
    );

    if ($project != "") {
        phpCollab\Util::headerFunction("../projects/viewproject.php?id=$project&msg=delete");
    } else {
        phpCollab\Util::headerFunction("../general/home.php?msg=delete");
    }
}

$projectDetail = $projects->getProjectById($project);

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
if ($project != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
    $blockPage->itemBreadcrumbs($strings["delete_tasks"]);
} else {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../general/home.php?", $strings["home"], "in"));
    $blockPage->itemBreadcrumbs($strings["my_tasks"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->form = "saP";
$block1->openForm("../tasks/deletetasks.php?project=$project&action=delete&id=$id");

$block1->heading($strings["delete_tasks"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);
$listTasks = $tasks->getTasksById($id);

foreach ($listTasks as $listTask) {
    echo '<tr class="odd"><td valign="top" class="leftvalue">#' . $listTask["tas_id"] . '</td><td>' . $listTask["tas_name"] . '</td></tr>';
}

echo <<< TR
<tr class="odd">
    <td valign="top" class="leftvalue">&nbsp;</td>
    <td><input type="submit" name="delete" value="{$strings["delete"]}"> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"></td>
</tr>
TR;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
