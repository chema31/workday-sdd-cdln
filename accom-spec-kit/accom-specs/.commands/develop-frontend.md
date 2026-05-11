# Command: Develop Frontend Spec

Please implement the frontend spec: $ARGUMENTS.

## Steps to Follow:

1.  **Read the Spec**: Analyze the spec file located in `openspec/specs/`.
2.  **Environment Setup**:
    *   Start a new branch named after the User Story and spec if not already on one.
    *   Ensure the latest changes from the base branch are pulled.
3.  **Implement with TDD**:
    *   **Write failing component tests first** as defined in the spec's Unit Test Specifications.
    *   Follow `rules/frontend-standards.mdc` for component structure, accessibility, and styling.
    *   Implement pixel-perfect UI according to design requirements in the spec.
4.  **Verification**:
    *   Run tests and ensure they pass.
    *   Check for responsiveness and accessibility.
5.  **Completion**:
    *   Once implemented and verified, run the **`complete-spec`** command for this spec.
6.  **Git Operations**:
    *   Use the `commit` command to stage and commit the changes for this spec.

## Design Alignment
If a Figma URL is provided in the spec, use the Figma MCP to ensure the implementation matches the design exactly.
