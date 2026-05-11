# Command: Generate Specs from User Story

Given a refined User Story (Feature or Hotfix) with its `Implementation Checklist`, this command generates all atomic spec files ready for implementation.

---

## Steps to Follow

### 1. Read Context

Before asking anything, gather all available context:

- Read the User Story file provided in `$ARGUMENTS` (from `openspec/features/` or `openspec/hotfixes/`).
- Read the project's `README.md` to identify the **application type** and tech stack.
- Load `rules/backend-standards.mdc` (for backend specs) and/or `rules/frontend-standards.mdc` (for frontend specs).

> **If the README.md does not clearly state the application type and tech stack, ask the developer to add this information before proceeding.**

### 2. Identify Application Type

For each checklist item in the User Story, identify which application type it belongs to:

| Type | When it applies |
|---|---|
| **Laravel API** | REST endpoints, business logic, Jobs, authentication |
| **Laravel Admin Panel** | Filament/Orchid screens, admin CRUD |
| **CodeIgniter 4** | Simple website routes/views |
| **Python Process** | Standalone data scripts or processes |
| **Frontend** | React/Vue components, UI, API integration layer |

### 3. MANDATORY Interactive Clarification

For each item in the `Implementation Checklist`, you must analyze the requirements. **CRITICAL RULE**: Do not generate specs until you have enough detail to write them without ambiguity. You MUST ask the developer about any missing details, ambiguous business logic, or edge cases.

Questions must cover (as applicable):

**For all backend specs:**
- Which application type does this belong to? (confirm from README or ask)
- What branch should be used? (remind naming convention: `feature/YYYYMMDD_description` or `hotfix/YYYYMMDD_description`)
- Are there any third-party integrations that should use a Laravel Job/Queue?

**For Laravel API specs:**
- What HTTP method and route? (e.g., `POST /api/v1/positions`)
- What authentication is required? (Passport, public)
- What are the request fields and validation rules?
- What is the response structure?
- Which Service class contains the business logic? (existing or new)
- Are there any Jobs to dispatch?
- Which database tables/models are involved?
- Are there any migrations needed?

**For Laravel Admin Panel specs:**
- Is this a Filament (new) or Orchid (legacy) panel?
- What resource/page type? (List, Form, Detail, Custom)
- What fields and filters are needed in the UI?
- What permissions/roles should be enforced?

**For CodeIgniter 4 specs:**
- What is the route URL?
- What view file should be created?
- Is there a DB query needed? (which table)

**For Python process specs:**
- What is the input data source? (DB, file, API)
- What is the expected output? (DB write, file, API call)
- Does it need to be callable from Laravel (e.g., via a Job)?
- What external dependencies/libraries are needed?

**For Frontend specs:**
- What component(s) are created or modified?
- What API endpoints does it consume?
- Is there state management involved?
- Are there design mockups or Figma URLs?

### 4. Generate Atomic Spec Files

Once all details are gathered, create a **separate spec file for each checklist item** in `openspec/specs/`.

**Naming convention**: `YYYYMMDD-[us-title]-[spec-name].md`
Example: `20260430-user-authentication-api-login-endpoint.md`

Each spec file MUST include:

```markdown
# Spec: [Short Description]

## Metadata
- **User Story**: [Link to US file]
- **Type**: Backend (Laravel API) | Backend (Laravel Admin) | Backend (CI4) | Backend (Python) | Frontend
- **Branch**: feature/YYYYMMDD_description | hotfix/YYYYMMDD_description
- **Estimated effort**: [S/M/L]

## Overview
[What this spec implements and why]

## Architecture Context
[Layers, files, and components involved]

## Implementation Steps

### Step 0: Create Feature Branch
- Check out the base branch (`main` or `develop`) and pull latest.
- Create branch: `git checkout -b feature/YYYYMMDD_description`
- For Laravel: branch suffix `-backend` for API work, `-frontend` for UI.

### Step N: [Action]
- **File(s)**: Exact file path(s)
- **Action**: What to implement
- **Details**: Step-by-step instructions precise enough for autonomous implementation

### Step LAST: Update Documentation
- Review all changes made and update the relevant files in `rules/`:
  - API changes → `rules/api-spec.yml`
  - Backend patterns → `rules/backend-standards.mdc`
  - Frontend patterns → `rules/frontend-standards.mdc`
  - Data model changes → `rules/data-model.md`
- Update the project `README.md` if new setup steps, env vars, or architecture decisions are introduced.

## Unit Test Specifications (TDD)

> Write these tests FIRST (failing), then implement the code.

### Test file: `[path/to/test-file]`

For **Laravel** (PHPUnit):
- `test_[expected]_when_[condition]` — describe each test case with Arrange/Act/Assert structure.

For **Python** (unittest):
- `test_[expected]_when_[condition]` — describe each test case with mock dependencies.

For **Frontend**:
- Describe component render tests and interaction tests.

**Test categories to cover:**
1. Happy path (valid inputs → expected output)
2. Validation errors (missing/invalid fields)
3. Authorization (unauthenticated / forbidden)
4. Not found (404 scenarios)
5. Business rule violations
6. Edge cases (empty data, boundary values)

## Acceptance Criteria
- [ ] Implementation matches the described steps.
- [ ] All unit tests pass.
- [ ] No debug code left (`dd()`, `var_dump()`, `print()`, etc.).
- [ ] Documentation updated.
- [ ] Branch follows naming convention.
- [ ] PR created in Bitbucket for review.
```

### 5. Return Summary

After generating all spec files, return:

- List of created files with their paths.
- Summary of key technical decisions made.
- Any open questions or risks identified.
- Reminder: use `/develop-backend` or `/develop-frontend` to implement each spec, and `/complete-spec` when done.

---

## Standards Applied

- **Backend (Laravel)**: `rules/backend-standards.mdc` — Service layer, thin controllers, Eloquent, Passport auth, Jobs for async, PHPUnit TDD, branch naming `feature/YYYYMMDD_*`.
- **Backend (CI4)**: `rules/backend-standards.mdc` — Simple MVC, one view per route, no service layer.
- **Backend (Python)**: `rules/backend-standards.mdc` — Python 3.12, unittest, type hints, logging.
- **Frontend**: `rules/frontend-standards.mdc`
- **General**: `rules/base-standards.mdc` — English only, SOLID, TDD, incremental changes.

> **This command replaces `plan-backend-ticket` and `plan-frontend-ticket`, which are now deprecated.**
