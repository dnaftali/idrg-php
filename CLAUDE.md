# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A PHP web app on the **SIMRS (hospital information system) side** that drives medical-claim coding through the Kementerian Kesehatan **E-Klaim** web service (iDRG + INA-CBG groupers). It is a *client* of E-Klaim, not the grouper itself — all grouping/tariff logic lives on the remote E-Klaim server, which this app reaches over HTTP. `Manual Web Service 5.10.7.md` and `E-KLAIM IDRG.postman_collection.json` are the authoritative reference for the remote API's methods, parameters, and error codes.

## Running / environment

There is no build step, package manager, or test suite. It runs under Apache + PHP 8.0+ + MySQL 5.7+ (XAMPP-style), served from the web root.

- App home (patient list): `http://localhost/idrg-php/`
- Coding UI: `http://localhost/idrg-php/coding_idrg.php?patient_id=<id>`
- Required PHP extensions: PDO, cURL, JSON.
- DB schema + data is a single dump: `idrg.sql` (~9 MB). Import it to bootstrap a database named `idrg`. Note the connection uses **latin1** charset (`config/database.php`).
- Logs are written to `logs/` (auto-created): `eklaim_YYYY-MM-DD.log`, `web_service_requests.log`, `web_service_responses.log`.

Edit `config/database.php` and `config/eklaim_config.php` for local setup — both currently hold hardcoded LAN values and must be pointed at your own DB and E-Klaim server.

## Architecture

**Request flow:** browser (`index.php`, `coding_idrg.php`) → `fetch()` POST to an endpoint in `api/` → `api/eklaim_new_claim.php` dispatches → a `handle*` function → an E-Klaim wrapper function in `config/eklaim_config.php` → `sendEklaimRequest()` → remote `ws.php`.

- **`api/eklaim_new_claim.php`** is the central dispatcher (~2000 lines). It reads JSON from `php://input`, branches on the top-level `"action"` field via one big `switch`, and each `handle*()` function shapes the request, calls the wrapper, persists tracking, and returns a `{success, ...}` array that is echoed as JSON. To add an E-Klaim operation you add a `case` here plus its handler.
- **`config/eklaim_config.php`** holds connection constants and one wrapper function per E-Klaim method (`createNewClaim`, `setClaimData`, etc.). Every remote call is built as `{"metadata": {"method": ...}, "data": {...}}` and POSTed by `sendEklaimRequest()` (cURL, with a `file_get_contents` fallback).
- Other `api/*.php` files are narrower endpoints (patient lists, ICD code search/autocomplete, reading saved coding, validation) that talk to the local DB directly.

**Two-layer terminology — do not conflate them.** A claim is coded twice: first **iDRG** (ICD-10-IM diagnoses via `idrg_diagnosa_set`, ICD-9-IM procedures), then the result is imported into **INA-CBG** (ICD-10 / ICD-9-CM). `idrg_to_inacbg_import` bridges the two. The DB and UI keep parallel `*_idrg`/`*_inacbg` fields and the manual documents separate method sets for each — match the layer the code is in.

**Canonical workflow** (the order handlers expect): `new_claim` → `set_claim_data` → set iDRG diagnosa/procedure → `grouper` stage 1 (`grouper: "idrg"`) → optional stage 2 (top-up codes) → `idrg_grouper_final` → `idrg_to_inacbg_import` → set INA-CBG diagnosa/procedure → `grouper` stage 1 (`grouper: "inacbg"`) → optional stage 2 (`special_cmg`) → `inacbg_grouper_final` → `claim_final` → send online / print / re-edit. `set_claim_data` and `claim_final` require a registered `coder_nik`.

## Conventions that aren't obvious from a single file

- **Grouping validity:** an iDRG result is invalid/ungroupable when `mdc_number == "36"` (codes prefixed `36`); an INA-CBG result is invalid when the CBG `code` starts with `X`. The UI must hide the "final"/next-stage button on an invalid result. See the ungroupable code table in the manual.
- **Method tracking:** `functions/eklaim_method_tracking.php` records every E-Klaim call in the `eklaim_method_tracking` table keyed by `nomor_sep` + a two-digit `method_code` (`01`–`21`, validated by regex), with status `pending|success|error|skipped`, retry count, and execution time. Method-code → name comes from `eklaim_method_mapping`. New calls should track themselves the same way.
- **Encryption is NOT implemented here.** The manual mandates AES-256-CBC (`inacbg_encrypt`/`inacbg_decrypt`) for production, but this app runs against E-Klaim in **debug mode** (`EKLAIM_DEBUG_MODE = true`, which appends `?mode=debug` and sends/receives plaintext JSON). Going to production requires adding that encryption layer around `sendEklaimRequest()`.
- **Import is delete-then-insert** keyed on `nomor_sep`, into `import_coding_diagnosis` / `import_coding_procedure`, logged in `import_coding_log` (`includes/import_coding_db.php`). It backfills `icd_description` from master tables.
- **Fallback description:** when a code is missing from `inacbg_codes`, look it up in `idr_codes` and display it flagged "(IM tidak berlaku)" — IM (Indonesian Modification) codes are not all valid in plain INA-CBG.

## Key tables

`kunjungan_pasien` (patients + grouping status), `idr_codes` / `inacbg_codes` (ICD masters), `eklaim_method_tracking` + `eklaim_method_mapping` (call tracking), `import_coding_log` / `import_coding_diagnosis` / `import_coding_procedure` (iDRG→INA-CBG import).
