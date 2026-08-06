# Command: Update Documentation

Use `rules/documentation-standards.mdc` as the base standard for all updates.

## Documentation Scopes

This command covers **two scopes**. Always review and update both.

---

### Scope 1 — Framework Documentation (internal)

Files inside the `dln-spec-kit/` directory:

| Change type | File(s) to update |
|---|---|
| Data model changes | `rules/data-model.md` |
| API changes | `rules/api-spec.yml` |
| New libraries, DB migrations, env vars, setup changes | `rules/development_guide.md` |
| Standards or convention changes | `rules/coding-standards.mdc`, `rules/laravel-standards.mdc`, `rules/python-standards.mdc`, `rules/ci4-standards.mdc`, `rules/frontend-standards.mdc`, `rules/base-standards.mdc` |
| Any change that affects how the project runs or is structured | `README.md` (framework readme) |

---

### Scope 2 — Host Application Documentation (`../README.md`)

This is the **human-facing documentation** of the parent project. It must be written clearly enough for a developer or technical stakeholder to understand and operate the application without reading the source code.

Every User Story closure **must** trigger a review and update of `../README.md`. Content must cover both perspectives:

- **Functional**: what the feature does, how a user or system interacts with it, expected behaviours and edge cases.
- **Technical**: what changed in the system to enable it (configuration, APIs, migrations, dependencies, environment variables).

#### Managed sections and what to write in each

| Section | When to update | What to write |
|---|---|---|
| `## Features` | Every new or modified feature | A subsection (`###`) per User Story with: a short functional description of what the feature enables, the main user flows or use cases it covers, and any relevant business rules or constraints. |
| `## Architecture` | When a new component, service, or integration is added | Update the affected layer or add a subsection explaining the new component, its responsibility, and how it connects to the rest of the system. |
| `## API Reference` | When API endpoints are added or changed | Document each new/changed endpoint: method, route, authentication required, request parameters, response structure, and a usage example. |
| `## Configuration` | When new env vars, config keys, or setup steps are introduced | List each new variable or key with: its name, purpose, accepted values, and whether it is required or optional. Update setup instructions if the installation process changed. |
| `## Getting Started` | When the setup or onboarding process changes | Update steps to reflect new requirements (new env vars, migrations, dependencies, services to run). |
| `## Changelog` | Every User Story closure | Append one entry per closed US: `- [YYYY-MM-DD] <US title>: <one-line functional summary of what was delivered>`. |

#### Writing rules for `../README.md`

- Write **for a human reader** — use plain English, avoid internal jargon or references to spec files.
- The `## Features` subsection for each User Story must contain **at minimum**:
  1. One paragraph describing **what** the feature does and **why** it exists (functional context).
  2. A bullet list of the **main use cases or user flows** it enables.
  3. Any **important constraints or business rules** the user must know.
- The `## API Reference` and `## Configuration` sections must be **complete and accurate** — they are used by developers integrating with or maintaining the system.
- Only modify sections marked with `<!-- dln-spec-kit:managed -->`.
- If a managed section does not yet exist, create it with the marker comment immediately after the heading.
- Never modify developer-authored sections (those without the marker).
- If `../README.md` does not exist, create it with the full managed section template below.

#### Managed section template (use when `../README.md` does not yet exist)

```markdown
## Features
<!-- dln-spec-kit:managed -->

## Architecture
<!-- dln-spec-kit:managed -->

## API Reference
<!-- dln-spec-kit:managed -->

## Configuration
<!-- dln-spec-kit:managed -->

## Getting Started
<!-- dln-spec-kit:managed -->

## Changelog
<!-- dln-spec-kit:managed -->
```

---

## Steps

1. Review all recent changes in the codebase (git diff or context from the current task).
2. For each change, identify which files in **both scopes** require an update.
3. Update each affected file in English, maintaining consistency with existing content.
4. Ensure formatting follows the established structure of each file.
5. Report which files were updated and what was changed in each.
