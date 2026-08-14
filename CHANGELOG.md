# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.8.1] - 2026-08-14

### Changed

- `NumericComparator` now treats exact-integer floats up to `2^53` without applying float tolerance, while keeping significant-digits
  tolerance for fractional float arithmetic.
- Documented the final comparison semantics of `NumericComparator`, including exact numeric strings and tolerant native float handling.

### Fixed

- `NumericComparator` now uses a compact canonical numeric form instead of decimal expansion, preventing exponent-driven memory blowups
  and keeping extreme scientific values comparable without exhausting memory.
- `NumericComparator` now preserves comparisons across very small and very large magnitudes more consistently between float and numeric
  string inputs, while rejecting non-finite floats explicitly.
- `PatchInput::array(...)` now reports invalid values by type instead of crashing when the input cannot be stringified for the error
  message.

### Refactored

- Added `InvalidPatchValue` as the package-owned exception for invalid patch inputs and normalization failures across built-in patch
  value types.
- Replaced ad-hoc `InvalidActionDefinition` reasons with named constructors for empty field definitions and unexpected `when(...)`
  results.

## [0.8.0] - 2026-08-14

### Added

- Added Unix timestamp support to `DateTimeComparator` for integer values, so existing timestamps can be compared directly against
  `DateTimeInterface` instances and parseable datetime strings.

### Changed

- Clarified in the README that a plain `Field` behaves as `required()` by default, while `optional()` must be selected explicitly when
  absence should be allowed.

### Fixed

- Expanded `NumericComparator` to accept valid scientific numeric strings while still preserving exact comparison for large integer
  strings and consistent handling of native float values.
- `PatchInput::array(...)` now throws on invalid non-array input instead of silently degrading present values to `null`.
- `Orchestrator` now rejects `when(...)` callbacks that do not return a `Rule` with `InvalidActionDefinition` instead of crashing with a
  raw PHP error.
- `DateTimeComparator` now reports type mismatch errors in the same `value, current` parameter order used by `equals(...)`.

## [0.7.3] - 2026-08-07

### Fixed

- Fixed `NumericComparator` so large integer strings no longer compare as equal after float precision loss.
- Normalized equivalent numeric representations such as `10`, `10.0`, `010.000`, and signed decimal variants before comparison, while keeping unsupported scientific notation rejected.

## [0.7.2] - 2026-08-04

### Added

- Added `Action::for(...)->apply(...)` as a grouped field builder for defining actions in two steps: selecting the field scope first and
  then providing the execution callback.

### Changed

- Updated README examples to prefer the new `Action::for(...)->apply(...)` syntax in action definitions and activation rule examples.

## [0.7.1] - 2026-07-17

### Fixed

- Restored `PatchValue` as the public interface for custom implementations instead of forcing consumers to extend an abstract base class.
- Moved the shared `from(...)`, `present(...)`, `missing()`, and normalization flow into `NormalizablePatchValue` for the built-in patch value implementations.
- Updated tests and release notes to reflect the interface-plus-shared-base split.

## [0.7.0] - 2026-07-17

### Added

- Added shared PatchValue named constructors: `from(bool $present, mixed $value)`, `present(...)`, and `missing()`.

### Changed

- Promoted `PatchValue` from interface to abstract base class so all concrete patch values share the same construction API and common
  presence/value state handling.
- Updated README examples to document direct PatchValue instantiation through the new named constructors.

### Tests

- Added coverage for the shared PatchValue named constructors across present and missing cases.

## [0.6.2] - 2026-07-17

### Fixed

- Aligned `Rules\Boolean::from(...)` with the `when(...)` callback contract so fixed boolean guards can be used with the same syntax as
  `Boolean::resolve(...)`.
- Updated tests and README examples to reflect the callback-based boolean guard usage and the intended custom activation semantics.

## [0.6.1] - 2026-07-17

### Added

- Added `Any::isPresent()` and `All::isPresent()` as named constructors that return `when(...)`-compatible callbacks for the most common
  presence-based activation rules.
- Added `Rules\Boolean` with `Boolean::from(...)` and `Boolean::resolve(...)` to wrap fixed booleans and custom boolean guards as rules.

### Changed

- Updated README activation rule examples to use the new `All::isPresent()` syntax for common cases and to clarify boolean guard usage for
  custom action activation checks.

### Tests

- Added coverage for:
    - `Any::isPresent()` and `All::isPresent()` named constructors
    - `Boolean::from(...)` and `Boolean::resolve(...)`

## [0.6.0] - 2026-07-16

### Added

- Added the fluent `Action` API with `Action::from(...)`, `Action::describe(...)`, and `Action::or(...)`.
- Added the `ActionFailure` contract to expose failure context (`behavior`, `field`, `actionDescription`, `progressUntilFailure`) to
  custom failure factories.
- Added support for deferred custom failure rethrows through `ActionBehaviorNotSatisfied::rethrow()`.

### Changed

- `Action::when()` now configures the activation rule fluently instead of exposing the stored closure.
- `Action::condition()` now exposes the configured activation closure for internal/runtime access.
- `Action` is now mutable and behaves as a fluent builder/factory during action definition.
- `ActionBehaviorNotSatisfied` now remains the primary orchestration failure, even when a custom failure strategy is configured, so
  progress information is always preserved before any rethrow.
- Updated README examples and usage guidance to reflect the fluent `Action` API and custom failure rethrow flow.

### Tests

- Added and updated coverage for:
    - fluent `Action` definition through `from(...)`, `when(...)`, `describe(...)`, and `or(...)`
    - preserving `ActionBehaviorNotSatisfied` before relaunching a custom exception
    - custom failure rethrow behavior through the `ActionFailure` contract

## [0.5.2] - 2026-05-15

### Fixed

- Fixed datetime comparison in `StrictComparator` and `DateTimeComparator` to use absolute instant comparison (`U.u`) instead of local
  formatted representation, so equivalent instants across different timezones are treated as equal.
- Added unit tests covering timezone-equivalent datetime comparisons for both comparators.

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
