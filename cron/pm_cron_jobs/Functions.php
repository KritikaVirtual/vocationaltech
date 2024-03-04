<?php


function adjustDateandWeek($Pass_Current_Work_Order_Date, $Next_WO_Day_Of_Week_Number, $PasStartDateAdjustement, $PassFreq_Inverval_Addinit, $PassMultipler, $Calculate_Next_Work_Order_Date){
	
	$Current_WO_Day_of_Week = $Pass_Current_Work_Order_Date->format('l');                           // Get the Acutal Day the week of the WO Date
	// Get the Day of the week in number form
	// 1 - Monday
	// 2 - Tuesday
	// 3 - Wednesday
	// 4 - Thursday
	// 5 - Friday
	// 6 - Saturday
	// 7 - Sunday
	$Current_WO_Day_Of_Week_Number = $Pass_Current_Work_Order_Date->format('N');

	echo "WO-I-754 - Current WO falls on a ". $Pass_Current_Work_Order_Date->format('l') . " (". $Current_WO_Day_Of_Week_Number .") <br>\r";
	
	switch($PasStartDateAdjustement) 
	{                                           // 2000 - Begin switch $row["StartDateAdjustment"]
		case "Monday thru Friday":
					echo "WO-I-755 - Next WO Day falls on a ". $Calculate_Next_Work_Order_Date->format('l') . " (". ($Next_WO_Day_Of_Week_Number) .") <br>\r";
					switch($Next_WO_Day_Of_Week_Number) 
					{                                          // 2100 - Begin Switch $Next_WO_Day_Of_Week_Number
	
						case ($Next_WO_Day_Of_Week_Number > 5):          // Saturday or Sunday - adjust to the next Monday for New Work Order Date
						
								if ($PassFreq_Inverval_Addinit == "DAY" || $PassFreq_Inverval_Addinit == "WEEK" ){
									echo "WO-I-756 - WO Detected Generate Daily/Weekly Task(s)/Reading(s) for current Month, 
										will adjust next work Order to the Next Monday because WO currently falls on a ." . $Calculate_Next_Work_Order_Date->format('l') ." <br>\r";
									$Calculate_Next_Work_Order_Date =  $Calculate_Next_Work_Order_Date->modify('next monday');
									echo "WO-I-757 - Next WO Date Adjusted Date to  ". $Calculate_Next_Work_Order_Date->format('Y-m-d') ."<br>\r";
									echo "Muliiplyer : " . $PassMultipler  . " Pass Frequency " . $PassFreq_Inverval_Addinit . "Date Calc: " . $Calculate_Next_Work_Order_Date->format('Y-m-d') ."<br>\r";
								}
								break; 
		  
						default:
							// echo "WO-I-759 - Next WO is falls between M-F Day - no adjustemnt needed <br>\r";
							break;
					} 
					
					switch($Current_WO_Day_Of_Week_Number){
						
						case ($Current_WO_Day_Of_Week_Number > 5): 
						
								echo "WO-I-760 - Current WO to be adjusted to next Monday because the current WO date falls on a " .$Pass_Current_Work_Order_Date->format('l') ." <br>\r";
								$Pass_Current_Work_Order_Date = $Pass_Current_Work_Order_Date->modify('next monday');
								echo "WO-I-761 - Updated Current WO to  " .$Pass_Current_Work_Order_Date->format('l') . " | " . $Pass_Current_Work_Order_Date->format('Y-m-d') ." <br>\r";
								break;
					
					}			  
					break;
  
		case "Every Day (Sun-Sat)";                                               // Do Nothing - 				
					break;
		case "Sunday":
					if(($PassFreq_Inverval_Addinit == "DAY" || $PassFreq_Inverval_Addinit == "WEEK") && $Calculate_Next_Work_Order_Date->format('N')!= 7)
						{$Calculate_Next_Work_Order_Date->modify('next sunday');}
					if($Next_WO_Day_Of_Week_Number != 7){$Pass_Current_Work_Order_Date->modify('next sunday');} 
					break;		 
		case "Saturday":
					if(($PassFreq_Inverval_Addinit == "DAY" || $PassFreq_Inverval_Addinit == "WEEK") && $Calculate_Next_Work_Order_Date->format('N')!= 6 )
						{$Calculate_Next_Work_Order_Date->modify('next saturday');}
					if($Next_WO_Day_Of_Week_Number != 6){$Pass_Current_Work_Order_Date->modify('next saturday');} 
					break;
		case "Monday":
					if(($PassFreq_Inverval_Addinit == "DAY" || $PassFreq_Inverval_Addinit == "WEEK") && $Calculate_Next_Work_Order_Date->format('N')!= 1 )
						{$Calculate_Next_Work_Order_Date->modify('next monday');} 
					if($Next_WO_Day_Of_Week_Number != 1){$Pass_Current_Work_Order_Date->modify('next monday');}
					break;
		case "Tuesday":
					if(($PassFreq_Inverval_Addinit == "DAY" || $PassFreq_Inverval_Addinit == "WEEK") && $Calculate_Next_Work_Order_Date->format('N')!= 2 )
						{$Calculate_Next_Work_Order_Date->modify('next tuesday');} 
					if($Next_WO_Day_Of_Week_Number != 2){$Pass_Current_Work_Order_Date->modify('next tuesday');}
					break;
		case "Wednesday":
					if(($PassFreq_Inverval_Addinit == "DAY" || $PassFreq_Inverval_Addinit == "WEEK") && $Calculate_Next_Work_Order_Date->format('N')!= 3 )
					{$Calculate_Next_Work_Order_Date->modify('next wednesday');	} 
					if($Next_WO_Day_Of_Week_Number != 3){$Pass_Current_Work_Order_Date->modify('next wednesday'); echo "WO-I-DOW - Change to next Wednesday <br>\r";}
					break;
		case "Thursday":
					if(($PassFreq_Inverval_Addinit == "DAY" || $PassFreq_Inverval_Addinit == "WEEK") && $Calculate_Next_Work_Order_Date->format('N')!= 4 )
						{$Calculate_Next_Work_Order_Date->modify('next friday');} 
					if($Next_WO_Day_Of_Week_Number != 4){$Pass_Current_Work_Order_Date->modify('next friday');}
					break;
		case "Friday":
					if(($PassFreq_Inverval_Addinit == "DAY" || $PassFreq_Inverval_Addinit == "WEEK") && $Calculate_Next_Work_Order_Date->format('N')!= 5 )
						{$Calculate_Next_Work_Order_Date->modify('next friday');} 
					if($Next_WO_Day_Of_Week_Number != 5){$Pass_Current_Work_Order_Date->modify('next friday');}
					break;
	}
}
?>