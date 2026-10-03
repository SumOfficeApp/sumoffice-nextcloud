## 0.1.5 — 2026-09-28
- Add SumSlide/PPTX to the product description and status message.
- Require PPTX in WOPI discovery together with DOCX and XLSX; an incomplete two-editor stack now
  fails the connection check instead of appearing healthy.
- Package prepared for live presentation acceptance; not submitted to the App Store.
- Release metadata now matches the measured 28 September editor images
  (`2026.09.28-amd64`).
- Two different Nextcloud users can edit the same **workbook** without the later save
  erasing the earlier user's change. Measured; on the previous images the same round
  did not reach typing and the file was rewritten without either change.
- Simultaneous editing of one **document** (DOCX) is not claimed: it was not measured.
- Excel and Word open the returned files with no repair dialog — re-measured 29 September
  against the published image by digest, with a known-good and a damaged file in the
  same run.
- The installer code is unchanged; publish the app archive separately from the
  Docker images only after App Store approval.

## 0.1.4 — 2026-09-26
- Release archive no longer contains macOS AppleDouble entries, so Nextcloud sees
  exactly one top-level `sumoffice` directory and can install the package.

## 0.1.3 — 2026-09-26
- Store card: two screenshots of the editors (a macro workbook and a document) next to the admin settings page.
- Description states the address rule that causes almost every "document failed to load": the address you give
  Nextcloud Office must be reachable by the browser, over https or from the same origin as Nextcloud.

## 0.1.2 — 2026-09-24
- App Store metadata: bug tracker link is now the GitHub issues page (the store requires an http(s) URL); no functional changes.

## 0.1.0 — 2026-09-20
- First release: point Nextcloud Office at a SumOffice server with one field; discovery and capabilities check; status with the server name and the formats it offers.
