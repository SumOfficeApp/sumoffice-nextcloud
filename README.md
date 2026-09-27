# SumOffice Office for Nextcloud

Connects **Nextcloud Office** (richdocuments) to a self-hosted **SumOffice** stack — SumSheet for Excel files (`.xlsx`, `.xlsm` with macros, `.xlsb`) and SumDoc for Word files (`.docx`) — from one settings field. Files stay real Excel and Word files; macros and Power Query travel with them; Excel and Word open the result without a repair dialog.

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

## Collaboration measured for the 27 September images

On Nextcloud 31 with Nextcloud Office 8.8.2, two different users edited the same
XLSM and DOCX through the normal Files flow. Both users' changes survived in each
final file. The XLSM kept its VBA project byte-for-byte; Microsoft Excel and Word
opened the results without repair dialogs. Use the pinned `2026.09.27-amd64`
editor images from `SumOfficeApp/sumoffice-docker`.

## What does not survive yet (honestly)

- Power Query is preserved, not refreshed, in the browser editor (refresh is in the desktop app).
- This collaboration result is measured for Nextcloud/WOPI. Other host adapters
  need their own acceptance run before concurrent editing is promised there.
- Macros routed "Excel bridge" (COM automation, some ActiveX) stay in Excel; the report names each one.

## Licence and support

AGPL-3.0 for this app. The editors are free for evaluation; production use is licensed per application — https://sumoffice.com/download#server. Questions, or a file that did not open right: hello@sumoffice.com · issues here.
