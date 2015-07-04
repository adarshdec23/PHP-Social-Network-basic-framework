<?php
/* Project: myAm
  Purpose: Contains MySQL connection data, and initialises $link variable;
 * Replace "localhost" with your host name
 * Replace "root" with database username
 * Replace the third parameter with the password of the above mentioned user.
 * Replace "myam" with your database name
 */
$link= new mysqli("localhost", "root","","myam") or die();
?>