<?php
require_once "stimulsoft/helper.php";
// Please configure the security level as you required.
// By default is to allow any requests from any domains.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token");

$handler = new StiHandler();
$handler->registerErrorHandlers();

$handler->onEmailReport = function ($event) {
	$event->settings->from = "no-reply@visionworkorders.com";
	$event->settings->host = "mail.visionworkorders.com";
	$event->settings->login = "no-reply@visionworkorders.com";
	$event->settings->password = "Wk0256@PL()608";
	
	// These parameters will be used when sending the report by email. You must set the correct values.
	// These parameters are optional.
	$event->settings->name = "Vision Work Order Report";
	//$event->settings->port = "587";
	//$event->settings->cc[] = "copy1@gmail.com";
	//$event->settings->bcc[] = "copy2@gmail.com";
	//$event->settings->bcc[] = "copy3@gmail.com John Smith";
	
	return StiResult::success("Email sent successfully.");	
};

$handler->onBeginProcessData = function ($args) {
	if ($args->connection == "Con_workorders")
	
		switch ($_SERVER['SERVER_NAME']){
			
			case "qaworkorder.com" ;
				$args->connectionString = "server=localhost;database=ve_crm_new;user=ve_report;password=Vision@4424!;";
				break;
				
			case "www.qaworkorder.com" ;
				$args->connectionString = "server=localhost;database=ve_crm_new;user=ve_report;password=Vision@4424!;";
				break;				
				
			case "qanew.visionworkorders.com";
				$args->connectionString = "server=localhost;database=voctech_qanew;user=voctech_report;password=Vision@4424!;";
				break;
			
			case "visionworkorders.com";
				$args->connectionString = "server=localhost;database=voctech_vecrm;user=voctech;password=Vision@4424;";
				break;
				
			case "qa.visionworkorders.com";
				$args->connectionString = "server=localhost;database=voctech_pm_test;user=voctech_report;password=Vision@4424!;";
				break;			
			case "vwos.com";
				//$args->connectionString = "server=localhost;database=vwos_ve_cm_new;user=vwos_ve_crm;password=Vision@4424!;";
				$args->connectionString = "server=vwos.com;database=vwos_ve_crm_new;user=vwos_report;password=Vision@4424!;";
				break;	
		}
		

	return StiResult::success();
};

$handler->process();
