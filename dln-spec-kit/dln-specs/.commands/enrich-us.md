# Command: Enrich User Story

Please analyze and enrich the User Story: $ARGUMENTS.

## Steps to Follow:

1.  **Read Context**: Read the project's `README.md` and the rules in `rules/` to understand the application context before proceeding.
2.  **Identify Story Type**: Explicitly ask the user if this is a **Feature** or a **Hotfix**. Do not proceed until you have this answer.
2b. **Check for Development Plan**:
    *   Check whether `openspec/development-plan.md` exists.
    *   If it exists, read it and extract all pending entries (lines matching `- [ ] kebab-case-description` that have no ID or date yet).
    *   If `$ARGUMENTS` matches one of those descriptions (exact or close match), use that entry as the basis for the story — its kebab-case description becomes the `[kebab-case-description]` segment of the filename.
    *   If `$ARGUMENTS` is ambiguous or empty and multiple pending entries exist, present the list to the user and ask which story they want to enrich. Do not proceed until the user selects one.
    *   If no development plan exists, continue normally.
3.  **Draft the User Story**:
    *   Create a descriptive title.
    *   **Determine the US identifier**:
        *   Scan all files in `openspec/features/` and `openspec/hotfixes/` that match the pattern `*-us[N]-*.md`.
        *   Find the highest `[N]` across both folders and increment by 1 to get the next ID.
        *   Format as zero-padded three digits: `us001`, `us002`, etc.
        *   If no existing files are found, start at `us001`.
    *   Generate a filename following the pattern: `YYYYMMDD-[us-id]-[kebab-case-description].md`
        *   `YYYYMMDD` is today's date.
        *   `[us-id]` is the identifier determined above (e.g. `us001`).
        *   `[kebab-case-description]` is a short (2–5 words), lowercase, hyphen-separated description of the story.
        *   Valid examples: `20260623-us001-candidate-profile-page.md`, `20260623-us002-fix-login-token.md`
    *   Ensure the US includes: clear description, business value, detailed acceptance criteria, technical considerations, and non-functional requirements.
4.  **Generate Implementation Checklist**:
    *   Create a mandatory, ordered checklist of atomic **Specs** needed to complete the story.
    *   Format:
        ```markdown
        ## Implementation Checklist
        - [ ] Spec 1: [Short description]
        - [ ] Spec 2: [Short description]
        - [ ] Spec N: Playwright Functional Tests
        ```
    *   **Numbering rules (strictly enforced)**:
        *   Numbering **starts at 1**. `Spec 0` is forbidden.
        *   **Every item must carry its `Spec [N]:` prefix**, including the Playwright item.
        *   The Playwright Functional Tests item is **always the last spec** and must be numbered sequentially (never treated as an unnumbered extra item).
5.  **MANDATORY INTERACTIVE FEEDBACK**:
    *   Present your drafted User Story and Checklist to the user.
    *   **CRITICAL**: You MUST ask the user about any doubts, edge cases, missing examples, or ambiguous business rules. Do NOT save or close the US until the user has confirmed that the details are exhaustive and correct.
6.  **Save to Repository**:
    *   Once confirmed by the user, store the enriched US in:
        *   `openspec/features/` for features.
        *   `openspec/hotfixes/` for hotfixes.
7.  **Update Development Plan** *(only if `openspec/development-plan.md` exists)*:
    *   Find the entry in `## User Stories` that corresponds to this story (the plain `- [ ] kebab-case-description` line matched in step 2b).
    *   Replace it with a link to the newly created file:
        `- [ ] [YYYYMMDD-usXXX-kebab-case-description.md](features/YYYYMMDD-usXXX-kebab-case-description.md)`
        (use `hotfixes/` path for hotfix stories)
    *   Do not modify any other line in the plan.

Return the final markdown content only after user confirmation.