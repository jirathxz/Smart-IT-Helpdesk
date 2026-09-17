@echo off
setlocal enabledelayedexpansion
title Smart IT Helpdesk - Server Manager

:: Navigate to current script directory
cd /d "%~dp0"

:: Set Environment Paths for PHP, MariaDB, and Apache
set "PHP_PATH=C:\tools\php"
set "MARIADB_PATH=C:\tools\mariadb\bin"
set "APACHE_PATH=C:\tools\apache\Apache24\bin"
set "PATH=%PHP_PATH%;%MARIADB_PATH%;%APACHE_PATH%;%PATH%"

:: Tool locations
set "HTTPD_EXE=C:\tools\apache\Apache24\bin\httpd.exe"
set "MARIADBD_EXE=C:\tools\mariadb\bin\mariadbd.exe"
set "MARIADB_ADMIN=C:\tools\mariadb\bin\mariadb-admin.exe"
set "MYSQL_CLI=C:\tools\mariadb\bin\mariadb.exe"

:START_SERVERS
cls
echo ================================================================
echo           Smart IT Helpdesk - Starting Web Server Stack
echo ================================================================
echo.

:: 1. Verify Prerequisites
if not exist "%HTTPD_EXE%" (
    echo [ERROR] Apache executable not found at %HTTPD_EXE%
    goto PAUSE_EXIT
)
if not exist "%MARIADBD_EXE%" (
    echo [ERROR] MariaDB executable not found at %MARIADBD_EXE%
    goto PAUSE_EXIT
)
if not exist "%PHP_PATH%\php.exe" (
    echo [ERROR] PHP executable not found at %PHP_PATH%\php.exe
    goto PAUSE_EXIT
)

:: 2. Start MariaDB if not running
tasklist /FI "IMAGENAME eq mariadbd.exe" 2>NUL | find /I /N "mariadbd.exe" >NUL
if "%ERRORLEVEL%"=="0" (
    echo [INFO]  MariaDB is already running.
) else (
    echo [START] Launching MariaDB 11.4 on port 3306...
    start "MariaDB-Server" /min "%MARIADBD_EXE%" --console
)

:: 3. Start Apache if not running
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe" >NUL
if "%ERRORLEVEL%"=="0" (
    echo [INFO]  Apache is already running.
) else (
    echo [START] Launching Apache 2.4 on port 8090...
    start "Apache-Server" /min "%HTTPD_EXE%"
)

:: Brief pause to let services initialize
echo.
echo [INFO]  Initializing services, please wait...
ping 127.0.0.1 -n 3 >nul

:: Launch browser for project and phpMyAdmin
start http://localhost:8090
start http://localhost:8090/phpmyadmin

:MENU
cls
echo ================================================================
echo           Smart IT Helpdesk - Server Control Center
echo ================================================================
echo   [STATUS]
echo    - Apache 2.4 : http://localhost:8090
echo    - PHP 8.3    : Loaded (Apache PHP Module)
echo    - MariaDB    : 127.0.0.1:3306 (user: root, password: [blank])
echo    - phpMyAdmin : http://localhost:8090/phpmyadmin
echo    - DocRoot    : %CD%\htdocs
echo ================================================================
echo.
echo   [1] Open Project in Browser      (http://localhost:8090)
echo   [2] Open phpMyAdmin in Browser   (http://localhost:8090/phpmyadmin)
echo   [3] Open MariaDB / MySQL Shell   (CLI client)
echo   [4] Restart All Services         (Apache + MariaDB)
echo   [5] Stop All Services and Exit
echo   [Q] Quit Launcher (Keep servers running in background)
echo.
echo ================================================================
set /p "CHOICE=Select an option [1-5, Q]: "

if "%CHOICE%"=="1" (
    start http://localhost:8090
    goto MENU
)
if "%CHOICE%"=="2" (
    start http://localhost:8090/phpmyadmin
    goto MENU
)
if "%CHOICE%"=="3" (
    start "MariaDB Client" "%MYSQL_CLI%" -u root
    goto MENU
)
if "%CHOICE%"=="4" (
    echo.
    echo [RESTART] Stopping services...
    call :STOP_SERVICES
    ping 127.0.0.1 -n 2 >nul
    goto START_SERVERS
)
if "%CHOICE%"=="5" (
    echo.
    echo [STOP] Shutting down all services...
    call :STOP_SERVICES
    echo.
    echo [DONE] All services have been stopped.
    ping 127.0.0.1 -n 2 >nul
    exit /b 0
)
if /i "%CHOICE%"=="Q" (
    echo.
    echo Servers will remain running in the background.
    echo To stop them later, run stop.bat or use Option 5.
    ping 127.0.0.1 -n 2 >nul
    exit /b 0
)

echo [INVALID] Please enter a valid option.
ping 127.0.0.1 -n 2 >nul
goto MENU

:STOP_SERVICES
taskkill /F /IM httpd.exe >nul 2>&1
if exist "%MARIADB_ADMIN%" (
    "%MARIADB_ADMIN%" -u root shutdown >nul 2>&1
)
taskkill /F /IM mariadbd.exe >nul 2>&1
goto :eof

:PAUSE_EXIT
echo.
echo Press any key to exit...
pause >nul
exit /b 1
