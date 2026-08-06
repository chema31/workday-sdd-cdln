# Command: Complete Spec

When a specific spec implementation is finished:

## Steps to Follow:

1.  **Strict Verification (TDD & Unit Tests)**:
    *   **CRITICAL RULE**: It is strictly forbidden to consider a spec complete if it does not have its corresponding Unit Tests.
    *   You MUST verify that the unit tests exist and that they have been executed and passed correctly.
    *   Ensure all code is fully typed and follows `rules/base-standards.mdc`.
    *   If tests do not exist or do not pass, STOP. Do not proceed to the next step.

2.  **Archive Spec** — **FORBIDDEN TO SKIP**:
    *   Move the spec file from `openspec/specs/<spec-file>.md` to `openspec/archive/specs/<spec-file>.md`.
    *   If `openspec/archive/specs/` does not exist, create it first.
    *   After the move, verify: the file exists at the destination AND no longer exists at the source.
    *   If the move fails for any reason, STOP and report the exact error. Do not proceed to Step 3.

3.  **Update User Story** — **FORBIDDEN TO SKIP**:
    *   Locate the parent User Story file in `openspec/features/` or `openspec/hotfixes/` by searching for the spec file name inside its `Implementation Checklist` section.
    *   Find the unchecked line `- [ ] <spec-name>` and replace it with `- [x] ~~<spec-name>~~` (checked + strikethrough).
    *   After the edit, re-read the file and confirm the updated line is present.
    *   If the User Story file cannot be found, or the checklist line is missing, STOP and report which file/line is missing. Do not declare completion.

4.  **Reporting**:
    *   Report each action individually with its outcome:
        *   `[DONE] Tests verified: <test file path>`
        *   `[DONE] Spec archived: openspec/archive/specs/<spec-file>.md`
        *   `[DONE] Checklist updated: <user-story-file> — line "<spec-name>" marked complete`
    *   If any step has status `[FAILED]`, surface it with the exact error. Never declare overall success if any step failed.
