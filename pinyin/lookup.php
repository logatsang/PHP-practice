<?php

require 'pinyin.php';

// Error checking
if (empty($_POST["lang"])) {
    echo "Please choose a language.";
    exit();
}
if (empty($_POST["query"])) {
    echo "Please enter a query.";
    exit();
}

// Stores character information
class Character {
    public $code;
    public $definition;

    private $char;

    function __construct($code) {
        $this->code = $code;
    }

    // Converts codepoint to actual character
    function get_char() {
        $this->char = mb_chr(intval(substr($this->code, 2), 16), "utf8");
        return $this->char;
    }    

    function __toString() {
        return "<tr><td style='text-align:center'>" . $this->char . "</td><td>" . $this->definition . "</td></tr>";
    }
}

// Cleans data
function sanitize($data) { return htmlspecialchars(trim($data)); }

$lang = sanitize($_POST["lang"]);
$query = strtolower(sanitize($_POST["query"]));
if (!strcmp($lang, "kMandarin")) $query = pinyin_addaccents($query);

// Search for given reading
$dict = fopen("Unihan_Readings.txt", "r") or die("Failed to locate dictionary file");

$results = array();
$currchar = NULL;
$added = FALSE;

// Main loop through dictionary file
while (!feof($dict)) {
    $currline = fgets($dict);
    if (strlen($currline) > 0 and !strcmp($currline[0], "#")) {
        continue;
    }

    $parts = array_map('trim', explode("\t", $currline));
    if (count($parts) >= 3) {
        
        // Choose what to do with the object
        if (is_null($currchar) or strcmp($parts[0], $currchar->code)) {
            if (!$added) unset($currchar);
            else $currchar->get_char();

            // Reset settings for new character
            $currchar = new Character($parts[0]);
            $added = FALSE;
        }
        
        // Add character
        if (!strcmp($parts[1], $lang) and !strcmp($parts[2], $query)) {
            $added = TRUE;
            $results[$parts[0]] = $currchar;
        }

        // Add definition
        else if (!strcmp($parts[1], "kDefinition")) {
            $currchar->definition = $parts[2];
        }
    }
}
fclose($dict);

?>

<head>
    <title>Search results for "<?php echo $query ?>"</title>
</head>
<body>
    <?php echo "Found " . sizeof($results) . " results for reading <em>" . $query . "</em>.<br><hr>"; ?>
    <table>
        <tr>
            <th>Character</th>
            <th>Definition</th>
        </tr>
        <?php foreach($results as $x => $x_value) echo $x_value;?>
    </table>

    <hr>

    <a href="" onclick="history.back(1)">Back</a>
</body>