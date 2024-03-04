<?php
require_once "stimulsoft/helper.php";
?>
<!DOCTYPE html>

<html>
<head>
	<title>Report.mrt - Viewer</title>
	<link rel="stylesheet" type="text/css" href="css/stimulsoft.viewer.office2013.whiteblue.css">
	<script type="text/javascript" src="scripts/stimulsoft.reports.js"></script>
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
				"6vJhGtLLLz2GNviWmUTrhSqnOItdDwjBylQzQcAOiHl6ly24QKcZubXidpn43nzVw0dq08q4x0no1DHA" +
				"VyxiatNMCxO3SRcVaCvEQvVlTs/h6qsdtyfKLn81ZmSD/kKPG+5SYI/whrDOO0mqsmCsCzb13oUwXuHY" +
				"T7T3As6jLH4RMD2BKc+IJCdMsRwTYg/CHclG3C2caS4Kr4z34gKhpA26rFI+GQAOzlmda02zXm+QHL6x" +
				"g22yo+yL5fLY+pYoheOJWQmzpwXG9MHVn1tOoeoD8dKo+9vwBWYy/t7XXvN84QWPcxSORRHe67NIGcS+" +
				"B9f1fcRq8DiDB1ExtGetxw12g8qTwbtIHja4R785yQmxbXRltPDSCDsf2AL1E7ZMgVM4jQ/ee33GVeV7" +
				"DMpv5cKb8IGR1WCPyRIKxPuVgt0OS2QL7QOVU1u3cMwRWMzxEueL2cMhRbWcJsQrW9tkFDx/fy/Uryby" +
				"7vCNhUC3i1qagdpIf3lyXqlSubXW6ywy5kvC9spNZ/H2B9Uatawjq2g3mCmtYbTGkHXptFIff5Nab7rP" +
				"BJT/NoTvcDJFOziuGdFD006y+xpJpe91EJs7Dw==";

			Stimulsoft.Base.Localization.StiLocalization.setLocalizationFile("localization/en.xml", true);

			var report = new Stimulsoft.Report.StiReport();
			report.loadFile("reports/Report.mrt");

			var options = new Stimulsoft.Viewer.StiViewerOptions();
			options.appearance.fullScreenMode = true;
			options.toolbar.displayMode = Stimulsoft.Viewer.StiToolbarDisplayMode.Separated;
			
			var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);

			viewer.onBeginProcessData = function (args, callback) {
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