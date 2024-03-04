<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head profile="http://gmpg.org/xfn/11">
<title>Administrator</title>

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script>
var baseUrl='<?php echo BASEURL ?>';
</script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>

<link href="<?php  echo   BASEURL   . '/public/style.css'; ?>" rel="stylesheet" type="text/css"/>

	
</head>
<body>
<div class="wrapper">

    
   
<div class="header-bg">
     <div class="header">
          <div class="leftlogo">
               <a href="http://dev.businessgaselectric.org.uk/"><img border="0" src="<?php echo BASEURL ?>/public/images/logo.png"></a>
          </div>
          <div class="rightlogo">
               <div class="rightmenu">
						<?php echo $_SESSION['uuser'] ?> [<a href="<?php echo BASEURL ?>/logout">Log out</a>]		   
			   </div>

               <div class="mainmenu"><div class="menu-top-navigation-container">
			   
			   
			   <ul class="menu" id="menu-top-navigation">
					<li class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-7" id="menu-item-7"><a href="<?php echo  BASEURL ?>/site">Manage Site</a></li>
					<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-8" id="menu-item-8"><a href="<?php echo BASEURL ?>/staff">Manage Staff</a></li>
					<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-8" id="menu-item-8"><a href="<?php echo BASEURL ?>/quotes">Manage Quotes</a></li>
					<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-11" id="menu-item-11"><a href="<?php echo BASEURL ?>/sales">Manage Sales</a></li>
					<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-9" id="menu-item-9"><a href="<?php echo BASEURL ?>/prospects/online">Online Prospects</a></li>
					<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-10" id="menu-item-10"><a href="<?php echo BASEURL ?>/prospects/online">Offline Prospects</a></li>
				</ul>
 
</div></div>
          </div>
     </div>
   </div>   
   
   
   
	<div class="cotentadmin" style="width: 1260px;">