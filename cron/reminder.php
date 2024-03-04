<?php
//echo getcwd();
require_once '/home/ve/public_html/cron/init.php';

/************ Test Mail  ************/
@mail('ve.ppm2@gmail.com','Mail test' ,'This is mail test script after open the comment.');
$wstModel = new Model_WoScheduleStatus();
$wstModel->getWorkOrderReminder();
