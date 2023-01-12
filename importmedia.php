<?php
session_start();
header("Content-Type:".$_SESSION['userlist'][0]['image_type']);
echo($_SESSION['userlist'][0]['image']);
?>