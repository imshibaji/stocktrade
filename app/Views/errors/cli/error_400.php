<?php
echo "\033[33m";
echo "  __  __ _     _____ _______ _    _  ____  \n";
echo " |  \/  | |   / ____|__   __| |  | |/ __ \ \n";
echo " | \  / | |  | (___    | |  | |  | | |  | |\n";
echo " | |\/| | |   \___ \   | |  | |  | | |  | |\n";
echo " | |  | | |___ ____) |  | |  | |__| | |__| |\n";
echo " |_|  |_|_____|_____/   |_|   \____/ \____/ \n";
echo "\033[0m\n";
echo "Message: " . ($message ?? 'Unknown error') . "\n\n";
if (isset($exception)) {
    echo $exception->getMessage() . "\n";
    echo $exception->getTraceAsString() . "\n";
}