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

GitLab Code Quality Reporting
=========

GitLab Code Quality reporting is supported out of the box with its own PHPMD renderer called ``gitlab``. You can read the GitLab docs about this topic `here <https://docs.gitlab.com/ee/user/project/merge_requests/code_quality.html>`_.

A simple GitLab Code Quality report workflow could look like this: ::

  mess_detection:
      image: ubuntu-latest
      stage: quality
      script:
        - phpmd . gitlab phpmd.ruleset.xml > phpmd-report.json
      artifacts:
        reports:
          codequality: phpmd-report.json

