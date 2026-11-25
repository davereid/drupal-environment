# Changelog

## [2.0.0](https://github.com/davereid/drupal-environment/compare/1.3.0...2.0.0) (2026-11-25)


### ⚠ BREAKING CHANGES

* raise PHPStan to level 6 and add native types ([#82](https://github.com/davereid/drupal-environment/issues/82))
* require PHP 8.3 or later

### Features

* add getMemoryLimit() and getAvailableMemory() methods ([70b523e](https://github.com/davereid/drupal-environment/commit/70b523e249cfc39a23b35e4c31293ddaf9f0cba0))
* require PHP 8.3 or later ([70b523e](https://github.com/davereid/drupal-environment/commit/70b523e249cfc39a23b35e4c31293ddaf9f0cba0))


### Performance Improvements

* cache commandExists() results per request ([#88](https://github.com/davereid/drupal-environment/issues/88)) ([2e89920](https://github.com/davereid/drupal-environment/commit/2e89920efa858192d4a5b8624d05c58059c3943c))


### Miscellaneous Chores

* add cspell ([#35](https://github.com/davereid/drupal-environment/issues/35)) ([053a868](https://github.com/davereid/drupal-environment/commit/053a86888744100a8b20028897d07935c10b06c4))
* add release notes for changes before release-please ([70b523e](https://github.com/davereid/drupal-environment/commit/70b523e249cfc39a23b35e4c31293ddaf9f0cba0))
* **deps:** bump cspell from 10.2.1 to 10.4.1 ([#61](https://github.com/davereid/drupal-environment/issues/61)) ([4345f61](https://github.com/davereid/drupal-environment/commit/4345f61cce6233df523b34d12f2745414e0a89c5))
* **deps:** lock file maintenance ([#104](https://github.com/davereid/drupal-environment/issues/104)) ([dfcfca9](https://github.com/davereid/drupal-environment/commit/dfcfca95c9a108465989604a318308f13b893120))
* **deps:** lock file maintenance ([#85](https://github.com/davereid/drupal-environment/issues/85)) ([7f75e38](https://github.com/davereid/drupal-environment/commit/7f75e3832631ccd7604c2ba48390828f46d22941))
* **deps:** update dependency cspell to v10.5.0 ([#91](https://github.com/davereid/drupal-environment/issues/91)) ([f8f4fe0](https://github.com/davereid/drupal-environment/commit/f8f4fe001960cbc6c6a8e541c49e907a0ab6b308))
* **deps:** update dependency cspell to v10.5.1 ([#100](https://github.com/davereid/drupal-environment/issues/100)) ([503afbc](https://github.com/davereid/drupal-environment/commit/503afbc84182c5c3493a62a94c08dac8ce649fb3))
* **deps:** update dependency phpunit/phpunit to v13 (main) ([#70](https://github.com/davereid/drupal-environment/issues/70)) ([1fd698f](https://github.com/davereid/drupal-environment/commit/1fd698f503bdcd21ab328fd364bde286506d8cfc))
* **deps:** update qltysh/qlty-action action to v2.6.0 ([#89](https://github.com/davereid/drupal-environment/issues/89)) ([fd1daf1](https://github.com/davereid/drupal-environment/commit/fd1daf159a0e2b945e4ac54d119f43c0b79762a2))
* **deps:** update qltysh/qlty-action action to v2.7.0 ([#98](https://github.com/davereid/drupal-environment/issues/98)) ([44b3af3](https://github.com/davereid/drupal-environment/commit/44b3af3dafd8478c66ce28de6adc8c6936388018))
* record architecture decisions ([#73](https://github.com/davereid/drupal-environment/issues/73)) ([dd8d960](https://github.com/davereid/drupal-environment/commit/dd8d9604d8b37b4acad97c0b75acf06a4a4b6263))
* remove CLAUDE.md, since Claude Code reads AGENTS.md ([#101](https://github.com/davereid/drupal-environment/issues/101)) ([2b7b05c](https://github.com/davereid/drupal-environment/commit/2b7b05c11db8cb1e601d319dff6f1c8f7219135e))
* restore root-anchored ignore paths and drop .idea ([8bd9248](https://github.com/davereid/drupal-environment/commit/8bd9248bfb0cf2d7227249c3da5fb729f7a03d66))
* store text files with LF line endings ([#113](https://github.com/davereid/drupal-environment/issues/113)) ([cdd4e78](https://github.com/davereid/drupal-environment/commit/cdd4e78c56c28843123fc1ccf6fb554f48c9660a))


### Code Refactoring

* raise PHPStan to level 6 and add native types ([#82](https://github.com/davereid/drupal-environment/issues/82)) ([22084e6](https://github.com/davereid/drupal-environment/commit/22084e6047773b85e1aea1a569b3c3f47940eafb))


### Continuous Integration

* add release-please and conventional commit PR title check ([#45](https://github.com/davereid/drupal-environment/issues/45)) ([0d3cbec](https://github.com/davereid/drupal-environment/commit/0d3cbec10010d6dceea7d339a7eddcc61f72c555))
