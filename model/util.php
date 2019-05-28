<?php
/* Bruce Turner, Professor Ostrander, Spring 2019
 * IT 328 Full Stack Web Development
 * Dating IV Assignment: with form validation & sticky forms
 * file: util.php
 * date: Monday, May 27 2019
 * all of these have to do with the linking table member_interest.
 * they are not working now....
*/
/**
 * general utility code, not really worth commenting.... yet it
 * will clutter up index.php, and that is not going to win any
 * beauty awards.
 */
function getIndoorDBIndex($interest)
{
    $index = -1;
    switch ($interest) {
        case "tv":
            $index = 1;
            break;
        case "puzzles":
            $index = 2;
            break;
        case "movies":
            $index = 3;
            break;
        case "reading":
            $index = 4;
            break;
        case "cooking":
            $index = 5;
            break;
        case "playing cards":
            $index = 6;
            break;
        case "board games":
            $index = 7;
            break;
        case "video games":
            $index = 8;
            break;
        default:
            break;;
    }
    return $index;
}

function getOutdoorDBIndex($interest)
{
    $index = -1;
    switch ($interest) {
        case "hiking":
            $index = 1;
            break;
        case "walking":
            $index = 2;
            break;
        case "biking":
            $index = 3;
            break;
        case "climbing":
            $index = 4;
            break;
        case "swimming":
            $index = 5;
            break;
        case "collecting":
            $index = 6;
            break;
        default:
            break;;
    }
    return $index;
}