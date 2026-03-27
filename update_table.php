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
$pk_val = $_POST["pk_val"];

$col_query = "SELECT * FROM " . $table_name . " LIMIT 1";
$result = $db->query($col_query);
$table_info = $result->fetch_fields();

$prepared_query = "UPDATE $table_name SET ";
$bindString = "";
$pk_name = "";
$values = [];

$i = 0;
foreach ($table_info as $val) {
    $col_name = $val->name;

    $prepared_query .= "$col_name=?, ";
    $bindString .= mysqli_type_to_bind($val->type);

    if ($val->flags & MYSQLI_PRI_KEY_FLAG)
        $pk_name = $val->name;  

    // Get value from form
    $values[] = &$_POST["column$i"];
    $i++;
}
$bindString .= mysqli_type_to_bind($pk_val);
$values[] = &$pk_val;

$prepared_query = substr($prepared_query, 0, -2) . " WHERE $pk_name = ?";

$stmt = $db->prepare($prepared_query);
$stmt->bind_param($bindString, ...$values);

try
{
    $stmt->execute();
} catch (Exception $e)
{
    echo "<a>Query failed: " . $e->getMessage() . "</a>";
}

echo "<a href=\"http://localhost/hw/CPS541hw6/index.html\">Back to main page</a><br>";
?>