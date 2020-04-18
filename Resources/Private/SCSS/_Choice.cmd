@echo off
CLS

:DEFAULTS
SET SASS_SETTINGS=--style compressed --no-source-map

:MENU
ECHO.
ECHO Render for Context...:
ECHO.
ECHO   D. Development
ECHO   P. Production
ECHO.
CHOICE /C DP /T 5 /D P /M "Maak een keuze"

IF ERRORLEVEL 2 GOTO CHOICE2
IF ERRORLEVEL 1 GOTO CHOICE1
GOTO NONE

:NONE
ECHO ------------------------------------------
ECHO - INVALID or UNDEFINED CHOICE [1535096705]
ECHO ------------------------------------------
SET OTAP=
GOTO END

:CHOICE1
SET OTAP=O
SET SASS_SETTINGS=--style expanded --no-source-map
ECHO --------------------------------
ECHO - DEV settings activated
ECHO - Settings: %SASS_SETTINGS%
ECHO --------------------------------
GOTO END

:CHOICE2
SET OTAP=P
SET SASS_SETTINGS=--style compressed --no-source-map
ECHO --------------------------------
ECHO - PROD settings activated
ECHO - Settings: %SASS_SETTINGS%
ECHO --------------------------------
GOTO END

:END

