<?php
mysql_connect("localhost", "root", "");
mysql_select_db('misafir_defteri');
if (NULL !== ($model = json_decode($_POST['model']))) {
    mysql_query(sprintf('insert into entry (name, message, create_date) values("%s","%s",NOW())',
        mysql_real_escape_string($model->name),
        mysql_real_escape_string($model->message)
    ));
    exit;
}

$entries = mysql_query("select * from entry order by create_date");
$entryArray = array();
while (false !== ($row = mysql_fetch_assoc($entries))) $entryArray[] = $row;

echo json_encode($entryArray);
?>