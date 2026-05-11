#!/usr/bin/env bash
# Appends slash-command invocations to prompts.md at the project root.
# Invoked by the Claude Code UserPromptSubmit hook — runs with project root as CWD.
set -euo pipefail

PROMPTS_FILE="./prompts.md"

# Read JSON payload from stdin
INPUT=$(cat)

# Extract the prompt text; exit silently if jq is unavailable or field is missing
PROMPT=$(echo "$INPUT" | jq -r '.prompt // empty' 2>/dev/null) || exit 0

# Only log slash commands
[[ -z "$PROMPT" ]] && exit 0
[[ "${PROMPT:0:1}" != "/" ]] && exit 0

# Split into command name and the rest
CMD=$(echo "$PROMPT" | awk '{print $1}')
ARGS=$(echo "$PROMPT" | sed "s|^${CMD}||" | sed 's/^[[:space:]]*//')
# Truncate args preview to 300 chars for readability
ARGS_PREVIEW=$(echo "$ARGS" | head -c 300)

TIMESTAMP=$(date '+%Y-%m-%d %H:%M')

{
  printf '\n'
  printf '## %s — %s\n' "$TIMESTAMP" "$CMD"
  if [[ -n "$ARGS_PREVIEW" ]]; then
    printf '**Args:**\n'
    # Indent multi-line args as a blockquote
    while IFS= read -r line; do
      printf '> %s\n' "$line"
    done <<< "$ARGS_PREVIEW"
  else
    printf '**Args:** none\n'
  fi
} >> "$PROMPTS_FILE"

exit 0
