# Repository Guidelines

## Project Structure & Module Organization
Laravel 12 powers this API. Domain logic lives in `app/` (controllers, models, actions), while `routes/api.php` defines HTTP entry points backed by services in `app/Http`. Persistence artefacts—migrations, factories, seeders—reside in `database/`; queueable jobs expect a worker via `php artisan queue:listen`. Frontend scaffolding sits in `resources/` with compiled assets emitted to `public/`. Tests use Pest in `tests/Feature` for request flows and `tests/Unit` for pure logic.

## Build, Test, and Development Commands
- `composer setup` – bootstrap the project (PHP deps, `.env`, key generation, migrations, Node deps, production build).
- `composer dev` – run the API server, queue worker, and Vite watcher together during local development.
- `php artisan test` – execute the Pest suite; add `--filter Workout` to target a specific feature.
- `npm run dev` – hot-reload frontend assets; avoid committing the generated `public/build` output.

## Coding Style & Naming Conventions
Follow PSR-12 with 4-space indentation and LF endings as enforced by `.editorconfig`. Format PHP with `./vendor/bin/pint` before pushing; let your IDE manage import ordering. Use descriptive singular class names (`StartWorkoutJob`) and snake_case migration filenames. Keep API route segments kebab-case, and align Pest `describe` blocks with the class or endpoint under test.

## Testing Guidelines
Pest is the default framework. Place request/response scenarios in `tests/Feature` and isolate business helpers in `tests/Unit`. Prefer expressive `it()` descriptions written in present tense and stick to an arrange–act–assert structure. For new endpoints, cover happy path, validation failure, and authorization branches in separate tests. Use factories with explicit overrides so scenarios stay deterministic.

## Commit & Pull Request Guidelines
Write imperative, ≤72-character commit subjects (`Add workout completion endpoint`) and include a body when schema or queue behaviour changes. Name branches after the work item (`feature/workout-metrics`). Pull requests must summarise the behaviour change, reference the tracker ID, and list manual verification steps. Attach example API requests/responses or screenshots whenever UI-facing assets change.

## Environment & Security Notes
Copy `.env.example` to `.env` and set database credentials; SQLite fallback is available via `database/database.sqlite`. Run `php artisan migrate --seed` after schema updates and keep `.env` out of commits. Queue features require a worker (`php artisan queue:listen --tries=1`) or the combined `composer dev` script.
