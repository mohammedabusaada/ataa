# Progress

Ataa is an aid distribution management system for NGOs and charities: household registration and verification, aid programs, distribution rounds, and pickup tracking with duplicate prevention.

## Done

### Day 1: Database schema and Eloquent relationships

- Laravel 12 project on MariaDB, pushed to GitHub.
- 14 domain tables with migrations and models:
  - **Geography:** `governorates` → `areas`
  - **Households:** `households`, `vulnerability_flags` + `household_vulnerability_flag` pivot, `household_documents`, `household_status_changes`
  - **Programs and distribution:** `aid_programs`, `aid_program_area` and `aid_program_vulnerability_flag` pivots (eligibility rules), `distribution_rounds`, `allocations`
  - **System:** `audit_logs` (polymorphic)
- Database-level duplicate prevention:
  - `UNIQUE(households.national_id)`
  - `UNIQUE(allocations.distribution_round_id, household_id)`: a household is allocated at most once per round
  - `UNIQUE(distribution_rounds.aid_program_id, round_number)`
  - `UNIQUE(areas.governorate_id, name_ar)` and `(governorate_id, name_en)`
  - composite primary keys on all pivot tables
- Delete rules: `restrict` for anything that carries history, `cascade` for labels and attachments (pivots, documents), `nullOnDelete` for `audit_logs.user_id`.

## Learned

- A migration is version control for the schema: `up()` applies, `down()` reverts, and filename order matters for foreign keys.
- Column types follow the data: `char` for fixed length, `string` for short variable text, `text` for long unindexed text, unsigned integers for quantities. IDs and phone numbers are strings, because they are codes, not numbers.
- The foreign key lives on the "many" side. `foreignId()->constrained()` creates the column and the constraint; `constrained('users')` when the name breaks the convention.
- `unique('a', 'b')` makes `b` the index **name**. A composite unique needs an array: `unique(['a', 'b'])`.
- Indexes do two jobs: speed and enforcing rules (unique, primary). InnoDB reuses an index whose leftmost column is the foreign key.
- Database constraints protect against race conditions that validation alone cannot.
- Eloquent: `belongsTo`, `hasMany`, `belongsToMany`, `hasManyThrough`, `morphTo`; second argument for non-standard foreign keys.
- `$fillable` guards mass assignment; columns that must come from the system (`status`, `registered_by`) stay out of it and are set with `associate()`.
- `SoftDeletes`: `delete()` sets `deleted_at`, a global scope hides those rows, `withTrashed()` includes them.
- A database default is not seen by the in-memory model after `save()`; `$attributes` sets it on the model too.
- `casts()` converts columns to PHP types (dates to Carbon, JSON to arrays).
- Linux file systems are case-sensitive: class names must match file names exactly.

## To revisit

- `attach` / `detach` / `sync` on `belongsToMany`: revisit with the household form's checkboxes.
- `$household->flags` (loaded collection) vs `$household->flags()` (relationship object you can query or modify).

## Next step

**Day 2:** Breeze authentication, user roles as a PHP backed enum (`role`, `locale`, `is_active` on `users`), enum casts for statuses, Policies, then factories and seeders with realistic demo data (`migrate:fresh --seed`).
