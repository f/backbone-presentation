<?php
    mysql_connect("localhost", "root", "");
    mysql_select_db('misafir_defteri');

    if (count($_POST) > 0) {
        mysql_query(sprintf('insert into entry (name, message, create_date) values("%s","%s",NOW())',
            mysql_real_escape_string($_POST['name']),
            mysql_real_escape_string($_POST['message'])
        ));
        //Tekrar form gönderimini engelliyoruz.
        header('Location: ./', true, 302);
    }

    $entries = mysql_query("select * from entry order by create_date desc");
    $entryArray = array();
    while (false !== ($row = mysql_fetch_assoc($entries))) $entryArray[] = $row;
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Misafir Defteri</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div>
    <h1>Misafir Defteri <?php echo time() ?></h1>
    <ul>
        <?php foreach ($entryArray as $entry): ?>
        <li><span><?php echo $entry['name'] ?>:</span> <?php echo $entry['message'] ?></li>
        <?php endforeach; ?>
    </ul>
    <a href="?add">Ekle</a>
    <?php if (isset($_GET['add'])): ?>
    <form method="post" action="">
        <table>
            <tr>
                <td>İsim:</td>
                <td><input name="name" type="text"></td>
            </tr>
            <tr>
                <td>Mesaj:</td>
                <td><textarea name="message"></textarea></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit">Gönder</button></td>
            </tr>
        </table>
    </form>
    <?php endif; ?>
</div>
</body>
</html>