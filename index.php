<?php
// Get file
if (count($argv) == 1) {
    echo "Not enough args";
    return;
}

$file = $argv[1];

if (!file_exists($file) || !is_file($file)) {
    echo "File don't exist";
    return;
}

// Setup
$values = array_fill(0, 32, 0);
$cursor = 0;
$copied_values = array_fill(0, 32, 0);

// Read file
$str = fread(fopen($file, "r"), filesize($file));

function clamp(int|float $value) {
    if ($value < 0) {
        return 0;
    } else if ($value > 255) {
        return 255;
    } else {
        return $value;
    }
}

for ($i = 0; $i < strlen($str); ++$i) {
    $char = trim($str[$i]);
    if (strlen($char) == 0) continue;

    switch ($char) {
        case ">":
            if ($cursor < 31) ++$cursor;
            break;
        case "<":
            if ($cursor > 0) --$cursor;
            break;

        case ")":
            $values[$cursor] = clamp($values[$cursor] + 1);
            break;
        case "]":
            $values[$cursor] = clamp($values[$cursor] + 10);
            break;
        case "}":
            $values[$cursor] = clamp($values[$cursor] + 100);
            break;
        case "(":
            $values[$cursor] = clamp($values[$cursor] - 1);
            break;
        case "[":
            $values[$cursor] = clamp($values[$cursor] - 10);
            break;
        case "{":
            $values[$cursor] = clamp($values[$cursor] - 100);
            break;

        case "!":
            $values[$cursor] = 0;
            break;
        case "$":
            $values = array_fill(0, 32, 0);
            break;
        case ".":
            $cursor = 0;
            break;

        case "@":
            echo "$values[$cursor]\n";
            break;
        case "&":
            echo "$cursor\n";
            break;
        case "#":
            print_r($values);
            break;
        case "=":
            echo pack('C*', ...$values)."\n";
            break;

        case "'":
            $copied_values = $values;
            break;
        case '"':
            $values = $copied_values;
            break;
        case "~":
            $values = array_reverse($values);
            break;
    }
}
?>
