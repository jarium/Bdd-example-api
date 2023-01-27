Feature: Test General Qualifications

  Background:
    Given I reset database

    #Invalid bir endpointe get request atmayı deniyorum
  Scenario: Try to make get request to an invalid route
    When I make a GET request to 'http://docker.for.mac.localhost/api/invalid-route'
    Then the response status code should be 404
    And the JSON node 'meta.errorCode' should have the value '404001'
    And the JSON node 'meta.errorMessage' should have the value 'route not found!'

    #Invalid bir endpointe post request atmayı deniyorum
  Scenario: Try to make post request to an invalid route
    When I make a POST request to 'http://docker.for.mac.localhost/api/invalid-route' with json '{"key" : "value"}'
    Then the response status code should be 404
    And the JSON node 'meta.errorCode' should have the value '404001'
    And the JSON node 'meta.errorMessage' should have the value 'route not found!'

    #Invalid bir json body ile post request atmayı deniyorum
  Scenario: Try to make post request to valid route with invalid json
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with json '{}'
    Then the response status code should be 400
    And the JSON node 'meta.errorCode' should have the value '400004'
    And the JSON node 'meta.errorMessage' should have the value 'Invalid json'

