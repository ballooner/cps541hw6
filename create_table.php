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
$pk_val = $_POST["ispk"];

$query = "CREATE TABLE $table_name (";

for ($i = 0; $i < $num_cols; $i++)
{
    $col_type = $_POST["col" . $i . "type"];
    $col_name = $_POST["col" . $i . "name"];

    $query .= "$col_name $col_type";
    if ($pk_val == $i)
    {
        $query .= " PRIMARY KEY";
    }
    $query .= ",";
}

$query = substr($query, 0, -1); // Cut the last comma off
$query .= ")";

try
{
    $db->query($query);
    echo "<a>Table successfully created!</a><br>";
} catch (Exception $e)
{
    echo "<a>Query failed: " . $e->getMessage() . "</a><br>";
}

echo "<a href=\"http://localhost/hw/CPS541hw6/index.html\">Back to main page</a><br>";

?>