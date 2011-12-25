<?php
mysql_connect("localhost", "root", "");
mysql_select_db('misafir_defteri');

if (count($_POST) > 0) {
    mysql_query(sprintf('insert into entry (name, message, create_date) values("%s","%s",NOW())',
        mysql_real_escape_string($_POST['name']),
        mysql_real_escape_string($_POST['message'])
    ));
    exit("1");
}

$entries = mysql_query("select * from entry order by create_date desc");
$entryArray = array();
while (false !== ($row = mysql_fetch_assoc($entries))) $entryArray[] = $row;

echo json_encode($entryArray);
?>