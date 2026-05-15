# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.4.0] - 2026-05-14

### Added

- Added `Field::effective()` to resolve the effective value per field (`patch` value when present, otherwise `current` value).
- Added `Field::isAbsent()` as an explicit counterpart to `Field::isPresent()`.

### Changed

- Renamed `Field::changed()` to `Field::isChanged()` for API consistency.
- Updated orchestrator internals to use `Field::isChanged()`.
- Updated README examples to reflect `effective()`-based value selection.

### Tests

- Expanded unit coverage for:
    - `Field::effective()`
    - `Field::isAbsent()`

## [0.3.0] - 2026-04-28

### Added

- Added `Field::notNullable(...)` helper to simplify nullable transformations.

### Tests

- Added unit coverage for `Field::notNullable(...)` behavior with both `null` and non-null values.
