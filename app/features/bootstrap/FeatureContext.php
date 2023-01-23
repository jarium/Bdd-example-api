<?php

use app\Database;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

require_once __DIR__ . '/../../config.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private $response;
    private $responseData;

    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['http_errors' => false]);
    }

    /**
     * @When I make a GET request to :endpoint
     */
    public function iMakeGetRequestTo($endpoint)
    {
        $this->response = $this->client->get($endpoint);
        $this->responseData = json_decode($this->response->getBody(), true);
    }

    /**
     * @Given I make a POST request to :endpoint with body:
     */
    public function iMakePostRequestTo($endpoint, TableNode $tableNode)
    {
        $data = [];

        foreach ($tableNode->getRows() as $row) {
            $data[$row[0]] = $row[1];
        }

        $this->response = $this->client->post($endpoint, ['json' => $data]);
        $this->responseData = json_decode($this->response->getBody(), true);
    }

    /**
     * @Given I make a POST request to :endpoint with json :json
     */
    public function iMakeJsonPostRequestTo($endpoint, $json)
    {
        $this->response = $this->client->post($endpoint, ['json' => json_decode($json)]);
        $this->responseData = json_decode($this->response->getBody(), true);
    }

    /**
     * @Given I add :key header to request with value: :value
     */
    public function iAddHeader($key, $value)
    {
        $this->client = new Client([
            'http_errors' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                $key => $value
            ],
        ]);
    }

    /**
     * @Then the response status code should be :statusCode
     */
    public function responseStatusCodeShouldBe($statusCode)
    {
        $actualStatusCode = $this->response->getStatusCode();
        if ($actualStatusCode != $statusCode) {
            throw new \Exception(
                "Expected status code {$statusCode} but got {$actualStatusCode}."
            );
        }
    }

    /**
     * @Then the JSON node :node should have the value :value
     */
    public function theJsonNodeShouldHaveTheValue($node, $value)
    {
        $data = json_decode($this->response->getBody(), true);

        $this->checkNodeValue($data, $node, $value);
    }

    /**
     * @Then the JSON node :node should be true
     */
    public function theJsonNodeShouldBeTrue($node)
    {
        $data = json_decode($this->response->getBody(), true);

        $this->checkNodeValue($data, $node, true);
    }

    /**
     * Helper method to check the value of a JSON node, and is compatible with multidimensional JSON nodes
     */
    private function checkNodeValue($data, $node, $value)
    {
        $node = explode('.', $node);
        $current = &$data;
        foreach ($node as $key) {
            if (!isset($current[$key])) {
                throw new \Exception(sprintf('The node "%s" does not exist', implode('.', $node)));
            }
            $current = &$current[$key];
        }

        if ($current !== $value) {
            throw new \Exception(sprintf('The value of node "%s" is "%s", expected "%s"', implode('.', $node), $current, $value));
        }
    }

    /**
     * @Given /^I reset database$/
     */
    public function iResetDatabase()
    {
        $this->executeDbQueryWithFile(__DIR__ . '/../fixtures/init.sql');
    }

    /**
     * @Given I execute sql file :fileName on db
     */
    public function iExecuteSqlFile(string $fileName)
    {
        $this->executeDbQueryWithFile(__DIR__ . '/../fixtures/' . $fileName);
    }

    private function executeDbQueryWithFile(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new FileNotFoundException('file ' . $filePath . ' not found!');
        }

        $query = file_get_contents($filePath);

        (new Database())->rawQuery($query);
    }

    /**
     * @Then the following record at :tableName table must be existed:
     */
    public function theFollowingRecordAtTableMustBeExisted(string $tableName, TableNode $tableNode)
    {
        $statement = (new Database())->pdo->prepare("SELECT * FROM $tableName");
        $statement->execute();

        $recordsOfTable = $statement->fetchAll(PDO::FETCH_ASSOC);
        $expectedData = array_combine($tableNode->getRows()[0], $tableNode->getRows()[1]);

        $recordFound = false;

        foreach ($recordsOfTable as $record) {
            if (!array_diff_assoc($record, $expectedData)) {
                $recordFound = true;
            }
        }

        if (!$recordFound) {
            throw new NotFoundResourceException(
                "Record not found on the table!"
            );
        }
    }
}
