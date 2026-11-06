# SumOffice Office for Nextcloud

Connects **Nextcloud Office** (richdocuments) to a self-hosted **SumOffice** stack — SumSheet for Excel files (`.xlsx`, `.xlsm` with macros, `.xlsb`), SumDoc for Word (`.docx`) and SumSlide for PowerPoint (`.pptx`) — from one settings field. Files stay real Office files; macros and Power Query travel with spreadsheets.

![Settings](https://sumoffice.com/media/nextcloud-app-settings.png)

## What the app does

- One field — the public address of your SumOffice stack (e.g. `https://office.example.com`) — and **Connect**.
- Writes `wopi_url` / `public_wopi_url` for Nextcloud Office and activates the configuration (the same three `occ` settings, from the UI).
- Checks WOPI discovery and capabilities of the stack before saving and shows the status: which editors answer, which formats are covered.

## Requirements

- Nextcloud 29–32, Nextcloud Office (richdocuments) ≥ 8.
- A SumOffice stack reachable from Nextcloud and from users' browsers: compose files and the guide are in [SumOfficeApp/sumoffice-docker](https://github.com/SumOfficeApp/sumoffice-docker) (`nextcloud/`), the three-step page is https://sumoffice.com/nextcloud.

## Install

From the Nextcloud app store (category Office), or manually: unpack the release archive into `apps/sumoffice` and enable the app. Then Settings → Administration → SumOffice → address → Connect.

## What was measured for the 28 September images

On Nextcloud 31.0.14 with Nextcloud Office 8.8.2, through the normal Files flow:

- **XLSM, two different users in one workbook:** both users' changes were in the
  final file on the server, and the VBA project came back byte-for-byte identical.
  The same round on the previous images did not reach the typing stage at all, and
  the file was rewritten without either change.
- **DOCX, one user:** the change reached the file, embedded pictures intact, package
  valid. **Two users in one DOCX was not measured**, so this release does not claim
  simultaneous Word editing.
- **PPTX:** 0.1.5 detects and requires the presentation discovery action so an incomplete two-editor
  stack cannot be saved as healthy. The module package is ready for a live Nextcloud → SumSlide →
  new-version circle, but that circle has not been claimed or submitted yet.
- Package integrity was checked directly (parts, media, ZIP structure, VBA bytes).
- **Desktop Microsoft Excel and Word opened the returned files with no repair
  dialog** — measured 29 September against the published image by digest
  (`sha256:527afe26004a…`), with a known-good and a deliberately damaged file in the
  same run, so a quiet "clean" is a real observation.

Use the pinned `2026.09.28-amd64` editor images from `SumOfficeApp/sumoffice-docker`.

## What does not survive yet (honestly)

- Power Query is preserved, not refreshed, in the browser editor (refresh is in the desktop app).
- This collaboration result is measured for Nextcloud/WOPI. Other host adapters
  need their own acceptance run before concurrent editing is promised there.
- Macros routed "Excel bridge" (COM automation, some ActiveX) stay in Excel; the report names each one.
- Presentation co-editing is not claimed until two users' changes are observed in one returned PPTX.

## Licence and support

AGPL-3.0 for this app. The editors are free for evaluation; production use is licensed per application — https://sumoffice.com/download#server. Questions, or a file that did not open right: hello@sumoffice.com · issues here.
