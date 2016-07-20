Feature: A Console Command exists to remove old tokens
  Invalid tokens (expired or used) needs to be removed.
  A Console Command ease to remove all tokens

  Scenario:  run the cleaner
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
