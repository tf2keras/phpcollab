<?php
namespace loggedIn;
use \AcceptanceTester;

/**
 * Class NewsdeskCest
 * @package loggedIn
 *
 * Tests performed as a general user
 */
class NewsdeskCest
{
    private $postId;
    private $commentId;

    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/general/login.php');
        $I->fillField(['name' => 'usernameForm'], 'testUser');
        $I->fillField(['name' => 'passwordForm'], 'testing');
        $I->click('input[type="submit"]');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param AcceptanceTester $I
     */
    public function listPosts(AcceptanceTester $I)
    {
        $I->wantTo('See a list of Newsdesk posts');
        $I->amOnPage('/newsdesk/listnews.php');
        $I->see('News list');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function viewPost(AcceptanceTester $I)
    {
        $I->wantTo('View a newsdesk post');
        $I->amOnPage('/newsdesk/listnews.php');
        $I->see('News list');
        $I->click('.listing tr:nth-child(2) td:nth-child(2) a');
        $I->see('Details');
        $I->see('Comments');
        $this->postId = $I->grabFromCurrentUrl('~id=(\d+)~');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function addComment(AcceptanceTester $I)
    {
        $I->wantTo('Add a comment to a news post');
        $I->amOnPage('/newsdesk/viewnews.php?id=' . $this->postId);
        $I->see('Newsdesk');
        $I->see('Comments');
        $I->amOnPage('/newsdesk/editmessage.php?postid=' . $this->postId);
        $I->see('Add a comment to the News Article');
        $I->submitForm('form', [
            'comment' => 'Codeception comment'
        ]);
        $I->see('Success : Addition succeeded', ['css' => '.message']);
        $I->see('Codeception comment', ['css' => '#clPrc']);
        $this->commentId = preg_replace("/[^0-9s]/", "", $I->grabAttributeFrom('#clPrc table.listing tr:last-child img', 'name'));

    }

    /**
     * @param AcceptanceTester $I
     */
    public function editComment(AcceptanceTester $I)
    {
        $I->wantTo('Edit my news post comment');
        $I->amOnPage('/newsdesk/viewnews.php?id=' . $this->postId);
        $I->see('Codeception comment', ['css' => '#clPrc']);
        $I->amGoingTo('Navigate to the edit view');
        $I->amOnPage('/newsdesk/editmessage.php?postid=' . $this->postId . '&id=' . $this->commentId);
        $I->see('Edit the comment of the News Article :');
        $I->submitForm('form', [
            'comment' => 'Codeception comment - edited'
        ]);
        $I->see('Success : Modification succeeded');
        $I->see('Codeception comment - edited', ['css' => '#clPrc']);
    }
}
