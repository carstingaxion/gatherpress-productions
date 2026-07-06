# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased](https://github.com/carstingaxion/gatherpress-productions/compare/0.3.2...HEAD)

## [0.3.2](https://github.com/carstingaxion/gatherpress-productions/compare/0.3.1...0.3.2) - 2026-07-06

* Update README
* Update DE translations

## [0.3.1](https://github.com/carstingaxion/gatherpress-productions/compare/0.3.0...0.3.1) - 2026-06-17

- Use the registered post type singular label as the block variation name ([#13](https://github.com/carstingaxion/gatherpress-productions/pull/13))
- updated de_DE translations

### Dependency Updates & Maintenance

- Update deps ([#14](https://github.com/carstingaxion/gatherpress-productions/pull/14))

## [0.3.0](https://github.com/carstingaxion/gatherpress-productions/compare/0.2.1...0.3.0) - 2026-06-08

### 🚀 Added

- Feature/register block var in js ([#11](https://github.com/carstingaxion/gatherpress-productions/pull/11))
- Guard to only enqueue on the production edit screen. ([#8](https://github.com/carstingaxion/gatherpress-productions/pull/8))
- Update the production-status term of the production, when its premier… ([#7](https://github.com/carstingaxion/gatherpress-productions/pull/7))
- Use JS in favor over php registered block-variation ([#9](https://github.com/carstingaxion/gatherpress-productions/pull/9))

## [0.2.1](https://github.com/carstingaxion/gatherpress-productions/compare/0.2.0...0.2.1) - 2026-05-31

* Added manual `/build`, because the deployment action failed to keep the asset on the release

## [0.2.0](https://github.com/carstingaxion/gatherpress-productions/compare/0.1.0...0.2.0) - 2026-05-31

**Full Changelog**: https://github.com/carstingaxion/gatherpress-productions/compare/0.1.0...0.2.0

## [0.1.0](https://github.com/carstingaxion/gatherpress-productions/compare/0.1.0...0.1.0) - 2026-05-04

- Initial release as WIP
- Registers a "Production" post_type to be meant for theater productions
- Provides a queryable, but otherwise hidden, shadow taxonomy "Productions" related to regular GatherPress events.
- Provides a "Premiere" date, that is used as default ordering of production posts.
