# Command: Create Development Plan

You are acting as the product-strategy-analyst agent defined in `dln-specs/.agents/product-strategy-analyst.md`. Read and adopt that agent's full role and methodology before proceeding.

## Steps to Follow

### 1. Read Project Context

- Read `../README.md` to understand the current tech stack, architecture, and domain.
- Note any existing features or constraints that will inform the plan.

### 2. Requirements Gathering (Interactive)

Using the product-strategy-analyst methodology, lead a structured conversation **in Spanish** with the engineer to capture:

- The overall goal and business motivation of the development effort.
- The scope: what is included and explicitly excluded.
- The list of functional areas or capabilities to develop.
- Priority or suggested order of delivery (if the engineer has a view).
- Any technical or business constraints.

Ask questions in logical groups, one group at a time. Do not proceed to step 3 until the scope is clear enough to define a coherent list of user stories.

### 3. Draft the Development Plan

Once requirements are gathered, generate:

1. **Overview**: One paragraph summarising the development goal and business motivation.
2. **Scope**: What is covered and what is explicitly out of scope.
3. **User Stories list**: An ordered list of stories derived from the requirements. Each entry:
   - Is written in **kebab-case** (2–5 words, lowercase, hyphen-separated), in **English**.
   - Has **no ID and no date** — these are assigned later by `/enrich-us`.
   - Format: `- [ ] kebab-case-description`

Present the full draft to the engineer and ask for confirmation or revisions. Do not save until the engineer explicitly approves.

### 4. Save the Development Plan

Once confirmed, save the plan to `openspec/development-plan.md` using this structure:

```markdown
# Development Plan

## Overview
[Summary of the development goal and business motivation]

## Scope

**Included:**
- [item]

**Excluded:**
- [item]

## User Stories

- [ ] first-story-description
- [ ] second-story-description
- [ ] third-story-description
```

### 5. Update the Host README

- Open `../README.md`.
- If a `Development Plan` section does not exist, add one under a logical heading (e.g. `## Development`).
- Add the line: `See [Development Plan](openspec/development-plan.md) for the full list of planned user stories.`
- Do not add the reference if it is already present.

### 6. Stop and Report

- Report the path to the created `openspec/development-plan.md`.
- List the user stories included in the plan.
- Remind the engineer that stories have no ID yet — each story receives its ID and date when enriched via `/enrich-us`.
- Do NOT invoke `/enrich-us` automatically.
