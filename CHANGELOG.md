# Changelog

All notable changes to this project will be documented in this file.

## 1.0.0 - 2026-11-17

First release: California data breach register (Cal. Civ. Code §1798.82) for Laravel, part of the laravel-ccpa suite.

- Breaches with your own notification target (30 days by default, or none): California only says "without unreasonable delay".
- An assessment of whether notification is required, with the reasoning recorded.
- A law-enforcement delay that stops the clock (§1798.82(c)).
- Resident notices, including substitute notice (§1798.82(j)), and the sample copy to the Attorney General above 500 residents (§1798.82(f)). The breach can't be closed while a notice is owed.
- `notice()` returns the "Notice of Data Breach" title and headings, in the order §1798.82(d)(1) requires.
- The `pending` and `overdue` scopes, the `BreachOpened` and `BreachOverdue` events, and `ccpa:breaches-overdue`.

## [Unreleased]
