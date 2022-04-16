<?php

class Utilities
{
    /**
     * Removes slashes and converts special characters into HTML entities
     * @param string $input
     * @return string
     */
    static function cleanInput(string $input) : string
    {
        $input = trim($input);
        $input = stripslashes($input);
        return htmlspecialchars($input);
    }
}