# Command: Develop Backend Spec

Please implement the backend spec: $ARGUMENTS.

## Steps to Follow:

1.  **Analyze Context & Technology**:
    *   Read the specified spec file located in `openspec/specs/`.
    *   Read the project's `README.md` to identify the technology stack (Laravel, Python, or CodeIgniter 4).
2.  **Adopt the Specialized Role**:
    *   Based on the identified technology, strictly adopt the corresponding agent persona from `accom-specs/.agents/`:
        *   If Laravel (API, Web, Admin Panel): Use **`laravel-developer.md`**.
        *   If Python (Data process/script): Use **`python-developer.md`**.
        *   If CodeIgniter 4 (Commercial web): Use **`ci-developer.md`**.
3.  **Environment Setup**:
    *   Start a new branch named after the User Story and spec if not already on one. Branch name must be `feature/YYYYMMDD_description` or `hotfix/YYYYMMDD_description`.
    *   Ensure the latest changes from the base branch are pulled.
4.  **Implement**:
    *   Apply the specific guidelines from `rules/backend-standards.mdc` for the chosen technology.
    *   **TDD**: For Laravel and Python, you MUST write failing tests first as defined in the spec's Unit Test Specifications, then implement the code.
5.  **Verification**:
    *   Run tests and ensure they pass.
    *   Check for type safety and linting where applicable.
6.  **Completion**:
    *   Once implemented and verified, run the **`complete-spec`** command for this spec.
7.  **Git Operations**:
    *   Use the `commit` command to stage and commit the changes for this spec. Ensure the commit message is in English.

Remember to use the GitHub/Bitbucket CLI for all repository-related tasks if needed.