<?php
try
{
    $user = "root";
    $pass = "";
    $db = new PDO('mysql:host=localhost;dbname=VoteBook', $user, $pass);

    $db->exec("CREATE TABLE vote (
      id int(11) NOT NULL auto_increment,
      name varchar(100) NOT NULL,
      counter int(11) NOT NULL default '0',
      PRIMARY KEY  (id))  CHARACTER SET utf8");

    $db->exec("INSERT INTO vote VALUES (NULL ,'Олег Блохин', 0)");
    $db->exec("INSERT INTO vote VALUES (NULL ,'Юрий Калитвинцев', 0)");
    $db->exec("INSERT INTO vote VALUES (NULL ,'Мирча Луческу', 0)");
    $db->exec("INSERT INTO vote VALUES (NULL ,'Мирон Маркевич', 0)");
    $db->exec("INSERT INTO vote VALUES (NULL ,'Алексей Михайличенко', 0)");
    $db->exec("INSERT INTO vote VALUES (NULL ,'Павел Яковенко', 0)");
    $db->exec("INSERT INTO vote VALUES (NULL ,'Другой отчественный тренер', 0)");
    $db->exec("INSERT INTO vote VALUES (NULL ,'Иностранный тренер', 0)");



    echo "Всё готово!";
}
catch(PDOException $e)
{
    die("Error: ".$e->getMessage());
}
?>