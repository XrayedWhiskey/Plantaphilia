; NSIS installer customization for Plantaphilia ("Produkte")
; Loaded by electron-builder during NSIS builds (build.nsis.language = 1031 → German UI).
;
; Adds the install directory to the current user's PATH so the app can be
; started from any terminal by typing "produkte" (Windows PATH/file lookups
; are case-insensitive, so "PRODUKTE" works too).

!ifndef PRODUKTE_INSTALLER_NSH_INCLUDED
!define PRODUKTE_INSTALLER_NSH_INCLUDED

!include "WinMessages.nsh"

!macro customInstall
  DetailPrint "Füge PRODUKTE zur PATH-Umgebungsvariable hinzu..."
  ReadRegStr $0 HKCU "Environment" "Path"
  StrCmp $0 "" produkte_path_empty
    StrCpy $0 "$0;$INSTDIR"
    Goto produkte_path_write
  produkte_path_empty:
    StrCpy $0 "$INSTDIR"
  produkte_path_write:
  WriteRegExpandStr HKCU "Environment" "Path" "$0"
  SendMessage ${HWND_BROADCAST} ${WM_WININICHANGE} 0 "STR:Environment" /TIMEOUT=5000
!macroend

!endif
