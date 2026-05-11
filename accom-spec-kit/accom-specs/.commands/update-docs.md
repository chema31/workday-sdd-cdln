# Command: Update Documentation

Use `rules/documentation-standards.mdc` as the base standard for all updates.

## Documentation Scopes

This command covers **two scopes**. Always review and update both.

---

### Scope 1 — Framework Documentation (internal)

Files inside the `accom-spec-kit/` directory:

| Change type | File(s) to update |
|---|---|
| Data model changes | `rules/data-model.md` |
| API changes | `rules/api-spec.yml` |
| New libraries, DB migrations, env vars, setup changes | `rules/development_guide.md` |
| Standards or convention changes | `rules/backend-standards.mdc`, `rules/frontend-standards.mdc`, `rules/base-standards.mdc` |
| Any change that affects how the project runs or is structured | `README.md` (framework readme) |

---

### Scope 2 — Host Application Documentation (external)

Files located **one level above** `accom-spec-kit/`, i.e., at `../`:

| Change type | Section(s) to update in `../README.md` |
|---|---|
| New user-facing feature | `## Features` — add a bullet describing the new capability |
| Removed or changed feature | `## Features` — update or remove the relevant bullet |
| Architecture or tech stack change | `## Architecture` or `## Tech Stack` — update accordingly |
| Installation/setup change | `## Getting Started` or `## Installation` — update steps |
| Any completed User Story | `## Changelog` — append a line with format: `- [YYYY-MM-DD] <US title>: <one-line summary>` |

**Rules for editing `../README.md`:**
- Only modify sections marked with the `<!-- accom-spec-kit:managed -->` comment.
- If a managed section does not yet exist, create it with the marker comment immediately after the section heading.
- Never modify developer-authored sections (those without the marker).
- If `../README.md` does not exist, create it with a minimal structure and all managed sections.

**Managed section template** (use when creating a section for the first time):
```markdown
## Features
<!-- accom-spec-kit:managed -->

## Architecture
<!-- accom-spec-kit:managed -->

## Getting Started
<!-- accom-spec-kit:managed -->

## Changelog
<!-- accom-spec-kit:managed -->
```

---

## Steps

1. Review all recent changes in the codebase (git diff or context from the current task).
2. For each change, identify which files in **both scopes** require an update.
3. Update each affected file in English, maintaining consistency with existing content.
4. Ensure formatting follows the established structure of each file.
5. Report which files were updated and what was changed in each.
