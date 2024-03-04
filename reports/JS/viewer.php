<?php
require_once 'stimulsoft/helper.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Vision Reports.PHP - JS Report Viewer</title>

	<!-- Report Office2013 style -->
	<link href="css/stimulsoft.viewer.office2013.css" rel="stylesheet">

	<!-- Stimusloft Reports.JS -->
	<script src="scripts/stimulsoft.reports.js" type="text/javascript"></script>
	<script src="scripts/stimulsoft.viewer.js" type="text/javascript"></script>
	
	<?php StiHelper::initialize(); ?>
	<script type="text/javascript">
		var options = new Stimulsoft.Viewer.StiViewerOptions();
		options.appearance.fullScreenMode = true;
		options.toolbar.showSendEmailButton = true; 
	
		var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);
		viewer.onBeginProcessData = function (event, callback) {
			<?php StiHelper::createHandler(); ?>
		}
		viewer.onEndExportReport = function (event) {
			event.preventDefault = true; // Prevent client default event handler (save the exported report as a file)
			<?php StiHelper::createHandler(); ?>
		}
		viewer.onEmailReport = function (event) {
			<?php StiHelper::createHandler(); ?>
		} 

		var report = new Stimulsoft.Report.StiReport();
		/*report.loadFile("reports/SimpleList.mrt"); */
		report.loadFile("reports/SimpleList.mrt");
		viewer.report = report;
		viewer.renderHtml("viewerContent");
	</script>
	</head>
<body>
	<div id="viewerContent"></div>
</body>
</html>
