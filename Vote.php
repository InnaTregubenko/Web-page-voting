<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <title>jQuery</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <style type="text/css">
        p {
        / / margin: 0 px;
            display: <?php echo isset( $_GET["once"])?'block':'none'?>;
        }

        #par1 {
            opacity: <?php echo isset( $_GET["once"])?'1':'0'?>;
            display: <?php echo isset( $_GET["once"])?'block':'none'?>;
            position: relative;
            width: 100%;
            height: 100%;
        }

    </style>

</head>
<body>
<div class="jumbotron text-center">
    <h1>Интернет - голосование</h1>
    <h4>Кто должен возглавить сборную Украины?</h4>
</div>
<div class="container mt-3">
    <form method="post" action="vote.php">
        <?php
        try {
            $user = "root";
            $pass = "";
            $db = new PDO('mysql:host=localhost;dbname=VoteBook', $user, $pass);

            $nametable = "vote";


            $query = "SELECT * FROM " . $nametable;
            $stmt = $db->query($query);
            $number_fields = $stmt->columnCount(); //кол колонок
            $counter = 0;

            while ($row = $stmt->fetch()) {
                ++$counter;
                echo("<div class='custom-control custom-radio custom-control-block'>");
                echo("<input type='radio' class='custom-control-input' value='" . $row['id'] . "' id='" . $row['id'] . "' name='radio'>");
                echo("<label class='custom-control-label' for='" . $row['id'] . "'>" . $row['name'] . "</label>");
                echo("</div>");

            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }

        ?>
        <button type="submit" name="Vote" class="btn btn-primary">Голосовать</button>
    </form>
</div>
<br/>
<p class="bg-danger text-white">Вы не можете голосовать</p><br><br>
<div class="jumbotron text-center">
    <h1>Результаты голосования</h1>
</div>
<div class="container">
    <table class="table table-dark table-hover">
        <thead>
        <tr>
            <th>Кандидат</th>
            <th>% голосов</th>
        </tr>
        </thead>
        <tbody>


        <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['Vote'])) {
                if (isset($_REQUEST['radio'])) {
                    $id = $_REQUEST['radio'];
                    try {
                        $flag = false;
                        $user = "root";
                        $pass = "";
                        $db = new PDO('mysql:host=localhost;dbname=VoteBook', $user, $pass);
                        $nametable = "users";
                        $query = "SELECT * FROM " . $nametable;
                        $stmt = $db->query($query);
                        while ($row = $stmt->fetch()) {
                            if ($row['username'] != $_SERVER['REMOTE_ADDR']) {
                                $dnow = date('Y-m-d h:i:s');
                                //$newdate = date('Y-m-d h:i:s', strtotime($row['votetime'] . " + 60 minutes"));
                                $newdate = date('Y-m-d h:i:s', strtotime($row['votetime'] ));
                                // создаёт 2 объекта
                                $dateStart = new DateTime($dnow);
                                $dateEnd = new DateTime($newdate);
                                // тут происходит вычитание двух дат
                                $i = date_diff($dateStart, $dateEnd);
                                $interval = (int)$i->format('%i'); //минуты

                                if ($interval >=60) {
                                    $db->exec("INSERT INTO users VALUES (NULL ,'" . $_SERVER['REMOTE_ADDR'] . "',' " . date('Y-m-d h:i:s') . "')") or die(print_r($db->errorInfo(), true));
                                    $flag = true;
                                }
                            }
                        }

                        if ($flag) {
                            $nametable = "vote";

                            $query = "SELECT * FROM " . $nametable;
                            $stmt = $db->query($query);

                            $counter = 0;
                            while ($row = $stmt->fetch()) {
                                if ($row['id'] == $id) {
                                    $counter = $row['counter'];
                                    ++$counter;
                                    $db->query("UPDATE vote SET counter = '" . $counter . "' WHERE id = '" . $id . "'") or die(print_r($db->errorInfo(), true));
                                }
                            }
                        } else if (!$flag) {
                            header("location: Vote.php?once=1");
                        }
                    } catch (PDOException $e) {
                        die("Error: " . $e->getMessage());
                    }
                }
            }
            //header("location: Vote.php");
        }


        try {
            $user = "root";
            $pass = "";
            $db = new PDO('mysql:host=localhost;dbname=VoteBook', $user, $pass);
            $nametable = "vote";

            $totalvotecount = $db->query("select sum(counter) from vote") or die(print_r($db->errorInfo(), true));
            $row = $totalvotecount->fetch();
            $totalvotecount = $row[0];

            $query = "SELECT * FROM " . $nametable;
            $stmt = $db->query($query);
            //$number_fields = $stmt->columnCount(); //кол колонок
            while ($row = $stmt->fetch()) {
                echo "<tr>";
                echo("<td>" . $row['name'] . "</td>");
                $res = $row['counter'] / $totalvotecount * 100;
                echo("<td>" . round($res, 2) . " % </td>");
                echo "</tr>";
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
        ?>

        </tbody>
    </table>


</div>


</body>
</html>
