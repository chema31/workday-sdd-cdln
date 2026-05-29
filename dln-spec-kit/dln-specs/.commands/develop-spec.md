# Command: Develop Spec

Please implement the spec: $ARGUMENTS.

## Steps to Follow:

### 1. Read the Spec

- Read the spec file passed in `$ARGUMENTS` (located in `openspec/specs/`).
- Read the project's `README.md` to confirm the tech stack.

### 2. Detect Implementation Type

Parse the spec's `## Metadata` section and read the `**Type**:` field. Map it to one of three routes:

| Spec `Type` value | Route |
|---|---|
| `Backend (Laravel API)`, `Backend (Laravel Admin)`, `Backend (CI4)`, `Backend (Python)` | **Backend** |
| `Frontend` | **Frontend** |
| `Playwright`, `Functional Tests (Playwright)` | **Playwright** |

> **If the `Type` field is missing or unrecognized, stop and ask the developer to add it to the spec before proceeding.**

### 3. Adopt the Specialized Role

- **Backend route**: Adopt the agent from `dln-specs/.agents/` matching the sub-type:
  - Laravel (API / Web / Admin Panel): use `laravel-developer.md`
  - Python (data process / script): use `python-developer.md`
  - CodeIgniter 4 (commercial web): use `ci-developer.md`
  - Apply `rules/coding-standards.mdc` (always) and the technology-specific file: `rules/laravel-standards.mdc`, `rules/python-standards.mdc`, or `rules/ci4-standards.mdc`.
- **Frontend route**: Adopt `dln-specs/.agents/frontend-developer.md`. Apply `rules/frontend-standards.mdc`. If a Figma URL is present in the spec, use the Figma MCP to ensure pixel-perfect alignment.
- **Playwright route**: Apply `rules/playwright-standards.mdc`. Follow the spec's Acceptance Criteria as the implementation guide.

### 4. Environment Setup

- If not already on the correct branch, create one as defined in the spec's `## Metadata` (`**Branch**:` field).
- Branch naming: `feature/YYYYMMDD_description` or `hotfix/YYYYMMDD_description`.
- Pull latest from the base branch before starting.

### 5. Implement (TDD)

- **Backend (Laravel / Python)**: Write failing unit tests first as defined in the spec's `## Unit Test Specifications`, then implement the code.
- **Frontend**: Write failing component tests first, then implement.
- **Playwright**: Write the E2E test scenarios from the spec's Acceptance Criteria, then verify they pass against the running application.

### 6. Verification

- Run all tests and ensure they pass.
- **Backend**: Check type safety and linting where applicable.
- **Frontend**: Check responsiveness and accessibility.
- **Playwright**: All E2E scenarios must be green before declaring completion.

### 7. Completion

- Once implemented and verified, run the **`complete-spec`** command for this spec.

### 8. Git Operations

- Use the `commit` command to stage and commit the changes for this spec. Commit message must be in English.
- Use the GitHub / Bitbucket CLI for any repository-related tasks if needed.
