@echo off
chcp 65001 > nul
echo Running SAC Data Master Import Runner...
php run_all_imports.php
pause
