# Command: Complete Spec

When a specific spec implementation is finished:

## Steps to Follow:

1.  **Strict Verification (TDD & Unit Tests)**:
    *   **CRITICAL RULE**: It is strictly forbidden to consider a spec complete if it does not have its corresponding Unit Tests.
    *   You MUST verify that the unit tests exist and that they have been executed and passed correctly.
    *   Ensure all code is fully typed and follows `rules/base-standards.mdc`.
2.  **Archive Spec**:
    *   Move the spec file from `openspec/specs/` to `openspec/archive/specs/`.
3.  **Update User Story**:
    *   Locate the original User Story file in `openspec/features/` or `openspec/hotfixes/`.
    *   Mark the corresponding item in the `Implementation Checklist` as completed (`- [x] Spec Name`).
4.  **Reporting**:
    *   Confirm the file move and the checklist update to the user.
