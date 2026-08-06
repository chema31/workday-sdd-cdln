# Development Plan

## Overview

Finish the already-started development of workday-sdd-cdln, a centralised time-tracking application, and bring it to a state fit for real internal use. The application currently manages employee accounts and displays attendance records, but the clock-in/clock-out button is a placeholder with no write logic, so the product does not yet do the one thing it exists to do. This plan closes that gap, adds the administrative and reporting capabilities that daily real-world use and Spanish working-time regulation (RD-ley 8/2019) demand, and ends with the infrastructure work needed to run the application in production on a Docker-based VPS.

## Scope

**Included:**
- Completion of US-001 (user management and application bootstrap), which is already enriched and has 9 specs pending implementation
- Real clock-in / clock-out write logic, including closing an open shift and the in-progress shift timer
- Administrative management and correction of attendance records, including closing shifts left open by a forgotten clock-out
- Attendance export by employee and period, and employee access to their full history rather than only the current month
- Production readiness: application security hardening, Docker packaging and an automated deployment pipeline

**Excluded:**
- Self-service password reset flow (administrators reset passwords from the Filament employee CRUD)
- Email notifications on account creation
- Per-employee timezone configuration
- Any change to the technology stack: Laravel 11, Filament 3.x, Blade with Tailwind and Alpine.js, MySQL

## User Stories

- [ ] finish-user-management-bootstrap
- [ ] clock-in-clock-out-logic
- [ ] admin-clock-record-management
- [ ] attendance-export-and-history
- [ ] production-security-hardening
- [ ] production-docker-setup
- [ ] deployment-pipeline

## Notes

- `finish-user-management-bootstrap` refers to US-001, which already has an ID and is already enriched, with its 9 pending specs in `openspec/specs/`. It must **not** be passed through `/enrich-us` — doing so would assign a second ID and duplicate the story. It is listed here so the plan reflects the complete remaining work.
- The four functional stories are ordered by dependency: attendance records cannot be managed or exported before they can be created.
- Infrastructure stories come last. Within them, security hardening precedes Docker packaging because it is application-level configuration (sessions, HTTPS enforcement, login rate limiting) that shapes how the image is built.
- `production-security-hardening` handles personal employment data. When that story is enriched, it should cover GDPR access and retention obligations for attendance records, not only transport and session security.
