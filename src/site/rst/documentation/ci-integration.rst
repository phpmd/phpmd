==============
CI Integration
==============

PHPMD can be integrated into continuous integration (CI) pipelines to verify that each code change conforms to the configured rules.

GitHub Actions
==============

GitHub Actions is supported out of the box with its own PHPMD renderer called ``github``. This renderer will add annotations directly to your commits and pull requests right in the code.

A simple GitHub Actions workflow could look like this: ::

  name: CI
  
  on: push
  
  jobs:
    phpmd:
      name: PHPMD
      runs-on: ubuntu-latest
      steps:
        - name: Checkout
          uses: actions/checkout@v2
  
        - name: Setup PHP environment
          uses: shivammathur/setup-php@v2
          with:
            coverage: none
            tools: phpmd
  
        - name: Run PHPMD
          run: phpmd . github phpmd.ruleset.xml --exclude 'tests/*,vendor/*'

This assumes that you have a `custom rule set </documentation/creating-a-ruleset.html>`_ in the file ``phpmd.ruleset.xml``. Alternatively, you can of course list the rule sets manually.
