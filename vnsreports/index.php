<?php
require_once "stimulsoft/helper.php";
?>
<!DOCTYPE html>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>---Vision Work Orders - Viewer----</title>
	<link rel="stylesheet" type="text/css" href="css/stimulsoft.viewer.office2013.whiteteal.css">
	<script type="text/javascript" src="scripts/stimulsoft.reports.js"></script>
	<script type="text/javascript" src="scripts/stimulsoft.reports.maps.js"></script>
	<script type="text/javascript" src="scripts/stimulsoft.viewer.js"></script>

	<?php
		$options = StiHelper::createOptions();
		$options->handler = "handler.php";
		$options->timeout = 30;
		StiHelper::initialize($options);
	?>
	<script type="text/javascript">
					
		function Start() {
			Stimulsoft.Base.StiLicense.key =
				"6vJhGtLLLz2GNviWmUTrhSqnOItdDwjBylQzQcAOiHkqHr+Yjf/eNEo6zXJctyOLoM3wjMEMJIS/j2g4" +
				"GJPm+JiBItg+H5EvZvu2Z/H8g0Zw5biPamC8MY6gTwSEPbKdMPQjVZ8+3B7V94CPfQxI5hC8UfOlZOSd" +
				"cQj3HdgoSMG3WkAuxwBHt6ya/9GeDCYGr245nokrR83qsM5dhfhDoZsYzHAImsC7IRLtOiap/56PXyIP" +
				"MyMqaSmod0Ru03e2DW6J1CYH26d2jNt1UUxLpZm2T28MoGcE2dUhRojWAcUuYwmrU9lpUAMoSkjbgDFy" +
				"/73tpjykdr8SfE89uU+U7S4VSxYomZJfC0oA8cbgec8VZJgpYrONwlKDq+nmYDVHWr/bmWxVWXPC2z1e" +
				"nSO1qvXTqDp/AQVqaXNva7t37XTHo06/QsucHRCujYXnRMTNbGINL4EQ2HS+2K2DNgyIMdMCAtCjUug1" +
				"PGTzZZCLqUIvDzGqdzhMHzVaY3wnIFO71MkhUuC11ZodFh372LpBs1EbLmFVhyiRJwhAK5gporeffsuv" +
				"i1VO2cg3mSIQKYMb6X6GD35P/PcFRgmntYT8KA==";

			Stimulsoft.Base.Localization.StiLocalization.setLocalizationFile("localization/en.xml", true);

			//var report = new Stimulsoft.Report.StiReport();
			var report = Stimulsoft.Report.StiReport.createNewReport();
			report.loadFile("../reports/reports/<?php echo $_GET['report_key']; ?>");
			<?php if (isset($_GET['buildkey'])){ ?> report.dictionary.variables.getByName("buildkey").valueObject = <?php echo '"' . $_GET['buildkey'].'";';}?>
			<?php if (isset($_GET['Cost_Center_Number'])){ ?> report.dictionary.variables.getByName("Cost_Center_Number").valueObject = <?php echo '"' . $_GET['Cost_Center_Number'].'";';}?>
			<?php if (isset($_GET['User'])){ ?> report.dictionary.variables.getByName("User").valueObject = <?php echo '"' . $_GET['User'].'";';}?>
			<?php if (isset($_GET['AU_Designation_ID'])){ ?> report.dictionary.variables.getByName("AU_Designation_ID").valueObject = <?php echo '"' . $_GET['AU_Designation_ID'] .'";';}?>
			<?php if (isset($_GET['AU_Template_Name_ID'])){ ?> report.dictionary.variables.getByName("AU_Template_Name_ID").valueObject = <?php echo  '"' . $_GET['AU_Template_Name_ID'].'";';}?>
			<?php if (isset($_GET['WO_Number'])){ ?> report.dictionary.variables.getByName("WO_Number").valueObject = <?php echo '"' . $_GET['WO_Number'].'";';}?>
			<?php if (isset($_GET['Status'])){ ?> report.dictionary.variables.getByName("Status").valueObject =  <?php echo '"' . $_GET['Status'].'";';}?>
			<?php if (isset($_GET['Batch_Number'])){ ?> report.dictionary.variables.getByName("Batch_Number").valueObject =  <?php echo '"' . $_GET['Batch_Number'].'";';}?>
			<?php if (isset($_GET['AU_Equipment_Name_ID'])){ ?> report.dictionary.variables.getByName("AU_Equipment_Name_ID").valueObject =  <?php echo '"' . $_GET['AU_Equipment_Name_ID'].'";';}?>
			<?php if (isset($_GET['AU_Equipment_Detail_ID'])){ ?> report.dictionary.variables.getByName("AU_Equipment_Detail_ID").valueObject =  <?php echo '"' . $_GET['AU_Equipment_Detail_ID'].'";';}?>
			<?php if (isset($_GET['Invoice_Number'])){ ?> report.dictionary.variables.getByName("Invoice_Number").valueObject =  <?php echo '"' . $_GET['Invoice_Number'].'";';}?>
			<?php if (isset($_GET['tenantId'])){ ?> report.dictionary.variables.getByName("tenantId").valueObject =  <?php echo '"' . $_GET['tenantId'].'";';}?>
			<?php if (isset($_GET['coiAuTenantId'])){ ?> report.dictionary.variables.getByName("coiAuTenantId").valueObject =  <?php echo '"' . $_GET['coiAuTenantId'].'";';}?>
			<?php if (isset($_GET['coiAuDetailsId'])){ ?> report.dictionary.variables.getByName("coiAuDetailsId").valueObject =  <?php echo '"' . $_GET['coiAuDetailsId'].'";';}?>
			<?php if (isset($_GET['coiAuRequirId'])){ ?> report.dictionary.variables.getByName("coiAuRequirId").valueObject =  <?php echo '"' . $_GET['coiAuRequirId'].'";';}?>
			<?php if (isset($_GET['PM_WO_Number'])){ ?> report.dictionary.variables.getByName("PM_WO_Number").valueObject =  <?php echo '"' . $_GET['PM_WO_Number'].'";';}?>		
		

				

			var options = new Stimulsoft.Viewer.StiViewerOptions();
			options.appearance.fullScreenMode = true;
			options.toolbar.displayMode = Stimulsoft.Viewer.StiToolbarDisplayMode.Separated;
			options.toolbar.showSendEmailButton = true;

			
			var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);

			viewer.onBeginProcessData = function (args, callback) {
				<?php StiHelper::createHandler(); ?>
			}

			viewer.onEmailReport = function (event) {
				<?php StiHelper::createHandler(); ?>
			}

			viewer.report = report;
			viewer.renderHtml("viewerContent");
		}
		
	</script>
</head>
<body onload="Start()">
	<div id="viewerContent"></div>
</body>
</html>