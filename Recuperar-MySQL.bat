@echo off
title Recuperacion MySQL - SisDiT
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0scripts\Recuperar-MySQL.ps1"
echo.
pause
