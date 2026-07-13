# Command: Complete User Story

When all specs for a User Story are completed:

## Steps to Follow:

1.  **Final Verification (E2E Tests)**:
    *   Ensure all atomic spec items in the `Implementation Checklist` are marked as completed (`[x]`).
    *   **CRITICAL**: You MUST run and verify the **Playwright functional tests** for this User Story. It is strictly forbidden to validate or close a User Story if the E2E Playwright tests have not passed correctly.
2.  **Archive User Story**:
    *   Once E2E tests pass, move the User Story file from `openspec/features/` or `openspec/hotfixes/` to `openspec/archive/features/` or `openspec/archive/hotfixes/`.
    *   **Update Development Plan** *(only if `openspec/development-plan.md` exists)*:
        *   Find the entry in `## User Stories` that links to this User Story file.
        *   Mark the checkbox as done and apply strikethrough to the entire line:
            `- [x] ~~[filename.md](archive/features/filename.md)~~`
        *   Update the path in the link to reflect the new archive location.
        *   Do not modify any other line in the plan.
3.  **Clean Repository**:
    *   **DELETE** all the archived spec files in `openspec/archive/specs/` that belong to this User Story. We do this to ensure the repository remains clean and lightweight over time.
4.  **Update Documentation** *(mandatory — same weight as E2E tests)*:
    *   Run the `/update-docs` command. This is non-skippable.
    *   `/update-docs` covers two scopes:
        *   **Framework scope**: `rules/` files (`data-model.md`, `api-spec.yml`, `*-standards.mdc`, `development_guide.md`) and the framework's own `README.md`.
        *   **Host application scope**: `../README.md` — update the managed sections (`Features`, `Architecture`, `API Reference`, `Configuration`, `Getting Started`, `Changelog`) as appropriate for this US. Write functional-technical documentation for human readers: what the feature does, how to use it, what changed technically. Only touch sections marked with `<!-- dln-spec-kit:managed -->`.
    *   Do not proceed to step 5 until both scopes have been reviewed and updated.
5.  **Create Pull Request** — **FORBIDDEN TO SKIP**:
    *   Before reporting, open a Pull Request in Bitbucket following `rules/git-standards.mdc`:
        *   Merge the base branch into the working branch locally first and verify no conflicts exist.
        *   **Feature branches** (`feature/*`): PR targets `develop`. Minimum **1 reviewer**.
        *   **Hotfix branches** (`hotfix/*`): Two PRs required — one targeting `master`/`main` and one targeting `develop`. Minimum **2 reviewers** for the `master`/`main` PR, **1 reviewer** for the `develop` PR.
    *   Use the Bitbucket CLI (`bb`) or the GitHub CLI (`gh`) depending on the project's remote. Include a descriptive PR title and a summary of changes in the PR body.
    *   Report the PR URL(s) in the final summary.
6.  **Reporting**:
    *   Summarize the changes made, the tests executed, and confirm that the cleanup, documentation, and PR creation steps were successfully completed.
    *   Include PR URL(s) created in step 5.
