<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Behat\Behat\Tester\Exception\PendingException;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($session)
    {
    }

    /**
     * @Given I am on homepage
     */
    public function iAmOnHomepage()
    {
        throw new PendingException();
    }

    /**
     * @When I enter valid credentials
     */
    public function iEnterValidCredentials()
    {
        throw new PendingException();
    }

    /**
     * @Then I am taken to the dashboard
     */
    public function iAmTakenToTheDashboard()
    {
        throw new PendingException();
    }
}
