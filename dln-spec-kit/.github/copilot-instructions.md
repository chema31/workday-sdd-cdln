# GitHub Copilot Instructions

This file is managed by **dln-spec-kit**. Do not edit it directly — edit the source at `dln-spec-kit/.github/copilot-instructions.md`.

## Core Principles

- Work in small, incremental steps. Never implement more than one task at a time.
- All code must be fully typed (TypeScript strict mode, PHP type hints, Python type annotations).
- Use clear, descriptive names for all variables, functions, and classes.
- Write no comments unless the *why* is non-obvious. Never describe what the code does.
- Do not add error handling, fallbacks, or abstraction beyond what the task requires.

## Language Standards

All technical artifacts must use **English only**: code, comments, documentation, commit messages, test names, configuration files, and database schemas.

## Standards Reference

Detailed guidelines are in the `rules/` directory:

- `rules/base-standards.mdc` — Core principles and index of all standards files
- `rules/coding-standards.mdc` — General standards for all languages: naming, data types, SOLID, QA, deployment
- `rules/laravel-standards.mdc` — Laravel API and Admin Panel: architecture, Eloquent, Jobs, Passport, PSR, testing
- `rules/python-standards.mdc` — Python standalone processes: architecture, type hints, testing, deployment
- `rules/ci4-standards.mdc` — CodeIgniter 4 commercial websites: simple MVC, routing, views
- `rules/frontend-standards.mdc` — React components, UI/UX guidelines, frontend architecture
- `rules/documentation-standards.mdc` — Technical documentation structure and formatting
- `rules/playwright-standards.mdc` — Functional testing requirements for User Stories
- `rules/git-standards.mdc` — Branch strategy, naming conventions, pull requests, CI/CD

## Project Skills

Reusable prompt workflows live in `dln-specs/skills/`. When a request matches a skill, load and follow its `SKILL.md` automatically before continuing.
