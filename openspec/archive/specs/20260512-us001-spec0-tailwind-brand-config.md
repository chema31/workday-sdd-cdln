# Spec 0: Accom Brand Tailwind Configuration

## Metadata
- **User Story**: [US-001](../features/20260511-user-management-and-application-bootstrap.md)
- **Type**: Frontend (build config)
- **Branch**: feature/20260511_user_management_and_application_bootstrap
- **Estimated effort**: S
- **Depends on**: Spec 1 (Laravel + Tailwind must be installed first)

## Overview
Register Accom's three corporate colour tokens in `tailwind.config.js` so that Blade components can use `bg-accom-teal`, `text-accom-pink`, etc. as first-class Tailwind utilities throughout the app.

## Architecture Context
- File: `tailwind.config.js` (project root, created by Breeze in Spec 1)
- No PHP changes; no database changes.
- After this spec, `npm run dev` must compile without errors and the custom classes must be available in all Blade views.

## Implementation Steps

### Step 0: Verify Tailwind is installed
- Confirm `tailwind.config.js` exists (created by `breeze:install` in Spec 1).
- Confirm `resources/css/app.css` includes `@tailwind` directives.

### Step 1: Add Accom brand colour tokens
- **File**: `tailwind.config.js`
- **Action**: Add a `colors.accom` extension inside `theme.extend`.
- **Details**:

```js
// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                accom: {
                    teal:        '#2BBFB3',
                    'teal-light': '#E8F9F8',
                    pink:        '#E8195A',
                },
            },
        },
    },
    plugins: [forms],
};
```

### Step 2: Verify compilation
- Run: `npm run dev` (or `npm run build`)
- Confirm: no errors in console output.
- Quick smoke-test: add `class="bg-accom-teal"` to any Blade view, inspect the compiled CSS to confirm `#2BBFB3` appears.
- Remove the smoke-test class after verification.

### Step LAST: Update Documentation
- No `rules/` changes needed for a colour config.
- Update `README.md` Design System section to note the brand tokens.

## Unit Test Specifications (TDD)
No automated tests for build-tool configuration. Acceptance is the successful `npm run dev` compilation verified in Step 2.

## Acceptance Criteria
- [ ] `tailwind.config.js` contains `accom.teal`, `accom.teal-light`, `accom.pink` tokens.
- [ ] `npm run dev` compiles without errors.
- [ ] Custom Tailwind classes resolve correctly in compiled CSS.
- [ ] No debug markup left in any Blade file.
- [ ] Branch follows naming convention.
