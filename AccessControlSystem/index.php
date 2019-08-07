<?php

//$dbconn = pg_connect ("localhost", 5432,  "dawsmol1");

$dbconn = pg_connect("dbname=dawsmol1 user=dawsmol1 host=localhost password=niebieski7") or die("Nie moge
poczyc sie z baza danych !");


if (isset($_POST['action_add_user_to_card'])){
	$user_id = $_POST['userc'];
	$card_id = $_POST['cardc'];

	$query = "UPDATE card SET is_active=TRUE, user_id = $user_id where id = $card_id";
	$resuelt = pg_query($dbconn, $query);
	if (!$resuelt) {
		die("nieprawidłowy wynik dodawania karty użytkownikowi");
	}
}

if (isset($_POST['delete_user'])){
	$user_id = $_POST['userc'];

	$query = "DELETE FROM users WHERE id = $user_id";
	$resuelt = pg_query($dbconn, $query);
	if (!$resuelt) {
		die("nieprawidłowy wynik usuwania uzytkownika z bazy");
	}
}

if (isset($_POST['imie'])) {
	$imie = $_POST['imie'];
	$telefon = $_POST['telefon'];
	$nazwisko = $_POST['nazwisko'];

	$query = "INSERT INTO users(name, surname, telephone) VALUES ('$imie', '$nazwisko', $telefon)";
	$resuelt = pg_query($dbconn, $query);
	if (!$resuelt) {
		die("nieprawidłowy wynik dodawania uzytkownika do bazy");
	}
}

if (isset($_POST['check_permission'])){
	$card_id = $_POST['cardc'];
	$door_id = $_POST['doorc'];

	$query = "SELECT description FROM door where id = $door_id";
	$resuelt = pg_query($dbconn, $query);
	$cdrow=pg_fetch_row($resuelt);
	$door_name = $cdrow[0];


	$query = "SELECT * FROM check_permissions($card_id, $door_id)";
	$resuelt = pg_query($dbconn, $query);
	if (!$resuelt) {
		die("nieprawidłowy wynik sprawdzania uprawnien");
	}
	$cdrow=pg_fetch_row($resuelt);
	echo "<h1>Wynik sprawdzania dostępu karty $card_id do $door_name: $cdrow[0]</h1>";
}


echo '<html><body>';

function print_table($dbc, $sql, $title){
	$result = pg_query($dbc, $sql);

	$i = 0;
	echo '<h1>'.$title.'</h1><table><tr>';

	while ($i < pg_num_fields($result))
	{
		$fieldName = pg_field_name($result, $i);
		echo '<td>' . $fieldName . '</td>';
		$i = $i + 1;
	}

	echo '</tr>';
	$i = 0;

	while ($row = pg_fetch_row($result)) 
	{
		echo '<tr>';
		$count = count($row);
		$y = 0;
		while ($y < $count)
		{
			$c_row = current($row);
			echo '<td>' . $c_row . '</td>';
			next($row);
			$y = $y + 1;
		}
		echo '</tr>';
		$i = $i + 1;
	}
	pg_free_result($result);

	echo '</table>';
}

$q1 = "select * from logs";

print_table($dbconn, $q1, "Logi");
print_table($dbconn, "select * from users", "Użytkownicy");
print_table($dbconn, "select c.id, c.is_active, u.name, u.surname, u.telephone from card c left join users u on u.id = c.user_id", "Karty");
print_table($dbconn, "select * from door", "Czytniki");
print_table($dbconn, "select d.description, u.name, u.surname from permissions p left join door d on d.id = p.door_id left join card c on c.id = p.card_id left join users u on u.id = c.user_id", "Uprawnienia");


echo "
<h1> Dodawanie usera </h1>
<!-- FORMULARZ HTML WPROWADZANIA DANYCH -->
<form name=\"add_user\" method=\"post\" action=\"index.php\">
  imie: <input name=\"imie\" type=\"text\" size=\"20\">
  nazwisko: <input name=\"nazwisko\" type=\"text\" size=\"20\">
  telefon: <input type=\"text\" name=\"telefon\" size=\"20\">
  <input value=\"dodaj usera\" type=\"submit\">
</form>
<!-- KONIEC FORMULARZA -->
";


echo "
<h1> Przypisywanie karty userowi </h1>
<!-- FORMULARZ HTML WPROWADZANIA DANYCH -->
<form name=\"add_user\" method=\"post\" action=\"index.php\">
<select id=\"cardc\" name=\"cardc\" value=\"Id karty\">
";
            
$cdquery="SELECT id FROM card WHERE user_id IS NULL and is_active = FALSE";
$cdresult=pg_query($dbconn, $cdquery) or die ("Query to get data from firsttable failed: ".mysql_error());
            
while ($cdrow=pg_fetch_array($cdresult)) {
    $id=$cdrow["id"];
        echo "
        <option value=\"$id\">
        $id
        </option>";
            
}
echo "</select>




<select id=\"userc\" name=\"userc\">
";
            
$cdquery="SELECT id, name, surname FROM users";
$cdresult=pg_query($dbconn, $cdquery) or die ("Query to get data from firsttable failed: ".mysql_error());
            
while ($cdrow=pg_fetch_array($cdresult)) {
    $id=$cdrow["id"];
    $name = $cdrow["name"];
    $sur = $cdrow["surname"];
        echo "
        <option value=\"$id\">
                    $name $sur
        </option>";
            
}
echo "</select>

  <input value=\"przypisz karte\" name=\"action_add_user_to_card\" type=\"submit\">
  </form>
<!-- KONIEC FORMULARZA -->
";




echo "
<h1> Usuń użytkownika </h1>
<!-- FORMULARZ HTML WPROWADZANIA DANYCH -->
<form name=\"del_user\" method=\"post\" action=\"index.php\">
<select id=\"userc\" name=\"userc\" value=\"Id karty\">
";
            
$cdquery="SELECT id, name, surname FROM users";
$cdresult=pg_query($dbconn, $cdquery) or die ("Query to get data from firsttable failed: ".mysql_error());
            
while ($cdrow=pg_fetch_array($cdresult)) {
    $id=$cdrow["id"];
    $name = $cdrow["name"];
    $sur = $cdrow["surname"];
        echo "
        <option value=\"$id\">
                    $name $sur
        </option>";
            
            
}
echo "</select>

<input value=\"Usuń użytkownika\" name=\"delete_user\" type=\"submit\">
  </form>
<!-- KONIEC FORMULARZA -->
";
    



echo "
<h1> Sprawdzanie uprawnień </h1>
<!-- FORMULARZ HTML WPROWADZANIA DANYCH -->
<form name=\"check\" method=\"post\" action=\"index.php\">
<select id=\"cardc\" name=\"cardc\" value=\"Id karty\">
";
            
$cdquery="SELECT id FROM card";
$cdresult=pg_query($dbconn, $cdquery) or die ("Query to get data from firsttable failed: ".mysql_error());
            
while ($cdrow=pg_fetch_array($cdresult)) {
    $id=$cdrow["id"];
        echo "
        <option value=\"$id\">
        $id
        </option>";
            
}
echo "</select>




<select id=\"doorc\" name=\"doorc\">
";
            
$cdquery="SELECT id, description FROM door";
$cdresult=pg_query($dbconn, $cdquery) or die ("Query to get data from firsttable failed: ".mysql_error());
            
while ($cdrow=pg_fetch_array($cdresult)) {
    $id=$cdrow["id"];
    $name = $cdrow["description"];
        echo "
        <option value=\"$id\">
                    $name
        </option>";
            
}
echo "</select>

  <input value=\"Sprawdź!\" name=\"check_permission\" type=\"submit\">
  </form>
<!-- KONIEC FORMULARZA -->
";




echo '</body></html>';
?>