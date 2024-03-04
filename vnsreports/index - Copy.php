<?php
require_once "stimulsoft/helper.php";

//	echo "Building ID " . $_GET[buildkey] . "<br>";
//	echo "Cost Center " . $_GET[Cost_Center_Number] . "<br>";
//	echo "User " . $_GET[User] . "<br>";
//	echo "Report " . $_GET[report_key];
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
	
		function getQueryVariable(variable)
			{
			   var query = window.location.search.substring(1);
			   var vars = query.split("&");
			   for (var i=0;i<vars.length;i++) {
					   var pair = vars[i].split("=");
					   if(pair[0] == variable){return pair[1];}
			   }
			   return(false);
			}
			
		function Start() {
			Stimulsoft.Base.StiLicense.key =
				 "6vJhGtLLLz2GNviWmUTrhSqnOItdDwjBylQzQcAOiHn7jAKNHrgvhDP8dRcj4f0ehBc7WZERcEcddqOq6W0K8zWkuP" + 
"LhHvoNzGF5DK/k3hfa6xvIZiV3fmzNFgsTwXbmKxKdEDlcQgpy9SIzcbVrqRtQlWf6LFXlM72vMLw/fs7rCqYy1yj9" + 
"/ZNDjlK+QYOGkMncdL/P0hCdKa3shVTDK3sV8GczPhIh5uhJcxGjoqkXIcJOe4LzdihWBzd9Mp57nL1Yo/SYV+BRfp" + 
"fWhh3dF6+P6alVfujC7XIm/d8V+NSz6RBS3yNCAz6BPXBZjtUK1ECenpT3qGYPqBy9mpff780NnwpXKDAtRsmsLPXv" + 
"RPzJwFittJ7dNqxoK2TRmb0kcRUoM8+8S2VTx3r97FJZnNfF8TrNyWV4dnq7tZCwuxpCovaf2iStwBCpcX1GtbfvRc" + 
"XVDYOmSHZyQfQ0854J4+o9D+7SQnW1A4ADVqML3qronaRAKfswCjsqXbquRLvu1oMkXMnnFyLPjFGpU3cQO0BPLaRH" + 
"Lyaqn/3HhdkeJhutEw/ReTB/TFBJ5j4rgv8k0e9oT/eENxUj/pJ+EUaW7g==";

			Stimulsoft.Base.Localization.StiLocalization.setLocalizationFile("localization/en.xml", true);

			var report = new Stimulsoft.Report.StiReport();
			report.loadFile("../reports/reports/<?php echo $_GET[report_key]; ?>");
			report.dictionary.variables.getByName("buildkey").valueObject = "<?php echo $_GET[buildkey]; ?>";
			report.dictionary.variables.getByName("Cost_Center_Number").valueObject = "<?php echo $_GET[Cost_Center_Number]; ?>";
			report.dictionary.variables.getByName("User").valueObject = "<?php echo $_GET[User]; ?>";
			switch( getQueryVariable("report_key"))
				{
				case "PM_AU_Equipment_Link_Type_Designation_Ver1.mrt":
					report.dictionary.variables.getByName("AU_Designation_ID").valueObject = getQueryVariable("AU_Designation_ID");
					break;
					
				case "PM_AU_Equipment_Link_Template_Name_Ver1.mrt":
					report.dictionary.variables.getByName("AU_Template_Name_ID").valueObject = getQueryVariable("AU_Template_Name_ID");
					break;					
				default:
				}
			
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