<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23
** Path by root: ../preferences/updatepassword.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: updatepassword.php
**
** DESC: Screen:
**
** HISTORY:
** 	2003-10-23	-	added new document info
**	2003-10-27	-	session problem fixed
**  2004-08-23  -   session check for older php
** -----------------------------------------------------------------------------
** TO-DO:
** move to a better login system and authentication (try to db session)
**
** =============================================================================
*/


use phpCollab\Members\Members;
use phpCollab\Teams\Teams;

$checkSession = "true";
include_once '../includes/library.php';

$members = new Members();
$teams = new Teams();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST["action"] == "update") {

        $oldPassword = $_POST["old_password"];
        $confirmPassword = $_POST["confirm_password"];
        $newPassword = $_POST["new_password"];

        $r = substr($oldPassword, 0, 2);
        $oldPassword = crypt($oldPassword, $r);

        if ($oldPassword != $passwordSession) {
            $error = $strings["old_password_error"];
        } else {
            if (empty($newPassword) || $newPassword != $confirmPassword) {
                $error = $strings["new_password_error"];
            } else {
                // Encrypt new password
                $encryptedPassword = phpCollab\Util::getPassword($newPassword);

                if ($htaccessAuth == "true") {
                    $Htpasswd = new Htpasswd;

                    $myTeams = $teams->getTeamByMemberId($idSession);

                    if (!empty($myTeams)) {
                        foreach ($myTeams as $thisTeam) {
                            try {
                                $Htpasswd->initialize("../files/" . $thisTeam["tea_pro_id"] . "/.htpasswd");
                                $Htpasswd->changePass($loginSession, $encryptedPassword);
                            }
                            catch (Exception $e) {
                                echo "Error: " . $e->getMessage();
                            }
                        }
                    }
                }

                try {
                    $members->setPassword($idSession, $newPassword);
                }
                catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }

                //if mantis bug tracker enabled
                if ($enableMantis == "true") {
                    // call mantis function to reset user password
                    include("../mantis/user_reset_pwd.php");
                }

                $r = substr($newPassword, 0, 2);
                $newPassword = crypt($newPassword, $r);
                $passwordSession = $newPassword;

                $_SESSION['passwordSession'] = $passwordSession;

                phpCollab\Util::headerFunction("../preferences/updateuser.php?msg=update");
            }
        }
    }
}

$userDetail = $members->getMemberById($idSession);

if (empty($userDetail)) {
    phpCollab\Util::headerFunction("../users/listusers.php?msg=blankUser");
}

$bodyCommand = 'onLoad="document.change_passwordForm.original_password.focus();"';
include APP_ROOT . '/themes/' . THEME . '/header.php';


$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($strings["preferences"]);
if ($notifications == "true") {
    $blockPage->itemBreadcrumbs(
        $blockPage->buildLink(
            "../preferences/updateuser.php?", $strings["user_profile"], "in") .
        " | " . $strings["change_password"] .
        " | " . $blockPage->buildLink("../preferences/updatenotifications.php?",
            $strings["notifications"], "in")
    );
} else {
    $blockPage->itemBreadcrumbs(
        $blockPage->buildLink("../preferences/updateuser.php?",
            $strings["user_profile"], "in") . " | " . $strings["change_password"]
    );
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "change_password";
$block1->openForm("../preferences/updatepassword.php");

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["change_password"] . " : " . $userDetail["mem_login"]);

$block1->openContent();
$block1->contentTitle($strings["change_password_intro"]);

$block1->contentRow("* " . $strings["old_password"], '<input style="width: 150px;" type="password" name="old_password" value="" autocomplete="off">');
$block1->contentRow("* " . $strings["new_password"], '<input style="width: 150px;" type="password" name="new_password" value="" autocomplete="off">');
$block1->contentRow("* " . $strings["confirm_password"], '<input style="width: 150px;" type="password" name="confirm_password" value="" autocomplete="off">');
$block1->contentRow("", '<input type="hidden" name="action" value="update" /><input type="submit" name="Save" value="' . $strings["save"] . '">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
