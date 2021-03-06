<?php
namespace loggedIn;
use \AcceptanceTester;
use Exception;

class SearchCest
{
    private $searchTerm = 'test';

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
    public function listSearch(AcceptanceTester $I)
    {
        $I->wantTo('See a list of Search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('.xdebug-error');
        $I->dontSeeElement('.xdebug-var-dump');
        $I->dontSee('Fatal error');
        $I->dontSee('Warning');
    }

    /**
     * @param AcceptanceTester $I
     *
     */
    public function generalSearchNoResults(AcceptanceTester $I)
    {
        $I->wantTo('Perform a general search with no results');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('h1.headingError');
        $I->submitForm('form', [
            'searchfor' => 'codeception'
        ]);
        $I->seeInCurrentUrl('/search/resultssearch.php');
        $I->see('Search results for keywords : codeception');
        $I->see('The search returned no results.');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function generalSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a general search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('h1.headingError');
        $I->submitForm('form', [
            'searchfor' => $this->searchTerm
        ]);
        $I->see('Search results for keywords : ' . $this->searchTerm);
        $I->dontSee('The search returned no results.');
        $I->seeInCurrentUrl('/search/resultssearch.php');
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function notesSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Notes search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('h1.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Notes');
        $I->click('input[type="submit"]');
        $I->dontSee('The search returned no results.');
        $I->see('Search results for keywords : ' . $this->searchTerm);
        $I->see('Search Results : Notes', ['css' => 'h1.heading']);
        $I->seeInCurrentUrl('/search/resultssearch.php');
        $I->seeNumberOfElements('h1.heading', 1);
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function clientOrganizationsSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Client Organization search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('h1.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Client Organizations');
        $I->click('input[type="submit"]');
        $I->dontSee('The search returned no results.');
        $I->see('Search results for keywords : ' . $this->searchTerm);
        $I->see('Search Results : Client Organizations', ['css' => 'h1.heading']);
        $I->seeInCurrentUrl('/search/resultssearch.php');
        $I->seeNumberOfElements('h1.heading', 1);
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function projectsSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Projects search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('h1.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Projects');
        $I->click('input[type="submit"]');
        $I->dontSee('The search returned no results.');
        $I->see('Search results for keywords : ' . $this->searchTerm);
        $I->see('Search Results : Projects', ['css' => 'h1.heading']);
        $I->seeInCurrentUrl('/search/resultssearch.php');
        $I->seeNumberOfElements('h1.heading', 1);
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function tasksSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Tasks search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('h1.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Tasks');
        $I->click('input[type="submit"]');
        $I->dontSee('The search returned no results.');
        $I->see('Search results for keywords : ' . $this->searchTerm);
        $I->see('Search Results : Tasks', ['css' => 'h1.heading']);
        $I->seeInCurrentUrl('/search/resultssearch.php');
        $I->seeNumberOfElements('h1.heading', 1);
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function subtasksSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Subtasks search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('h1.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Subtasks');
        $I->click('input[type="submit"]');
        $I->dontSee('The search returned no results.');
        $I->see('Search results for keywords : ' . $this->searchTerm);
        $I->see('Search Results : Subtasks', ['css' => 'h1.heading']);
        $I->seeInCurrentUrl('/search/resultssearch.php');
        $I->seeNumberOfElements('h1.heading', 1);
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function discussionsSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Discussions search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('h1.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Discussions');
        $I->click('input[type="submit"]');
        $I->dontSee('The search returned no results.');
        $I->see('Search results for keywords : ' . $this->searchTerm);
        $I->see('Search Results : Discussions', ['css' => 'h1.heading']);
        $I->seeInCurrentUrl('/search/resultssearch.php');
        $I->seeNumberOfElements('h1.heading', 1);
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function usersSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Users search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('h1.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Users');
        $I->click('input[type="submit"]');
        $I->dontSee('The search returned no results.');
        $I->see('Search results for keywords : ' . $this->searchTerm);
        $I->see('Search Results : Users', ['css' => 'h1.heading']);
        $I->seeInCurrentUrl('/search/resultssearch.php');
        $I->seeNumberOfElements('h1.heading', 1);
    }
}
