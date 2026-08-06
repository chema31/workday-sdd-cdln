#!/usr/bin/env bash
set -euo pipefail

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BOLD='\033[1m'
RESET='\033[0m'

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
KIT_DIR="$SCRIPT_DIR"
KIT_NAME="$(basename "$KIT_DIR")"
PROJECT_ROOT="$(cd "$KIT_DIR/.." && pwd)"

count_ok=0
count_skip=0
count_backup=0
count_error=0

# Global used by handle_conflict to return the chosen action
CONFLICT_ACTION=""

print_header() {
    echo -e "\n${BOLD}Installing ${KIT_NAME} into ${PROJECT_ROOT}${RESET}\n"
}

symlink_is_correct() {
    local link_path="$1"
    local expected_target="$2"
    [ -L "$link_path" ] && [ "$(readlink "$link_path")" = "$expected_target" ]
}

handle_conflict() {
    local dest="$1"
    local label="$2"
    local existing_type

    if [ -L "$dest" ]; then
        existing_type="symlink → $(readlink "$dest")"
    elif [ -d "$dest" ]; then
        existing_type="directory"
    else
        existing_type="file"
    fi

    echo -e "  ${YELLOW}[?]${RESET}   ${BOLD}${label}${RESET} already exists (${existing_type})"
    echo -n "        → [s]kip / [o]verwrite / [b]ackup and overwrite / [a]bort: "

    while true; do
        read -r answer </dev/tty
        case "$answer" in
            s|S) CONFLICT_ACTION="skip";   return 0 ;;
            o|O) CONFLICT_ACTION="overwrite"; return 0 ;;
            b|B) CONFLICT_ACTION="backup"; return 0 ;;
            a|A)
                echo -e "\n  ${RED}Aborted.${RESET} No further changes made."
                exit 1
                ;;
            *) echo -n "        Please enter s, o, b or a: " ;;
        esac
    done
}

apply_conflict_resolution() {
    local dest="$1"
    local label="$2"

    case "$CONFLICT_ACTION" in
        skip)
            echo -e "  ${YELLOW}[SKIP]${RESET}  ${label} skipped"
            count_skip=$((count_skip + 1))
            return 1
            ;;
        overwrite)
            rm -rf "$dest"
            return 0
            ;;
        backup)
            local backup="${dest}.bak.$(date +%Y%m%d-%H%M%S)"
            mv "$dest" "$backup"
            echo -e "  ${YELLOW}[BACKUP]${RESET} ${label} → $(basename "$backup")"
            count_backup=$((count_backup + 1))
            return 0
            ;;
    esac
}

create_symlink() {
    local src_rel="$1"
    local dest_rel="$2"
    local src="${PROJECT_ROOT}/${src_rel}"
    local dest="${PROJECT_ROOT}/${dest_rel}"
    local label
    label="$(basename "$dest_rel")"

    if [ ! -e "$src" ]; then
        echo -e "  ${RED}[ERROR]${RESET} Source not found: ${src_rel}"
        count_error=$((count_error + 1))
        return
    fi

    if symlink_is_correct "$dest" "$src_rel"; then
        echo -e "  ${YELLOW}[SKIP]${RESET}  ${label} already installed"
        count_skip=$((count_skip + 1))
        return
    fi

    if [ -e "$dest" ] || [ -L "$dest" ]; then
        handle_conflict "$dest" "$label"
        apply_conflict_resolution "$dest" "$label" || return
    fi

    mkdir -p "$(dirname "$dest")"
    ln -s "$src_rel" "$dest"
    echo -e "  ${GREEN}[OK]${RESET}   ${label} → ${src_rel}"
    count_ok=$((count_ok + 1))
}

create_copilot_symlink() {
    local src_rel="${KIT_NAME}/.github/copilot-instructions.md"
    local dest_rel=".github/copilot-instructions.md"
    local src="${PROJECT_ROOT}/${src_rel}"
    local dest="${PROJECT_ROOT}/${dest_rel}"
    # Relative from inside .github/ to the kit source
    local link_target="../${src_rel}"
    local label=".github/copilot-instructions.md"

    if [ ! -e "$src" ]; then
        echo -e "  ${YELLOW}[SKIP]${RESET}  copilot-instructions.md not found in kit, skipping GitHub Copilot setup"
        count_skip=$((count_skip + 1))
        return
    fi

    local github_dir="${PROJECT_ROOT}/.github"
    if [ -L "$github_dir" ]; then
        echo -e "  ${RED}[ERROR]${RESET} .github is a symlink — cannot create directory, skipping Copilot setup"
        count_error=$((count_error + 1))
        return
    fi
    mkdir -p "$github_dir"

    if symlink_is_correct "$dest" "$link_target"; then
        echo -e "  ${YELLOW}[SKIP]${RESET}  ${label} already installed"
        count_skip=$((count_skip + 1))
        return
    fi

    if [ -e "$dest" ] || [ -L "$dest" ]; then
        handle_conflict "$dest" "$label"
        apply_conflict_resolution "$dest" "$label" || return
    fi

    ln -s "$link_target" "$dest"
    echo -e "  ${GREEN}[OK]${RESET}   ${label} → ${link_target}"
    count_ok=$((count_ok + 1))
}

GITIGNORE_MARKER="# dln-spec-kit — managed by install.sh"

GITIGNORE_ENTRIES=(
    "${KIT_NAME}/"
    "CLAUDE.md"
    "AGENTS.md"
    "GEMINI.md"
    "codex.md"
    ".claude"
    ".cursor"
    ".codex"
    "rules"
    ".github/copilot-instructions.md"
)

update_gitignore() {
    local gitignore="${PROJECT_ROOT}/.gitignore"

    echo -e "\n${BOLD}Updating .gitignore${RESET}"

    if ! grep -qF "$GITIGNORE_MARKER" "$gitignore" 2>/dev/null; then
        printf '\n%s\n' "$GITIGNORE_MARKER" >> "$gitignore"
    fi

    local entry
    for entry in "${GITIGNORE_ENTRIES[@]}"; do
        if grep -qF "$entry" "$gitignore" 2>/dev/null; then
            echo -e "  ${YELLOW}[SKIP]${RESET}  .gitignore: ${entry} already present"
            count_skip=$((count_skip + 1))
        else
            printf '%s\n' "$entry" >> "$gitignore"
            echo -e "  ${GREEN}[OK]${RESET}   .gitignore: ${entry}"
            count_ok=$((count_ok + 1))
        fi
    done
}

print_summary() {
    echo -e "\n${BOLD}Summary:${RESET} ${GREEN}${count_ok} installed${RESET}, ${YELLOW}${count_skip} skipped${RESET}, ${YELLOW}${count_backup} backed up${RESET}, ${RED}${count_error} errors${RESET}\n"
}

main() {
    print_header

    create_symlink "${KIT_NAME}/CLAUDE.md" "CLAUDE.md"
    create_symlink "${KIT_NAME}/AGENTS.md" "AGENTS.md"
    create_symlink "${KIT_NAME}/GEMINI.md" "GEMINI.md"
    create_symlink "${KIT_NAME}/codex.md"  "codex.md"

    create_symlink "${KIT_NAME}/.claude"   ".claude"
    create_symlink "${KIT_NAME}/.cursor"   ".cursor"
    create_symlink "${KIT_NAME}/.codex"    ".codex"
    create_symlink "${KIT_NAME}/rules"     "rules"

    create_copilot_symlink

    update_gitignore

    print_summary
}

main "$@"
