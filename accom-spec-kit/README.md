# Accom Spec-Kit: AI-Driven Development Framework

Welcome to the **Accom Spec-Kit**, a portable, plug-and-play framework designed to standardize AI-assisted development across all our projects.

Whether you're using **Claude Code**, **Cursor**, **GitHub Copilot**, **Codex**, or **Gemini**, this kit ensures that every AI agent follows our strict company standards, writes tests, and adheres to a flawless **Spec-Driven Development (SDD)** lifecycle.

**⏳ Onboarding time:** < 10 minutes.

---

## 🚀 1. Installation (2 minutes)

Place this kit as a subdirectory inside your project root, then run the install command:

```bash
# 1. Add the kit to your project (clone or copy)
git clone <accom-spec-kit-url> accom-spec-kit

# 2. Run the installer from your project root
./accom-spec-kit/install.sh
```

The installer creates **relative symlinks** in your project root pointing to every AI copilot configuration file inside the kit:

| Symlink created in project root | Tool |
|---|---|
| `CLAUDE.md` → `accom-spec-kit/CLAUDE.md` | Claude Code |
| `AGENTS.md` → `accom-spec-kit/AGENTS.md` | Generic agents |
| `GEMINI.md` → `accom-spec-kit/GEMINI.md` | Gemini CLI |
| `codex.md` → `accom-spec-kit/codex.md` | Codex |
| `.claude/` → `accom-spec-kit/.claude/` | Claude Code |
| `.cursor/` → `accom-spec-kit/.cursor/` | Cursor |
| `.codex/` → `accom-spec-kit/.codex/` | Codex |
| `rules/` → `accom-spec-kit/rules/` | All tools |
| `.github/copilot-instructions.md` → `accom-spec-kit/.github/...` | GitHub Copilot |

**The installer is idempotent** — running it again only reports already-installed items without making changes. If a conflict is detected (a file or directory already exists at the destination), it will ask you interactively what to do: skip, overwrite, or back up.

Once installed, every AI copilot will automatically read its configuration files and inherit all company standards.

---

## 🔄 2. The Development Lifecycle (5 minutes)

Our workflow consists of 6 strict, command-driven steps. We move from a raw idea to deployed, tested code while keeping the repository perfectly clean.

### The Flow at a Glance

1️⃣ `/meta-prompt` ➔ 2️⃣ `/enrich-us` ➔ 3️⃣ `/generate-specs-from-us` ➔ 4️⃣ `/develop-backend` (or `frontend`) ➔ 5️⃣ `/complete-spec` ➔ 6️⃣ `/complete-us`

---

### Step 1: `/meta-prompt` (The Idea)
You have a raw idea or a non-technical request from a client.
**Command**: `/meta-prompt "Add a search bar for users"`
**What it does**: The AI transforms your raw text into a consolidated, highly descriptive technical request.

### Step 2: `/enrich-us` (The Blueprint)
**Command**: `/enrich-us [paste output from step 1]`
**What it does**: 
- Reads your project's `README.md` and our `rules/`.
- **🛑 HUMAN FEEDBACK REQUIRED**: The AI will ask you questions about edge cases, business rules, and examples.
- Generates a highly detailed User Story (Feature or Hotfix) with a checklist of atomic tasks (Specs).
- Saves it in `openspec/features/`.

### Step 3: `/generate-specs-from-us` (The Specs)
**Command**: `/generate-specs-from-us openspec/features/YYYYMMDD-title.md`
**What it does**:
- Converts the checklist into atomic, ready-to-code Markdown specs in `openspec/specs/`.
- **🛑 HUMAN FEEDBACK REQUIRED**: The AI will ask you for any ambiguous technical details (DB tables, API routes) before generating them.

### Step 4: `/develop-backend` or `/develop-frontend` (The Code)
**Command**: `/develop-backend openspec/specs/YYYYMMDD-title-spec1.md`
**What it does**:
- The AI automatically identifies the framework (Laravel, Python, CodeIgniter, React).
- Adopts the persona of an expert developer for that framework.
- Creates a new git branch.
- **Strict TDD**: Writes unit tests *before* writing the code.

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

## 🧠 3. Understanding the Rules (3 minutes)

The AI relies on the `rules/` directory to know how to code. It is your job to keep these files updated if project requirements change.

- `rules/base-standards.mdc`: The absolute core rules (English only, SOLID, TDD).
- `rules/backend-standards.mdc`: Our rules for Laravel, Python, and CI4.
- `rules/frontend-standards.mdc`: Our rules for Vite, React, and Styled Components.
- `rules/playwright-standards.mdc`: Our rules for E2E testing.
- `rules/api-spec.yml` & `rules/data-model.md`: Keep these updated to represent your database and endpoints!

You're all set! Open your copilot chat and type `/meta-prompt` to start your first feature.
