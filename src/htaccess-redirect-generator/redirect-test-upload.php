<?php

// Does the file have a header?
$fileGotHeaderRow = false;

if(isset($_POST['fileGotHeaderRow'])) {
    $fileGotHeaderRow = true;
}

// replaceUrlPart
$replaceUrl = false;
$replaceUrlPart = trim(getPostVar('replaceUrlPart'));
$replaceUrlWith = trim(getPostVar('replaceUrlWith'));

if(strlen($replaceUrlPart) > 0) {
    $replaceUrl = true;
}

// Open stream to read from file.
$fp = fopen($_FILES['fileToUpload']['tmp_name'], 'r');

if(false === $fp) {
    die('Beim Dateiupload ist etwas schiefgegangen.');
}

// Walk through file, row by row.
$countRows = 0;

while(!feof($fp)) {
    $row = fgetcsv($fp, 99999, ';', '"', '\\');

    if(false === $row) {
        break;
    }

    if(count($row) != 2 && count($row) != 0) {
        die('Fehler in der CSV-Datei. Die Zeile ' . ($countRows + 1) . ' enthält mehr oder weniger als zwei Datensätze.');
    }

    // Skip first line, if the csv file got a header row.
    if($countRows == 0 && $fileGotHeaderRow) {
        $countRows++;
        continue;
    }

    // Parse new url.
    if($replaceUrl) {
        $row[1] = str_replace($replaceUrlPart, $replaceUrlWith, $row[1]);
    }

    echo '<a target="_blank" href="' . $row[0] . '">' . $row[0] . '</a>, sollte weiterleiten auf: <b>' . $row[1] . "</b><br /><br />";

    $countRows++;
}

die;

/*
 * Post Daten prüfen und zurückgeben. 
 */
function getPostVar($fieldname) {
    if(!isset($_POST[$fieldname])) {
        die('Die erwartete Post-Variable <b>' . htmlspecialchars($fieldname) . '</b> wurde nicht gefunden.');
    }

    return $_POST[$fieldname];
}