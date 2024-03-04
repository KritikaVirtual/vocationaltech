<?php
require_once "stimulsoft/helper.php";

// Please configure the security level as you required.
// By default is to allow any requests from any domains.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token");

$handler = new StiHandler();
$handler->registerErrorHandlers();

$handler->onBeginProcessData = function ($args) {
	if ($args->connection == "Con_workorders")
		$args->connectionString = "server=qaworkorder.com;database=ve_crm_new;user=ve_report;password=Vision@4424!;";

	$args->parameters["buildkey"] = "1,2,44";
	$args->parameters["Cost_Center_Number"] = "600,1442921703,1460335468";
	$args->parameters["User"] = "421";

	return StiResult::success();
};

$handler->process();