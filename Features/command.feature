Feature: A Console Command exists to remove old tokens
  Invalid tokens (expired or used) needs to be removed.
  A Console Command ease to remove all tokens

  Scenario:  I want to rotate a key that do not exist
    When I run command "spomky-labs:jose:rotate-keys" with parameters
    """
    {
        "key": "jose.key.key10",
        "ttl": 1800
    }
    """
    Then The command exit code should be null
    And I should see
    """
    The key "jose.key.key10" does not exist and will be created.

    """

  Scenario:  I want to rotate a key that is not expired
    When I run command "spomky-labs:jose:rotate-keys" with parameters
    """
    {
        "key": "jose.key.key10",
        "ttl": 1800
    }
    """
    Then The command exit code should be null
    And I should see
    """
    The key "jose.key.key10" exists and is not expired.

    """

  Scenario:  I want to rotate a key that expired
    Given I wait 10 seconds
    When I run command "spomky-labs:jose:rotate-keys" with parameters
    """
    {
        "key": "jose.key.key10",
        "ttl": 5
    }
    """
    Then The command exit code should be null
    And I should see
    """
    The key "jose.key.key10" exists but expired. It will be updated.

    """
