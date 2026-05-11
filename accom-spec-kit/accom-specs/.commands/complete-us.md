# Command: Complete User Story

When all specs for a User Story are completed:

## Steps to Follow:

1.  **Final Verification (E2E Tests)**:
    *   Ensure all atomic spec items in the `Implementation Checklist` are marked as completed (`[x]`).
    *   **CRITICAL**: You MUST run and verify the **Playwright functional tests** for this User Story. It is strictly forbidden to validate or close a User Story if the E2E Playwright tests have not passed correctly.
2.  **Archive User Story**:
    *   Once E2E tests pass, move the User Story file from `openspec/features/` or `openspec/hotfixes/` to `openspec/archive/features/` or `openspec/archive/hotfixes/`.
3.  **Clean Repository**:
    *   **DELETE** all the archived spec files in `openspec/archive/specs/` that belong to this User Story. We do this to ensure the repository remains clean and lightweight over time.
4.  **Update Documentation** *(mandatory — same weight as E2E tests)*:
    *   Run the `/update-docs` command. This is non-skippable.
    *   `/update-docs` covers two scopes:
        *   **Framework scope**: `rules/` files (`data-model.md`, `api-spec.yml`, `*-standards.mdc`, `development_guide.md`) and the framework's own `README.md`.
        *   **Host application scope**: `../README.md` — update the managed sections (`Features`, `Architecture`, `Getting Started`, `Changelog`) as appropriate for this US. Only touch sections marked with `<!-- accom-spec-kit:managed -->`.
    *   Do not proceed to step 5 until both scopes have been reviewed and updated.
5.  **Reporting**:
    *   Summarize the changes made, the tests executed, and confirm that the cleanup and documentation steps were successfully completed.
