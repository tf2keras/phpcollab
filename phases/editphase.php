<?php

use phpCollab\Phases\Phases;
use phpCollab\Projects\Projects;
use phpCollab\Tasks\Tasks;
use phpCollab\Teams\Teams;

$checkSession = "true";
include_once '../includes/library.php';
include '../includes/customvalues.php';

$phases = new Phases();
$projects = new Projects();
$teams = new Teams();
$tasks = new Tasks();

$phaseDetail = $phases->getPhasesById($id);

$projectdetail = $projects->getProjectById($project);

$teamMember = $teams->isTeamMember($project, $idSession);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == "update") {
        $comments = phpCollab\Util::convertData($_POST["comments"]);
        $start_date = $_POST["start_date"];
        $end_date = $_POST["end_date"];
        $status = $_POST["status"];

        if ($start_date == 0 || $start_date == 1) {
            $end_date = "--";
        }

        if ($start_date == 2 && $end_date == "--") {
            $end_date = date('Y-m-d');
        }

        try {
            $phases->updatePhase($id, $status, $start_date, $end_date, $comments);
        } catch (Exception $e) {

        }

        if ($status != 1) {
            $changeTasks = $tasks->getOpenPhaseTasks($id);

            foreach ($changeTasks as $task) {
                $tasks->setTaskStatus($task["tas_id"], 4);
            }
        }
        phpCollab\Util::headerFunction("../phases/viewphase.php?id=$id");
    }
}

$includeCalendar = true; //Include Javascript files for the pop-up calendar

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($phaseDetail["pha_name"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

//set value in form
$start_date = $phaseDetail["pha_date_start"];

$end_date = $phaseDetail["pha_date_end"];
$comments = $phaseDetail["pha_comments"];

$block1 = new phpCollab\Block();
$block1->form = "pdD";
$block1->headingToggle($strings["phase"] . " : " . $phaseDetail["pha_name"]);

echo <<<FORM
<a id="filedetailsAnchor"></a>
<form method="POST" action="../phases/editphase.php?id=$id&action=update&#filedetailsAnchor" name="filedetailsForm" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
    <input type="hidden" name="maxCustom" value="{$projectDetail["pro_upload_max"]}">
FORM;
$block1->openContent();
$block1->contentTitle($strings["details"]);
$block1->form = "filedetails";

echo <<<TR
    <tr class="odd">
        <td class="leftvalue">{$strings["name"]} :</td>
        <td>{$phaseDetail["pha_name"]}</td>
    </tr>
    <tr class="odd">
        <td class="leftvalue">{$strings["phase_id"]} :</td>
        <td>{$phaseDetail["pha_id"]}</td>
    </tr>
    <tr class="odd">
        <td class="leftvalue">{$strings["status"]} :</td>
        <td><select name="status">
TR;

$comptSta = count($phaseStatus);

for ($i = 0; $i < $comptSta; $i++) {
    if ($phaseDetail["pha_status"] == $i) {
        echo "<option value='$i' selected>$phaseStatus[$i]</option>";
    } else {
        echo "<option value='$i'>$phaseStatus[$i]</option>";
    }
}

echo "</select></td></tr>";

if (empty($start_date)) {
    $start_date = $date;
}
if (empty($end_date)) {
    $end_date = "--";
}

$block1->contentRow($strings["date_start"], "<input type='text' name='start_date' id='start_date' size='20' value='$start_date'><input type='button' value=' ... ' id='trigStartDate'>");

echo <<<JAVASCRIPT
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'start_date',
        button         :    'trigStartDate',
        {$calendar_common_settings}
    })
</script>
JAVASCRIPT;

$block1->contentRow($strings["date_end"], "<input type='text' name='end_date' id='end_date' size='20' value='{$end_date}'><input type='button' value=' ... ' id='trigDateEnd'>");

echo <<<JAVASCRIPT
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'end_date',
        button         :    'trigDateEnd',
        {$calendar_common_settings}
    })
</script>
JAVASCRIPT;

echo <<<HTML
<tr class="odd">
    <td class="leftvalue">{$strings["comments"]} :</td>
    <td>
        <textarea rows="3" style="width: 400px; height: 100px;" name="comments" cols="43">{$comments}</textarea>
    </td>
</tr>
<tr class="odd">
    <td class="leftvalue">&nbsp;</td>
    <td><input type="submit" value="{$strings["save"]}"></td>
</tr>
HTML;

$block1->closeContent();
$block1->closeToggle();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
