Feature: Test Create Post Endpoint

  Background:
    Given I reset database

  #Tokensız istek
  Scenario: Try to create post without a token
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value                                                                                                                                                                                                                                                                                                                                                         |
      | title    | Everybody Loves                                                                                                                                                                                                                                                                                                                                               |
      | content  | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum |
      | category | Social                                                                                                                                                                                                                                                                                                                                                        |
    Then the response status code should be 401
    And the JSON node 'meta.errorCode' should have the value '401001'
    And the JSON node 'meta.errorMessage' should have the value 'Invalid token parameter'

  #Geçersiz tokenla istek
  Scenario: Try to create post with an invalid token
    When I execute sql file "posts_token_active.sql" on db
    And I add "X-Posts-Token" header to request with value: "123invalidtoken"
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value                                                                                                                                                                                                                                                                                                                                                         |
      | title    | Everybody Loves                                                                                                                                                                                                                                                                                                                                               |
      | content  | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum |
      | category | Social                                                                                                                                                                                                                                                                                                                                                        |
    Then the response status code should be 401
    And the JSON node 'meta.errorCode' should have the value '401002'
    And the JSON node 'meta.errorMessage' should have the value 'Token not found!'

  #Pasif statuslu tokenla istek
  Scenario: Try to create post with a passive token
    When I execute sql file "posts_token_passive.sql" on db
    And I add "X-Posts-Token" header to request with value: "7fa772744e30a916ce0008553092a834ebe94009542095d625798aba79ffc785"
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value                                                                                                                                                                                                                                                                                                                                                         |
      | title    | Everybody Loves                                                                                                                                                                                                                                                                                                                                               |
      | content  | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum |
      | category | Social                                                                                                                                                                                                                                                                                                                                                        |
    Then the response status code should be 401
    And the JSON node 'meta.errorCode' should have the value '401003'
    And the JSON node 'meta.errorMessage' should have the value 'Invalid token parameter'

  #Expired statuslu tokenla istek
  Scenario: Try to create post with an expired token
    When I execute sql file "posts_token_expired.sql" on db
    And I add "X-Posts-Token" header to request with value: "7fa772744e30a916ce0008553092a834ebe94009542095d625798aba79ffc785"
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value                                                                                                                                                                                                                                                                                                                                                         |
      | title    | Everybody Loves                                                                                                                                                                                                                                                                                                                                               |
      | content  | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum |
      | category | Social                                                                                                                                                                                                                                                                                                                                                        |
    Then the response status code should be 401
    And the JSON node 'meta.errorCode' should have the value '401004'
    And the JSON node 'meta.errorMessage' should have the value 'Token expired!'

  #Tarihi geçmiş bir tokenla istek
  Scenario: Try to create post with an outdated token
    And I execute sql file "posts_token_outdated.sql" on db
    And I add "X-Posts-Token" header to request with value: "7fa772744e30a916ce0008553092a834ebe94009542095d625798aba79ffc785"
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value                                                                                                                                                                                                                                                                                                                                                         |
      | title    | Everybody Loves                                                                                                                                                                                                                                                                                                                                               |
      | content  | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum |
      | category | Social                                                                                                                                                                                                                                                                                                                                                        |
    Then the response status code should be 401
    And the JSON node 'meta.errorCode' should have the value '401005'
    And the JSON node 'meta.errorMessage' should have the value 'Token expired!'
    And the following record at "api_token" table must be existed:
      | id | admin_id | token                                                            | status | created_at          | expired_at          | updated_at          |
      | 1  | 1        | 7fa772744e30a916ce0008553092a834ebe94009542095d625798aba79ffc785 | 2      | 2023-01-22 23:07:43 | 2023-01-22 00:07:43 | 2023-01-22 01:00:00 |

  #Token doğru, title paramsız istek
  Scenario: Try to create post without title param and with a valid token
    When I execute sql file "posts_token_active.sql" on db
    And I add "X-Posts-Token" header to request with value: "7fa772744e30a916ce0008553092a834ebe94009542095d625798aba79ffc785"
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value                                                                                                                                                                                                                                                                                                                                                         |
      | title    |                                                                                                                                                                                                                                                                                                                                                               |
      | content  | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum |
      | category | Social                                                                                                                                                                                                                                                                                                                                                        |
    Then the response status code should be 400
    And the JSON node 'meta.errorCode' should have the value '400001'
    And the JSON node 'meta.errorMessage' should have the value 'title param is required'

  #Token doğru, title uzunluk limitini geçiyor.
  Scenario: Try to create post with invalid title param and with a valid token
    When I execute sql file "posts_token_active.sql" on db
    And I add "X-Posts-Token" header to request with value: "7fa772744e30a916ce0008553092a834ebe94009542095d625798aba79ffc785"
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value                                                                                                                                                                                                                                                                                                                                                         |
      | title    | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum |
      | content  | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum |
      | category | Social                                                                                                                                                                                                                                                                                                                                                        |
    Then the response status code should be 400
    And the JSON node 'meta.errorCode' should have the value '400001'
    And the JSON node 'meta.errorMessage' should have the value 'title param cannot be longer than 256 characters'

  #Token doğru, content paramsız istek.
  Scenario: Try to create post without content param and with a valid token
    When I execute sql file "posts_token_active.sql" on db
    And I add "X-Posts-Token" header to request with value: "7fa772744e30a916ce0008553092a834ebe94009542095d625798aba79ffc785"
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value           |
      | title    | Everybody Loves |
      | content  |                 |
      | category | Social          |
    Then the response status code should be 400
    And the JSON node 'meta.errorCode' should have the value '400002'
    And the JSON node 'meta.errorMessage' should have the value 'content param is required'

  #Token doğru, content param uzunluk limitini geçiyor.
  Scenario: Try to create post with invalid content param and with a valid token
    When I execute sql file "posts_token_active.sql" on db
    And I add "X-Posts-Token" header to request with value: "7fa772744e30a916ce0008553092a834ebe94009542095d625798aba79ffc785"
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
      | title    | Everybody Loves                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      |
      | content  | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque non elit diam. Fusce et arcu malesuada, tristique ante gravida, scelerisque libero. In vehicula finibus dolor, eget sollicitudin tortor lobortis non. Nulla ut quam pulvinar, molestie libero eget, ultricies eros. Mauris malesuada et sapien suscipit pulvinar. In quis nibh dui. Suspendisse potenti. Vivamus quis purus quis odio fringilla tincidunt in sit amet odio. Donec facilisis in magna id euismod. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer fringilla vestibulum nunc. Nunc sed nunc molestie, hendrerit enim eu, lobortis nunc. Fusce at porta metus. Ut vel dapibus nulla, quis molestie purus. Pellentesque in ligula arcu. Sed auctor metus efficitur nisl finibus ultricies. Mauris purus nisl, eleifend ac pulvinar sed, porta vitae diam. Integer fringilla risus quis pellentesque iaculis. Suspendisse potenti. Donec tellus est, accumsan in tempus eu, ullamcorper et elit. Donec ultricies, nisi eget congue ultricies, turpis tellus ultrices risus, sed condimentum ligula orci vitae orci. Nunc quis mattis diam. Nullam pretium eleifend erat, porta pharetra quam fermentum eu. Integer quis eleifend tortor. In quis eros nibh. In ultrices neque vel ex fermentum facilisis. Nam eget tristique justo. Donec eu risus sed enim posuere rutrum eu quis erat. Aenean nec mi eu libero aliquam accumsan. In hac habitasse platea dictumst. Nullam vel dolor at diam feugiat tristique sit amet quis magna. Vivamus in semper libero. Donec sit amet sapien nec libero luctus placerat vitae blandit risus. Etiam vehicula enim vitae odio malesuada, in placerat libero laoreet. Vivamus molestie vitae lorem at tincidunt. Maecenas nisl dolor, ultrices ac euismod in, rhoncus sed nisi. Aliquam leo libero, dictum nec libero eget, cursus efficitur orci. Curabitur tellus purus, dapibus eget erat eu, venenatis mattis neque. Nam sit amet massa congue, aliquet quam non, vestibulum tortor. Phasellus iaculis nisi a massa porta luctus. Fusce consectetur tempor odio. |
      | category | Social                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               |
    Then the response status code should be 400
    And the JSON node 'meta.errorCode' should have the value '400002'
    And the JSON node 'meta.errorMessage' should have the value 'content param cannot be longer than 2048 characters'

  #Token doğru, category paramsız istek.
  Scenario: Try to create post without category param and with a valid token
    When I execute sql file "posts_token_active.sql" on db
    And I add "X-Posts-Token" header to request with value: "7fa772744e30a916ce0008553092a834ebe94009542095d625798aba79ffc785"
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value                                                                                                                                                                                                                                                                                                                                                         |
      | title    | Everybody Loves                                                                                                                                                                                                                                                                                                                                               |
      | content  | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum |
      | category |                                                                                                                                                                                                                                                                                                                                                               |
    Then the response status code should be 400
    And the JSON node 'meta.errorCode' should have the value '400003'
    And the JSON node 'meta.errorMessage' should have the value 'category param is required'

  #Token doğru, category param uzunluk limitini geçiyor.
  Scenario: Try to create post with invaid category param and with a valid token
    When I execute sql file "posts_token_active.sql" on db
    And I add "X-Posts-Token" header to request with value: "7fa772744e30a916ce0008553092a834ebe94009542095d625798aba79ffc785"
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value                                                                                                                                                                                                                                                                                                                                                         |
      | title    | Everybody Loves                                                                                                                                                                                                                                                                                                                                               |
      | content  | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum |
      | category | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque non elit diam.                                                                                                                                                                                                                                                                               |
    Then the response status code should be 400
    And the JSON node 'meta.errorCode' should have the value '400003'
    And the JSON node 'meta.errorMessage' should have the value 'category param cannot be longer than 64 characters'

  #Token doğru, category param doğru değil.
  Scenario: Try to create post with not allowed category param and with a valid token
    When I execute sql file "posts_token_active.sql" on db
    And I add "X-Posts-Token" header to request with value: "7fa772744e30a916ce0008553092a834ebe94009542095d625798aba79ffc785"
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value                                                                                                                                                                                                                                                                                                                                                         |
      | title    | Everybody Loves                                                                                                                                                                                                                                                                                                                                               |
      | content  | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum |
      | category | SomeCategory                                                                                                                                                                                                                                                                                                                                                  |
    Then the response status code should be 400
    And the JSON node 'meta.errorCode' should have the value '400003'
    And the JSON node 'meta.errorMessage' should have the value 'category param must be one of the following: Science, Health, Politicial, Technology, World, Economy, Sports, Art, Education, Social'

  #Doğru istek
  Scenario: Try to create post with valid params
    When I execute sql file "posts_token_active.sql" on db
    And I add "X-Posts-Token" header to request with value: "7fa772744e30a916ce0008553092a834ebe94009542095d625798aba79ffc785"
    When I make a POST request to 'http://docker.for.mac.localhost/api/create-post' with body:
      | key      | value                                                                                                                                                                                                                                                                                                                                                         |
      | title    | Everybody Loves                                                                                                                                                                                                                                                                                                                                               |
      | content  | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum |
      | category | Social                                                                                                                                                                                                                                                                                                                                                        |
    Then the response status code should be 200
    And the JSON node 'meta.success' should be true
    And the following record at "posts" table must be existed:
      | id | admin_id | title           | content                                                                                                                                                                                                                                                                                                                                                       | category | status | created_at          | updated_at |
      | 1  | 1        | Everybody Loves | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor urna id convallis tincidunt. Quisque interdum molestie dolor non pretium. Donec posuere diam id metus mollis, vitae elementum turpis tempus. Curabitur ultrices diam quam, non egestas nisi eleifend sed. Etiam neque ligula, cursus at libero et, accumsan iaculis risus. Vestibulum | Social   | 1      | 2023-01-22 01:00:00 |            |



