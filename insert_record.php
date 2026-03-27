<?php
# TOOK THIS FROM CHATGPT
function mysqli_type_to_bind($type) {
    switch ($type) {
        // Integers
        case MYSQLI_TYPE_TINY:
        case MYSQLI_TYPE_SHORT:
        case MYSQLI_TYPE_LONG:
        case MYSQLI_TYPE_LONGLONG:
        case MYSQLI_TYPE_INT24:
            return "i";

        // Floating point / decimal
        case MYSQLI_TYPE_DECIMAL:
        case MYSQLI_TYPE_NEWDECIMAL:
        case MYSQLI_TYPE_FLOAT:
        case MYSQLI_TYPE_DOUBLE:
            return "d";

        // Binary data
        case MYSQLI_TYPE_BLOB:
            return "b";

        // Everything else → treat as string
        default:
            return "s";
    }
}

$servername = "localhost";
$user = "root";
$password = "";
$db = new mysqli($servername, $user, $password, "test");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$table_name = $_POST["table_name"];
$num_cols = $_POST["num_cols"];

$col_query = "SELECT * FROM " . $table_name . " LIMIT 1";
$result = $db->query($col_query);
$table_info = $result->fetch_fields();

$prepared_query = "INSERT INTO $table_name (";
$prepared_query_values = "(";
$bindString = "";
$pk_name = "";
$values = [];

$i = 0;
foreach ($table_info as $val) {
    $col_name = $val->name;

    $prepared_query .= "$col_name,";
    $prepared_query_values .= "?,";

    $bindString .= mysqli_type_to_bind($val->type);

    $values[] = &$_POST["column$i"];
    $i++;
}

$prepared_query = substr($prepared_query, 0, -1) . ") VALUES ";
$prepared_query .= substr($prepared_query_values, 0, -1) . ")";

$stmt = $db->prepare($prepared_query);
$stmt->bind_param($bindString, ...$values);

try
{
    $stmt->execute();
    echo "<a>Record successfully inserted</a><br>";
} catch (Exception $e)
{
    echo "<a>Query failed: " . $e->getMessage() . "</a><br>";
}

echo "<a href=\"http://localhost/hw/CPS541hw6/index.html\">Back to main page</a><br>";
?>