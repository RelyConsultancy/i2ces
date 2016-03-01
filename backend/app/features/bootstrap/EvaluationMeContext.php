<?php

use Behat\Gherkin\Node\TableNode;
use Symfony\Component\DomCrawler\Form;
use Oro\Bundle\TestFrameworkBundle\Test\Client;
use Oro\Bundle\TestFrameworkBundle\Test\BehatWebContext;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;


/**
 * Defines application features from the specific context.
 */
class EvaluationMeContext extends BehatWebContext
{
    protected $response;

    /**
     * @Given /^Login as an existing "([^"]*)" user and "([^"]*)" password$/
     */
    public function loginAsAnExistingUserAndPassword($user, $password)
    {
        /** @var Client $client */
        $client = self::getClientInstance();
        $header = \Oro\Bundle\TestFrameworkBundle\Test\WebTestCase::generateBasicAuthHeader($user, $password);
        //open default route
        $client->request('GET', $this->getUrl('oro_default'), array(), array(), $header);
        WebTestCase::assertHtmlResponseStatusCodeEquals($client->getResponse(), 200);
        PHPUnit_Framework_Assert::assertContains('Dashboard', $client->getCrawler()->html());
    }

    /**
     * @When /^I access "([^"]*)" endpoint$/
     */
    public function iAccessEndpoint($route)
    {
        $client = WebTestCase::getClientInstance();
        $client->request('GET', $this->getUrl($route));
        $this->response = ($client->getResponse());
    }

    /**
     * @Then /^I should get the response status "([^"]*)"$/
     */
    public function iGetEndpointResponse($status)
    {
        WebTestCase::assertHtmlResponseStatusCodeEquals($this->response, $status);
    }
}
