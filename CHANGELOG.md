# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.5.1] - 2026-05-15

### Fixed

- Added explicit null handling in core comparators (`StrictComparator`, `NumericComparator`, `DateTimeComparator`, `ArrayComparator`) with
  consistent semantics (`null/null => true`, `null/non-null => false`).
- Centralized null comparison logic through `HandlesNullComparison` to keep comparator behavior aligned without changing public API.

## [0.5.0] - 2026-05-15

### Added

- Added `ExecutionResult` to represent orchestration outcomes (`total`, `executed`, `skipped`) and description-based helpers.
- Added `ActionBehaviorNotSatisfied::progressUntilFailure()` to expose partial execution progress when a contract failure interrupts
  execution.
- Added `ArrayPatchValue` with recursive normalization support for nested `PatchValue` instances.
- Added `PatchInput::array(...)` helper for array patch extraction.
- Added `Field::changed()` helper to return the incoming value only when a real change exists, otherwise `null`.

### Changed

- `Orchestrator::execute()` now returns `ExecutionResult`.
- `PatchInput` presence detection now relies on real input keys (`array_key_exists`) and no longer requires `expectedKeys`.
- Playground reporting now supports executed/skipped/failure output using orchestration results and failure progress.

### Tests

- Added/updated coverage for:
    - `ExecutionResult` usage through orchestrator tests
    - `ActionBehaviorNotSatisfied` partial progress exposure
    - `PatchInput` presence semantics based on actual input keys
    - `ArrayPatchValue` and `PatchInput::array(...)`

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
