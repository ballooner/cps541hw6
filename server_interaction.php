<?php

$servername = "localhost";
$user = "root";
$password = "";
$db = new mysqli($servername, $user, $password, "test");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$form_type = $_POST["form_type"];

if ($form_type == "create")
{
    $table_name = $_POST["table_name"];
    $num_cols = $_POST["num_columns"];

        echo "<h1>Enter column information for: " . $table_name . "</h1>";

        echo "<form action='create_table.php' method='post'>";

        echo "<table>";
        echo "<tr> 
                <th>Column</th>
                <th>Type</th>
                <th>Primary key?</th>
               </tr>";
        
        for ($i = 0; $i < $num_cols; $i++)
        {
            echo "<tr>
                    <th><input type='text' name=col" . $i . "name required></th>
                    <th><input type='text' name=col" . $i . "type required></th>
                    <th><input type='radio' name=ispk value='$i' required></th>
                  </tr>";
        }

        echo "</table>";

        echo "<button type='submit'>Create Table</button>";

        echo "<input type='hidden' name='table_name' value='$table_name'>";
        echo "<input type='hidden' name='num_cols' value='$i'>";

        echo "</form>";
} else if ($form_type == "drop")
{
    $table_name = $_POST["table_name"];

    $query = "DROP TABLE $table_name";

    try
    {
        $db->query($query);
        echo "<a>Table successfully dropped!</a><br>";
    } catch (Exception $e)
    {
        echo "<a>Query failed: " . $e->getMessage() . "</a><br>";
    }

    echo "<a href=\"http://localhost/hw/CPS541hw6/index.html\">Back to main page</a><br>";
} else if ($form_type == "add")
{
    $table_name = $_POST["table_name"];

    $query = "SELECT * FROM " . $table_name . " LIMIT 1";
    $result = false;

    try
    {
        $result = $db->query($query);
    } catch (Exception $e)
    {
        echo "<a>Query failed: " . $e->getMessage() . "</a><br>";
    }
    if ($result)
    {
        echo "<h1>Enter values for new record in: " . $table_name . "</h1>";

        echo "<form action='insert_record.php' method='post'>";
        $table_info = $result->fetch_fields();

        echo "<table>";
        echo "<tr> 
                <th>Column</th>
                <th>Value</th>
               </tr>";
        $i = 0;
        foreach ($table_info as $val)
        {
            echo "<tr>";
            echo "<td>" . $val->name . "</td>";
            echo "<td><input type='text' name='column$i' required></td>"; 
            echo "</tr>";
            $i++;
        }

        echo "</table>";

        echo "<button type='submit'>Update Table</button>";

        echo "<input type='hidden' name='table_name' value='$table_name'>";
        echo "<input type='hidden' name='num_cols' value='$i'>";

        echo "</form>";
    } else
    {
        echo "<h1> Query failed </h1>";
        echo "<a href=\"http://localhost/hw/CPS541hw6/index.html\">Back to main page</a>";
    }
} else if ($form_type == "delete")
{
    $table_name = $_POST["table_name"];
    $pk_val = $_POST["key"];

    $query = "SELECT * FROM " . $table_name . " LIMIT 1";

    try
    {
        $result = $db->query($query);
    } catch (Exception $e)
    {
        echo "<a>Query failed: " . $e->getMessage() . "</a><br>";
    }

    if ($result)
    {
        $table_info = $result->fetch_fields();
        $pk_name = "";
        $pk_type = "";

        foreach ($table_info as $val)
        {
            if ($val->flags & MYSQLI_PRI_KEY_FLAG)
            {
                $pk_name = $val->name;
                $pk_type = mysqli_type_to_bind($val->type);
                break;
            }
        }

        $stmt = $db->prepare("DELETE FROM $table_name WHERE $pk_name=?");
        $stmt->bind_param("$pk_type", $pk_val);
        
        try
        {
            $stmt->execute();

            echo "<h1> Record succesfully deleted </h1>";
        } catch (Exception $e)
        {
            echo "<h1> Query failed </h1>";
        }
        echo "<a href=\"http://localhost/hw/CPS541hw6/index.html\">Back to main page</a>";

    }
} else if ($form_type == "update")
{
    $table_name = $_POST["table_name"];
    $pk_val = $_POST["key"];

    echo "<h1>Enter new values for table: " . $table_name . "</h1>";

    $query = "SELECT * FROM " . $table_name . " LIMIT 1";
    $result = false;

    try
    {
        $result = $db->query($query);
    } catch (Exception $e)
    {
        echo "<a>Query failed: " . $e->getMessage() . "</a><br>";
    }
    if ($result)
    {
        echo "<form action='update_table.php' method='post'>";
        $table_info = $result->fetch_fields();

        echo "<table>";
        echo "<tr> 
                <th>Column</th>
                <th>Value</th>
               </tr>";
        $i = 0;
        foreach ($table_info as $val)
        {
            echo "<tr>";
            echo "<td>" . $val->name . "</td>";
            echo "<td><input type='text' name='column$i' required></td>"; 
            echo "</tr>";
            $i++;
        }

        echo "</table>";

        echo "<button type='submit'>Update Table</button>";

        echo "<input type='hidden' name='table_name' value='$table_name'>";
        echo "<input type='hidden' name='num_cols' value='$i'>";
        echo "<input type='hidden' name='pk_val' value='$pk_val'>";

        echo "</form>";
    } else
    {
        echo "<h1> Query failed </h1>";
        echo "<a href=\"http://localhost/hw/CPS541hw6/index.html\">Back to main page</a>";
    }
} else if ($form_type == "display")
{
    $table_name = $_POST["table_name"];
    $sql = "SELECT * FROM " . $table_name;
    $result = false;

    try
    {
        $result = $db->query($sql);
    } catch (Exception $e)
    {
        echo "<a>Query failed: " . $e->getMessage() . "</a><br>";
    }

    if ($result != false && mysqli_num_rows($result) > 0) {
        echo "<table border='1' cellpadding='5'><tr>";
        while ($field = mysqli_fetch_field($result)) 
        {
            echo "<th>{$field->name}</th>";
        }
        echo "</tr>";

        while ($row = mysqli_fetch_assoc($result)) 
        {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
        }
        echo "</tr>";
    }

        echo "</table>";
    }

    echo "<a href=\"http://localhost/hw/CPS541hw6/index.html\">Back to main page</a>";
}

$db->close();

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
?>