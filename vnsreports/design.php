<?php
require_once "stimulsoft/helper.php";
?>
<!DOCTYPE html>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Vision Work Orders - Viewer</title>
	<link rel="stylesheet" type="text/css" href="css/stimulsoft.viewer.office2013.whiteteal.css">
	<link rel="stylesheet" type="text/css" href="css/stimulsoft.designer.office2013.whiteteal.css">
	<script type="text/javascript" src="scripts/stimulsoft.reports.js"></script>
	<script type="text/javascript" src="scripts/stimulsoft.reports.maps.js"></script>
	<script type="text/javascript" src="scripts/stimulsoft.viewer.js"></script>
	<script type="text/javascript" src="scripts/stimulsoft.designer.js"></script>
    
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
			report.loadFile("../reports/reports/<?php echo $_GET[report_key]; ?>");
			report.dictionary.variables.getByName("buildkey").valueObject = "<?php echo $_GET[buildkey]; ?>";
			report.dictionary.variables.getByName("Cost_Center_Number").valueObject = "<?php echo $_GET[Cost_Center_Number]; ?>";
			report.dictionary.variables.getByName("User").valueObject = "<?php echo $_GET[User]; ?>";
			switch("<?php echo $_GET[report_key]; ?>")
				{
				case "PM_AU_Equipment_Link_Type_Designation_Ver1.mrt":
					report.dictionary.variables.getByName("AU_Designation_ID").valueObject = getQueryVariable("AU_Designation_ID");
					break;
					
				case "PM_AU_Equipment_Link_Template_Name_Ver1.mrt":
					report.dictionary.variables.getByName("AU_Template_Name_ID").valueObject = getQueryVariable("AU_Template_Name_ID");
					break;	
					
				case "HTML_WO_Service_Request.mrt":
					report.dictionary.variables.getByName("WO_Number").valueObject = "<?php echo $_GET[WO_Number]; ?>";
					
					break;

				case "HTML_Open_WO_Detail_With_Notes_R1.mrt":
					report.dictionary.variables.getByName("Status").valueObject =  "<?php echo $_GET[Status]; ?>";
					
					break;	
						
				default:
				}

			var options = new Stimulsoft.Designer.StiDesignerOptions();
			options.appearance.fullScreenMode = true;
			
			var designer = new Stimulsoft.Designer.StiDesigner(options, "StiDesigner", false);

			designer.onBeginProcessData = function (args, callback) {
				<?php StiHelper::createHandler(); ?>
			}

			designer.onSaveReport = function (args) {
				<?php StiHelper::createHandler(); ?>
			}

			designer.report = report;
			designer.renderHtml("designerContent");
		}
	</script>
</head>
<body onload="Start()">
	<div id="designerContent"></div>
</body>
</html>