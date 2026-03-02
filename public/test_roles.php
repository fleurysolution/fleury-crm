<?php
$db = new mysqli('localhost', 'root', '', 'crm_core_dev');
$res = $db->query("SELECT * FROM fs_roles");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
echo "Done.\n";
