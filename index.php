<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    input{
      margin-top: 10px;
      padding: 15px;
      border: thin solid black;

    }
    form{
    display: flex;
    flex-direction: column;
    }
  </style>
  <script>
	function editUser(id){
	   var line = document.getElementById('line-'+id);
	   const tdElements = line.getElementsByTagName('td');
	   document.getElementById('nameInput').value = tdElements[0].textContent;
	   document.getElementById('surnameInput').value = tdElements[1].textContent;
	   document.getElementById('patronymicInput').value = tdElements[2].textContent;
	   document.getElementById('birthdateInput').value = ageToDateFormatted(tdElements[3].textContent);
	   document.getElementById('cityInput').value = getOptionValueByTextContent(tdElements[4].textContent, 'cityInput');
	   document.getElementById('workInput').value = getOptionValueByTextContent(tdElements[5].textContent, 'workInput');
	   document.getElementById('userId').value = id;
	   document.getElementById('submitBtn').textContent = 'Edit';
	   document.getElementById('formMethod').value = 'PUT';
	}
	
	function getOptionValueByTextContent(desiredText, selectId) {
	    // Get the select element by its ID
	    const selectElement = document.getElementById(selectId);

	    let toReturn = '';
	    for (const option of selectElement.options) {
		if (option.textContent === desiredText) {
		    // If the text content matches, print its value
		    toReturn = option.value;
		    break;
		}
	    }
	    console.log(toReturn);
	    return toReturn;
	}

	function switchToAddUser(){
	    const myForm = document.getElementById('user-form');

	    // Loop through all form elements
	    for (const element of myForm.elements) {
		// Check if the element is an input, textarea, or select element
		if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
		    // Reset the value of the element to an empty string
		    element.value = '';
		}
		// For checkboxes and radio buttons, reset the "checked" property
		if (element.type === 'checkbox' || element.type === 'radio') {
		    element.checked = false;
		}
	    }
	    document.getElementById('formMethod').value = 'POST';
	    document.getElementById('submitBtn').textContent = 'Send';
	}
	
	function ageToDateFormatted(age) {
	    const today = new Date();
	    const birthDate = new Date(today.getFullYear() - age, today.getMonth(), today.getDate());

	    const year = birthDate.getFullYear();
	    const month = String(birthDate.getMonth() + 1).padStart(2, '0');
	    const day = String(birthDate.getDate()).padStart(2, '0');

	    const formattedDate = `${year}-${month}-${day}`;
	    return formattedDate;
	}

  </script>
</head>
<body>
<?php

// Подключение к БД

$db_host = 'localhost';
$db_user = 'wp_user';
$db_pass = '111111Aa!';
$db_name = 'wp_db';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die('Ошибка подключения: ' . mysqli_connect_error());
}

function post_data()
{
    global $conn;
    if (!empty($_POST['_method']) && $_POST['_method'] != 'POST'){
    	return;
    }
    if (!empty($_POST['surname']) && !empty($_POST['patronymic'] && !empty($_POST['birthdate']) && !empty($_POST['city']) && !empty($_POST['work']) && !empty($_POST['name']))) {
        $surname = $_POST['surname'];
	$patronymic = $_POST['patronymic'];
	$birthdate = $_POST['birthdate'];
	$cityId = $_POST['city'];
	$workId = $_POST['work'];
	$name = $_POST['name'];

        $query = "INSERT INTO wp_user (name, surname, patronymic, b_date, city_id, p_work_id)
                  VALUES ('$name', '$surname', '$patronymic', '$birthdate', $cityId, $workId)";

        $result = mysqli_query($conn, $query);
    }
}

function edit_data()
{
    global $conn;
    if (!empty($_POST['_method']) && $_POST['_method'] != 'PUT'){
    	return;
    }
    if (!empty($_POST['surname']) && !empty($_POST['patronymic']) && !empty($_POST['birthdate']) && !empty($_POST['city']) && !empty($_POST['work']) && !empty($_POST['name']) && 
    !empty($_POST['id'])) {
        $surname = $_POST['surname'];
	$patronymic = $_POST['patronymic'];
	$birthdate = $_POST['birthdate'];
	$cityId = $_POST['city'];
	$workId = $_POST['work'];
	$name = $_POST['name'];
	$userId = $_POST['id'];
	
        $query = "UPDATE wp_user SET name = '$name', surname = '$surname', patronymic = '$patronymic', b_date = '$birthdate', city_id = $cityId, p_work_id = $workId where id = $userId";

        $result = mysqli_query($conn, $query);
    }
}

function getUserById($userId){
	$query = "select wp_user.name as name, wp_user.surname, wp_user.patronymic, b_date as birthdate, wp_city.id as city, wp_work.id as work, wp_user.id as userId from wp_user 

	left join wp_city on wp_user.city_id = wp_city.id

	left join wp_work on wp_user.p_work_id = wp_work.id where wp_user.id = $userId";

  $result = mysqli_query($conn, $query);

  return $result;
}

post_data();
edit_data();

// Функция для вывода данных

function get_data($conn) {

  $query = "select wp_user.name as username, wp_user.surname, wp_user.patronymic, TIMESTAMPDIFF(YEAR, b_date, CURDATE()) AS age, wp_city.name as city, wp_work.name as work, wp_user.id as userId from wp_user 

	left join wp_city on wp_user.city_id = wp_city.id

	left join wp_work on wp_user.p_work_id = wp_work.id;"; // Replace 'wp_user' with the actual table name

  $result = mysqli_query($conn, $query);

  return $result;

}

function display_data()
{
    global $conn; // Объявляем, что используем глобальную переменную $conn

    $res = get_data($conn);

    echo '<table>';
    while ($row = mysqli_fetch_assoc($res)) {
        echo '<tr id="line-'. $row["userId"] .'">';
	echo '<td>' . $row["username"] . '</td>';
	echo '<td>' . $row["surname"] . '</td>';
	echo '<td>' . $row["patronymic"] . '</td>';
	echo '<td>' . $row["age"] . '</td>';
	echo '<td>' . $row["city"] . '</td>';
	echo '<td>' . $row["work"] . '</td>';
	echo '<td><form id="delete'. $row["userId"] .'" method="post"><input id="'. $row["userId"] .'" type="hidden" name="userId" placeholder="userId" value="'. $row["userId"] .'"><button type="submit" name="userDelete">Delete</button></form></td>';
	echo '<td><input id="'. $row["userId"] .'" type="hidden" name="userId" placeholder="userId" value="'. $row["userId"] .'">
      		<button name="userEdit" id="'. $row["userId"] .'" onclick="editUser('.$row["userId"].')">Edit</button></td>';
        echo '</tr>';
    }
    echo '</table>';
}

function delete_data(){
    global $conn;

    if (!empty($_POST['userId'])) {
        $userIdToDelete = $_POST['userId'];
	removeUserById($conn, $userIdToDelete);
    }
}

function removeUserById($conn, $userIdToDelete) {

  $query = "delete from wp_user where id = $userIdToDelete"; // Replace 'wp_user' with the actual table name

  $result = mysqli_query($conn, $query);
}

// Вызов функции display_data для вывода данных
delete_data();
display_data();
?>
    <button onclick="switchToAddUser()">Add new user</button>
    <form id="user-form" method="post">
      <input id="formMethod" type="hidden" name="_method" value="POST" required>
      <input id="userId" type="hidden" name="id" value="">
      <input id="nameInput" type="text" name="name" placeholder="Name" required>
      <input id="surnameInput" type="text" name="surname" placeholder="Surname" required>
      <input id="patronymicInput" type="text" name="patronymic" placeholder="Patronymic" required>
      <input id="birthdateInput" type="text" name="birthdate" pattern="\d{4}-\d{2}-\d{2}" placeholder="yyyy-MM-dd" title="Please enter a date in the format 'yyyy-MM-dd'" required>
<?php
	include 'wp-content/themes/my_custom_theme-1/send.php';
	// dropdown for cities
	echo "<select name='city' id='cityInput' required>";
	
	$cities = getCities();
	foreach ($cities as $city) {
	  echo "<option value='" . $city['id'] . "'>" . $city['name'] . "</option>";
	}
	echo "</select><br>";

	// dropdown for works
	echo "<select name='work' id='workInput' required>";
	
	$works = getWorks();
	foreach ($works as $work) {
	  echo "<option value='" . $work['id'] . "'>" . $work['name'] . "</option>";
	}
	echo "</select><br>";
?>
      <button id="submitBtn" type="submit" name="submit">Send</button>
    </form>
</body>
</html>

