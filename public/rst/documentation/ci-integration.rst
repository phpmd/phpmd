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
          uses: actions/checkout@v4

        - name: Setup PHP environment
          uses: shivammathur/setup-php@v2
          with:
            coverage: none
            tools: phpmd

        - name: Run PHPMD
          run: phpmd analyze --format github .

This assumes that you have a `custom rule set </documentation/creating-a-ruleset.html>`_ such as ``phpmd.yml`` in the root of your repository. PHPMD will detect it automatically. Exclude patterns (e.g. for ``vendor/``) can be configured directly in the rule set file.

Auto-detection
--------------

When PHPMD detects it is running inside GitHub Actions (via the ``GITHUB_ACTIONS`` environment variable), it will automatically add the ``github`` renderer output to stderr. This means you get inline annotations on your pull requests without needing to specify the ``github`` format explicitly.

For example, this workflow uses ``text`` as the primary format but still gets GitHub annotations automatically: ::

  - name: Run PHPMD
    run: phpmd analyze --format text .

GitHub Check Runs
-----------------

For richer integration with the GitHub Checks API, PHPMD provides the ``githubcheckruns`` renderer. This outputs JSON structured for the `Check Runs API <https://docs.github.com/en/rest/checks/runs#create-a-check-run>`_, which allows you to create proper check runs with summaries, file-level annotations, and priority-based severity levels.

Usage: ::

  phpmd analyze --format githubcheckruns . > checkrun.json

The JSON output includes:

- A title and summary of the analysis
- Annotations grouped by file with start/end lines
- Severity levels mapped from PHPMD rule priorities (1=failure, 2-3=warning, 4-5=notice)
- Detailed rule metadata (rule set, external info URL, priority)

This output can then be used with the GitHub API or third-party tools to create Check Runs on your repository.

GitLab Code Quality Reporting
=========

GitLab Code Quality reporting is supported out of the box with its own PHPMD renderer called ``gitlab``. You can read the GitLab docs about this topic `here <https://docs.gitlab.com/ee/user/project/merge_requests/code_quality.html>`_.

A simple GitLab Code Quality report workflow could look like this: ::

  mess_detection:
      image: ubuntu-latest
      stage: quality
      script:
        - phpmd analyze --format gitlab . > phpmd-report.json
      artifacts:
        reports:
          codequality: phpmd-report.json
