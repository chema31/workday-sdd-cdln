# Command: Enrich User Story

Please analyze and enrich the User Story: $ARGUMENTS.

## Steps to Follow:

1.  **Read Context**: Read the project's `README.md` and the rules in `rules/` to understand the application context before proceeding.
2.  **Identify Story Type**: Explicitly ask the user if this is a **Feature** or a **Hotfix**. Do not proceed until you have this answer.
3.  **Draft the User Story**:
    *   Create a descriptive title.
    *   Generate a filename following the pattern: `YYYYMMDD-kebab-case-title.md`.
    *   Ensure the US includes: clear description, business value, detailed acceptance criteria, technical considerations, and non-functional requirements.
4.  **Generate Implementation Checklist**:
    *   Create a mandatory, ordered checklist of atomic **Specs** needed to complete the story.
    *   Format:
        ```markdown
        ## Implementation Checklist
        - [ ] Spec 1: [Short description]
        - [ ] Spec 2: [Short description]
        - [ ] Functional Tests (Playwright)
        ```
5.  **MANDATORY INTERACTIVE FEEDBACK**:
    *   Present your drafted User Story and Checklist to the user.
    *   **CRITICAL**: You MUST ask the user about any doubts, edge cases, missing examples, or ambiguous business rules. Do NOT save or close the US until the user has confirmed that the details are exhaustive and correct.
6.  **Save to Repository**:
    *   Once confirmed by the user, store the enriched US in:
        *   `openspec/features/` for features.
        *   `openspec/hotfixes/` for hotfixes.

Return the final markdown content only after user confirmation.