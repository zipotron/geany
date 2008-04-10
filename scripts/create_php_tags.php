#!/usr/bin/php

<?php
// Author:	Matti Mårds
// License:	GPL V2 or later

// Script to generate a new php.tags file from a downloaded PHP function summary list from
// http://cvs.php.net/viewvc.cgi/phpdoc/funcsummary.txt?view=co
// The script expects a file funcsummary.txt in /tmp and will write the parsed tags into
// data/php.tags.
//
// wget -O funcsummary.txt "http://cvs.php.net/viewvc.cgi/phpdoc/funcsummary.txt?view=co"
//
// (the script should be run in the top source directory)


// Create an array of the lines in the file
$file = file('funcsummary.txt');

// Create template for a tag
// *Function name*|*Return type*|*Parameters*|*Description*
$tagTpl = "%s|%s|%s|%s";

// String to store the output
$tagsOutput = array();

// Iterate through each line of the file
for($line = 0, $lineCount = count($file); $line < $lineCount; ++$line) {

    // If the line isn't a function definition, skip it
    if(!preg_match('/^(?P<retType>\w+) (?P<funcName>[\w:]+)(?P<params>\(.*?\))/', $file[$line], $funcDefMatch)) {
        continue;
    }
    // Skip methods as they aren't used for now
    if (strpos($funcDefMatch['funcName'], '::') !== false) {
        continue;
    }

    // Get the function description
    //$funcDesc = trim($file[$line + 1]);
    // Geany doesn't use the function description (yet?), so use an empty string to save space
    $funcDesc = '';

    // Remove the void parameter, it will only confuse some people
    if($funcDefMatch['params'] === '(void)') {
        $funcDefMatch['params'] = '()';
    }

    // $funcDefMatch['funcName'] = str_replace('::', '->', $funcDefMatch['funcName']);

    $tagsOutput[] = sprintf($tagTpl, $funcDefMatch['funcName'],
        $funcDefMatch['retType'], $funcDefMatch['params'], $funcDesc);
}

// Sort the output
sort($tagsOutput);

file_put_contents('data/php.tags', join("\n", $tagsOutput));

?>
