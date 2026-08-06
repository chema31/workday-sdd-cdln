---
name: prompt-history
description: Use this skill whenever a slash command is executed, a free-form prompt produces a significant result, or the user requests a retroactive entry. Ensures every meaningful interaction is logged to prompts.md as a historical audit trail for this training project.
version: 1.0.0
---

# Prompt History Skill

Maintains `prompts.md` as a persistent audit log of all commands and significant prompts executed during this project.

## When to Use

- After completing any task triggered by a slash command invoked via VSCode command palette (with attachments or structured `<command-args>` — these are NOT captured by the hook).
- After any free-form prompt that produces a result worth auditing (design decisions, retroactive entries, significant context changes).
- When the user explicitly requests a retroactive entry.

> **Automatic logging:** Slash commands typed directly in chat are already captured by the `UserPromptSubmit` hook (`scripts/log-prompt.sh`). Do NOT duplicate those entries.

## Entry Formats

**Automatic (hook handles this — do not duplicate):**
```
## YYYY-MM-DD HH:MM — /command-name
**Args:**
> [first 300 chars of arguments]
```

**Manual/retroactive (Claude adds this after completing the task):**
```
## YYYY-MM-DD — /command-name  ← (retroactive)
**Args:**
> [full arguments or description]
**Result:** [one-line summary of what was done]
```

**Free-form prompt with significant result:**
```
## YYYY-MM-DD — [short action label]  ← (retroactive, non-slash)
**Action:** [description of what was requested]
**Result:** [one-line summary of outcome]
```

## Instructions

1. Identify whether the interaction was already captured by the hook (slash command typed in chat → skip).
2. For VSCode palette commands or free-form prompts with relevant output: append the appropriate entry to `prompts.md` **after** the task is complete.
3. Place the entry under the correct `<!-- SESSION: YYYY-MM-DD -->` header. If the date is new, add the header first.
4. Use today's date from the system (`currentDate` context variable = `2026-05-29`).
5. Keep the **Result** line concise (one line, no more than 120 chars).

## Constraints

- Never write to `prompts.md` mid-task — only after the result is known.
- Never duplicate entries already written by the hook.
- Never alter existing entries.
- Follow the exact format above — no new sections or fields.
