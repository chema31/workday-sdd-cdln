# DLN Spec-Kit: AI-Driven Development Framework

Welcome to the **DLN Spec-Kit**, a portable, plug-and-play framework designed to standardize AI-assisted development across all our projects.

Whether you're using **Claude Code**, **Cursor**, **GitHub Copilot**, **Codex**, or **Gemini**, this kit ensures that every AI agent follows our strict company standards, writes tests, and adheres to a flawless **Spec-Driven Development (SDD)** lifecycle.

**⏳ Onboarding time:** < 10 minutes.

---

## 🚀 1. Installation (2 minutes)

Place this kit as a subdirectory inside your project root, then run the install command:

```bash
# 1. Add the kit to your project (clone or copy)
git clone <dln-spec-kit-url> dln-spec-kit

# 2. Run the installer from your project root
./dln-spec-kit/install.sh
```

The installer creates **relative symlinks** in your project root pointing to every AI copilot configuration file inside the kit:

| Symlink created in project root | Tool |
|---|---|
| `CLAUDE.md` → `dln-spec-kit/CLAUDE.md` | Claude Code |
| `AGENTS.md` → `dln-spec-kit/AGENTS.md` | Generic agents |
| `GEMINI.md` → `dln-spec-kit/GEMINI.md` | Gemini CLI |
| `codex.md` → `dln-spec-kit/codex.md` | Codex |
| `.claude/` → `dln-spec-kit/.claude/` | Claude Code |
| `.cursor/` → `dln-spec-kit/.cursor/` | Cursor |
| `.codex/` → `dln-spec-kit/.codex/` | Codex |
| `rules/` → `dln-spec-kit/rules/` | All tools |
| `.github/copilot-instructions.md` → `dln-spec-kit/.github/...` | GitHub Copilot |

**The installer is idempotent** — running it again only reports already-installed items without making changes. If a conflict is detected (a file or directory already exists at the destination), it will ask you interactively what to do: skip, overwrite, or back up.

Once installed, every AI copilot will automatically read its configuration files and inherit all company standards.

---

## 🔄 2. The Development Lifecycle (5 minutes)

Our workflow consists of 6 strict, command-driven steps. We move from a raw idea to deployed, tested code while keeping the repository perfectly clean.

### The Flow at a Glance

1️⃣ `/create-us` ➔ 2️⃣ `/enrich-us` ➔ 3️⃣ `/generate-specs-from-us` ➔ 4️⃣ `/develop-spec` ➔ 5️⃣ `/complete-spec` ➔ 6️⃣ `/complete-us`

> **Optional pre-step**: if you have a raw, non-technical idea or a client request written in plain language, run `/meta-prompt` first to transform it into a structured technical description, then pass the output to `/create-us`.

---

### Step 1: `/create-us` (The Starting Point)
**Command**: `/create-us`
**What it does**:
- Reads your project's `README.md` automatically to infer tech stack, architecture, and constraints — so you don't repeat yourself.
- Guides you through a structured **conversation in Spanish**, asking one logical group of questions at a time: type of work, business context, functional description, acceptance criteria, and known constraints.
- Generates a complete User Story draft in English (the framework's technical language) and asks for your confirmation.
- **Calls `/enrich-us` automatically** once you confirm — no copy-pasting required.

### Step 2: `/enrich-us` (The Blueprint)
**Command**: `/enrich-us [user story draft]` — called automatically by `/create-us`, or manually if you have an existing draft.
**What it does**: 
- Reads your project's `README.md` and our `rules/`.
- **🛑 CRITICAL — HUMAN FEEDBACK REQUIRED**: Before confirming the User Story, the AI performs a **conflict and risk analysis** of the current application state. It will ask the engineer all necessary questions about potential conflicts, edge cases, business rules, and technical constraints. **Do not skip this step** — it is the only moment in the lifecycle where the AI surfaces design concerns before any code is written.
- Generates a highly detailed User Story (Feature or Hotfix) with a numbered checklist of atomic tasks (Specs).
- Saves it in `openspec/features/`.

### Step 3: `/generate-specs-from-us` (The Specs)
**Command**: `/generate-specs-from-us openspec/features/YYYYMMDD-title.md`
**What it does**:
- Converts the checklist into atomic, ready-to-code Markdown specs in `openspec/specs/`, named `YYYYMMDD-[us-id]-spec[N]-[kebab-case].md` (N starts at 1; the Playwright spec is always last and numbered).
- **🛑 HUMAN FEEDBACK REQUIRED**: The AI will ask you for any ambiguous technical details (DB tables, API routes) before generating them.

### Step 4: `/develop-spec` (The Code)
**Command**: `/develop-spec openspec/specs/YYYYMMDD-[us-id]-spec[N]-[kebab-case].md`
**What it does**:
- **Auto-detects the implementation type** from the spec's `## Metadata` section (`**Type**:` field) — no manual selection needed. Supported types: `Backend (Laravel API)`, `Backend (Laravel Admin)`, `Backend (CI4)`, `Backend (Python)`, `Frontend`, `Playwright`.
- Adopts the persona of an expert developer for the detected stack (Laravel, Python, CodeIgniter, React, or Playwright).
- Applies the correct rules file (`laravel-standards.mdc`, `python-standards.mdc`, `ci4-standards.mdc`, `frontend-standards.mdc`, or `playwright-standards.mdc`).
- Creates or switches to the branch defined in the spec metadata.
- **Strict TDD**: Writes failing tests *before* writing the code. Playwright specs go straight to E2E scenario implementation.

### Step 5: `/complete-spec` (The Spec Closure)
**Command**: `/complete-spec` (Run this when Step 4 is done)
**What it does**:
- Verifies that Unit Tests exist and pass. (Fails if they don't).
- Moves the spec from `openspec/specs/` to `openspec/archive/specs/`.
- Checks off `[x]` the task in the original User Story checklist.

### Step 6: `/complete-us` (The Grand Finale)
**Command**: `/complete-us openspec/features/YYYYMMDD-title.md` (Run when all specs are `[x]`)
**What it does**:
- Forces the execution of **Playwright E2E functional tests**.
- Updates the project's human documentation (`README.md`, `rules/`).
- **🧹 Clean Repository Philosophy**: Deletes all archived specs for this story to keep the git history light.
- Archives the User Story.

---

## 🛠️ 3. Utility Commands

These commands are available at any point in the workflow — they are not tied to a specific lifecycle step.

### `/meta-prompt`
**When to use**: You have a raw, non-technical idea or a client request in plain language and want to structure it before starting a User Story.
**What it does**: Transforms free-form text into a consolidated, structured technical description (role, context, objective, instructions, constraints). Pass the output to `/create-us`.

### `/explain`
**When to use**: You want to understand a concept, a design decision, or a technical behaviour — not just apply a fix.
**What it does**: Acts as a learning facilitator. Given a question or topic (from arguments or conversation context), it identifies the skill gap behind the question, explains the underlying concepts, lists alternative approaches with trade-offs, provides a mental model or diagram where helpful, and closes with an interactive quiz to validate understanding. It prioritises conceptual clarity over speed.

### `/update-docs`
**When to use**: You need to update documentation outside of the normal lifecycle closure (e.g. after a hotfix, an infrastructure change, or a manual schema update).
**What it does**: Reviews all recent changes in the codebase and updates **two scopes** in parallel:
- **Framework documentation** (`rules/data-model.md`, `rules/api-spec.yml`, `rules/development_guide.md`, standards files, and this `README.md`).
- **Host application documentation** (`../README.md`) — the human-facing project README, covering the `Features`, `Architecture`, `API Reference`, `Configuration`, `Getting Started`, and `Changelog` sections. Only managed sections (marked `<!-- dln-spec-kit:managed -->`) are touched; developer-authored content is never modified.

> `/update-docs` is also called automatically by `/complete-us` at the end of each User Story.

---

## 🧠 4. Understanding the Rules (3 minutes)

The AI relies on the `rules/` directory to know how to code. Rules are organised in a two-level authority hierarchy: **general rules first, then technology-specific rules** — never duplicate content between them. It is your job to keep these files updated if project requirements change.

**General (apply to all code)**
- `rules/base-standards.mdc`: Core principles and index of all standards files.
- `rules/coding-standards.mdc`: General rules for all languages — naming conventions, data types, SOLID, QA schedules, production deployment windows, and external communication principles.
- `rules/git-standards.mdc`: Adapted Gitflow — branch strategy, PR rules (feature → develop; hotfix → master + develop), reviewers, and AI agent git checklist.

**Technology-specific (extend `coding-standards.mdc`, no duplication)**
- `rules/laravel-standards.mdc`: Laravel API, Admin Panel, Jobs, Queues, and Supervisor configuration.
- `rules/python-standards.mdc`: Python 3.12+ standalone processes and scripts.
- `rules/ci4-standards.mdc`: CodeIgniter 4 commercial websites.
- `rules/frontend-standards.mdc`: Vite, React, and Styled Components.
- `rules/playwright-standards.mdc`: E2E functional testing standards.

**Domain knowledge (keep these updated as your project evolves)**
- `rules/api-spec.yml`: All API endpoint contracts.
- `rules/data-model.md`: Domain entities and database schema.

You're all set! Open your copilot chat and type `/create-us` to start your first feature.
