<?php
require_once 'PmController.php';
class CronjobController extends PMController{

    public function init(){
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
        $this->accessHelper = $this->_helper->access;
        $this->closewo_location = 4;
    }
    
    
    public function indexAction(){
        $Task = new Model_PmTemplate();
        $Result = $Task->gettaskdata();
        echo "<pre>";
        print_r($Result);
        $this->GetDataNextUpdate($Result[5]);
        //echo "<pre>";
        //print_r($Result);
        exit(0);
    }
    
    public function GetDataNextUpdate($data){
        
        print_r($data);
        switch($data->Interval){
            
            case "DAY":
                    $avail =  $this->GetDateAvaliable($data);
                    $AvailDay = explode(",", $avail);
                   echo $curentdate  = strtotime("now");
                    $CurrentMonth  = date("F Y",strtotime("now"));
                    // seasonal task 
                    $Seasonal_Task = $data->Seasonal_Task; 
                    if($Seasonal_Task =='Y' && $EndDate < $curentdate){
                        //update startdate end date
                        $update = array(
                                        'Start_date'=>$data->Seasonal_Start_Date,
                                        'End_date'=>$data->Seasonal_End_Date,
                            );
                    }
                    //print_r($data);
                    // end date 
                    
                    if(!empty($data->End_date)){
                        $EndDate = strtotime(date("Y-m-t",strtotime($data->End_date)));
                        if($EndDate < $curentdate){
                                    return false;
                        }
                    }
//                    if($data->Startdate_month=='lastday'){
//                        $startdate = strtotime('28'.$data->Start_date);
//                    }else{
                    $startdate = strtotime($data->Startdate_month.' '.$data->Start_date);
                    //}
                    if($curentdate >= $startdate){
                        if($data->Interval_Data == 0){
                            $day = date('N', strtotime('+1 day', $curentdate));
                            $cmpdate = date("d-m-Y", strtotime('+1 day', $curentdate));
                            $CurrentMonth  = date("F Y",strtotime($cmpdate));
                        }else{                    
                            $day = date('N', strtotime('+'.$data->Interval_Data.' day', $curentdate));
                            $cmpdate = date("d-m-Y", strtotime('+'.$data->Interval_Data.' day', $curentdate));
                            $CurrentMonth  = date("F Y",strtotime($cmpdate));
                        }

                        if(in_array($day,$AvailDay)){             
                            $date = $this->GetLastDay($cmpdate);
//                            if($date == 'lastday'){
//                                 $param = array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
//                            }else{
                                $param = array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                            //}
                        }else{
                           $getdate = $AvailDay[0];
                            $dayName  = $this->GetDayName($getdate);

                            if($data->Interval_Data == 0){
                                $CurrentMonth = date('F Y', strtotime('next '.$dayName,strtotime('+1 day', $curentdate)));
                                $newdate = date("d-m-Y",strtotime('next '.$dayName,strtotime('+1 day', $curentdate)));
                            }else{
                                $CurrentMonth = date('F Y', strtotime('next '.$dayName,strtotime('+'.$data->Interval_Data.' day', $curentdate)));
                                $newdate = date("d-m-Y",strtotime('next '.$dayName,strtotime('+'.$data->Interval_Data.' day', $curentdate)));
                            }
                            $date = $this->GetLastDay($newdate);
                            //if($date == 'lastday'){
                                //$param =array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                           // }else{
                                $param =array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                           //}
                        }    
                    }
                   
            break;
            
            case "WEEK":
                        $avail =  $this->GetDateAvaliable($data);
                        $AvailDay = explode(",", $avail);
                         $curentdate  = strtotime("now");
                         $CurrentMonth  = date("F Y",strtotime("now"));
                        
                        // seasonal task 
                        $Seasonal_Task = $data->Seasonal_Task; 
                        if($Seasonal_Task =='Y' && $EndDate < $curentdate){
                            //update startdate end date
                            $update = array(
                                            'Start_date'=>$data->Seasonal_Start_Date,
                                            'End_date'=>$data->Seasonal_End_Date,
                                );
                        }
                        print_r($data);
                        // end date 
                        if(!empty($data->End_date)){
                            $EndDate = strtotime(date("Y-m-t",strtotime($data->End_date)));
                            if($EndDate < $curentdate){
                                    return false;
                            }
                        }

//                        if($data->Startdate_month=='lastday'){
//                            $startdate = strtotime('28'.$data->Start_date);
//                        }else{
                            $startdate = strtotime($data->Startdate_month.' '.$data->Start_date);
                       // }
                        if($curentdate >= $startdate){
                            if($data->Interval_Data == 0){
                                //$date = date('d', strtotime('+1 day', strtotime("now")));
                                $day = date('N', strtotime('+1 week', $curentdate));
                                $cmpdate = date("d-m-Y", strtotime('+1 week', $curentdate));
                                $CurrentMonth  = date("F Y",strtotime($cmpdate));
                            }else{

                                $day = date('N', strtotime('+'.$data->Interval_Data.' week', $curentdate));
                                $cmpdate = date("d-m-Y", strtotime('+'.$data->Interval_Data.' week', $curentdate));
                                $CurrentMonth  = date("F Y",strtotime($cmpdate));
                            }

                            if(in_array($day,$AvailDay)){

                                $date = $this->GetLastDay($cmpdate);
//                                if($date == 'lastday'){
//                                    $param = array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
//                                }else{
                                    $param = array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                                //}
                            }else{

                                $getdate = $AvailDay[0];
                                $dayName  = $this->GetDayName($getdate);

                                if($data->Interval_Data == 0){                       

                                    $CurrentMonth = date('F Y', strtotime('next '.$dayName,strtotime('+1 week', strtotime("now"))));
                                    $newdate = date("d-m-Y",strtotime('next '.$dayName,strtotime('+1 week', strtotime("now"))));
                                }else{

                                    $CurrentMonth = date('F Y', strtotime('next '.$dayName,strtotime('+'.$data->Interval_Data.' week', strtotime("now"))));
                                    $newdate = date("d-m-Y",strtotime('next '.$dayName,strtotime('+'.$data->Interval_Data.' week', strtotime("now"))));

                                }                

                                $date = $this->GetLastDay($newdate);

//                                if($date == 'lastday'){
//                                    $param =array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
//                                }else{                        
                                    $param =array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                               //}
                            }    
                        }
                        
                break;
                
            case "MONTH":
                        $avail =  $this->GetDateAvaliable($data);
                        $AvailDay = explode(",", $avail);
                        $curentdate  = strtotime("28-01-2018");
                        $CurrentMonth  = date("F Y",$curentdate);
                        // seasonal task 
                        $Seasonal_Task = $data->Seasonal_Task; 
                        if($Seasonal_Task =='Y' && $EndDate < $curentdate){
                            //update startdate end date
                            $update = array(
                                            'Start_date'=>$data->Seasonal_Start_Date,
                                            'End_date'=>$data->Seasonal_End_Date,
                                );
                        }
                        // end date 
                        if(!empty($data->End_date)){
                            $EndDate = strtotime(date("Y-m-t",strtotime($data->End_date)));
                            if($EndDate < $curentdate){
                                    return false;
                            }
                        }
                        if($data->Startdate_month=='lastday'){
                            $startdate = strtotime(date("y-m-t",$curentdate));
                        }else{
                            $startdate = strtotime($data->Startdate_month.' '.$data->Start_date);
                        }
                        if($curentdate >= $startdate){
                            if($data->Interval_Data == 0){
                                if($data->Startdate_month == 'lastday'){
                                    $cdate = date("m-y",$curentdate);
                                    //$day = date('m', strtotime('+1 month', strtotime("01-".$cdate)));
                                    $day = date('N', strtotime(date('t-m-Y', strtotime('+1 month', strtotime("01-".$cdate)))));
                                    //$day = date('N', strtotime('+1 month', $curentdate));
                                    $cmpdate = date("d-m-Y", strtotime(date('t-m-Y', strtotime('+1 month', strtotime("01-".$cdate)))));
                                    $CurrentMonth  = date("F Y",strtotime($cmpdate));
                                }else{
                                    $day = date('N', strtotime('+1 month', $curentdate));
                                    $cmpdate = date("d-m-Y", strtotime('+1 month', $curentdate));
                                    $CurrentMonth  = date("F Y",strtotime($cmpdate));
                                }
                                
                            }else{
                                if($data->Startdate_month == 'lastday'){
                                    $cdate = date("m-y",$curentdate);
                                    //$day = date('m', strtotime('+1 month', strtotime("01-".$cdate)));
                                    $day = date('N', strtotime(date('t-m-Y', strtotime('+'.$data->Interval_Data.' month', strtotime("01-".$cdate)))));
                                    //$day = date('N', strtotime('+1 month', $curentdate));
                                    $cmpdate = date("d-m-Y", strtotime(date('t-m-Y', strtotime('+'.$data->Interval_Data.' month', strtotime("01-".$cdate)))));
                                    $CurrentMonth  = date("F Y",strtotime($cmpdate));
                                }else{
                                    $day = date('N', strtotime('+'.$data->Interval_Data.' month', $curentdate));
                                    $cmpdate = date("d-m-Y", strtotime('+'.$data->Interval_Data.' month', $curentdate));
                                    $CurrentMonth  = date("F Y",strtotime($cmpdate)); 
                                }

                                
                            }

                            if(in_array($day,$AvailDay)){

                                $date = $this->GetLastDay($cmpdate);
                                if($date == 'lastday'){

                                    $param = array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                                }else{

                                    $param = array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                                }
                            }else{

                                $getdate = $AvailDay[0];
                                $dayName  = $this->GetDayName($getdate);
                            
                                if($data->Interval_Data == 0){                     
                                    if($data->Startdate_month == 'lastday'){
     
                                        $CurrentMonth = date('F Y', strtotime('last '.$dayName,strtotime(date('t-m-Y', strtotime('+1 month', strtotime("01-".$cdate))))));
                                        $newdate = date("d-m-Y",strtotime('last '.$dayName,strtotime(date('t-m-Y', strtotime('+1 month', strtotime("01-".$cdate))))));
                                    }else{
                                        $CMonth = date('F Y', strtotime('next '.$dayName,strtotime('+1 month', $curentdate)));
                                        if(strtotime($CurrentMonth)==strtotime($CMonth)){
                                            $CurrentMonth = date('F Y', strtotime('next '.$dayName,strtotime('+1 month', $curentdate)));
                                            $newdate = date("d-m-Y",strtotime('next '.$dayName,strtotime('+1 month', $curentdate)));
                                        }else{
                                            $CurrentMonth = date('F Y', strtotime('last '.$dayName,strtotime('+1 month', $curentdate)));
                                            $newdate = date("d-m-Y",strtotime('last '.$dayName,strtotime('+1 month', $curentdate)));
                                        }
                                        
                                    }
                                    
                                }else{
                                    
                                    if($data->Startdate_month == 'lastday'){     
                                        $CurrentMonth = date('F Y', strtotime('last '.$dayName,strtotime(date('t-m-Y', strtotime('+'.$data->Interval_Data.' month', strtotime("01-".$cdate))))));
                                        $newdate = date("d-m-Y",strtotime('last '.$dayName,strtotime(date('t-m-Y', strtotime('+'.$data->Interval_Data.' month', strtotime("01-".$cdate))))));
                                    }else{
                                        
                                        $CMonth = date('F Y', strtotime('next '.$dayName,strtotime('+1 month', $curentdate)));
                                        if(strtotime($CurrentMonth)==strtotime($CMonth)){
                                            $CurrentMonth = date('F Y', strtotime('next '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                            $newdate = date("d-m-Y",strtotime('next '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                        }else{
                                            $CurrentMonth = date('F Y', strtotime('last '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                            $newdate = date("d-m-Y",strtotime('last '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                        }
                                        
                                        
                                    }

                                }

                                $date = $this->GetLastDay($newdate);
                                if($date == 'lastday'){
                                    $param =array("start_date"=>$CurrentMonth,"startdate_month"=>'lastdate');
                                }else{
                                    //$DateFormated = $date.$this->GetFormateDays($date);
                                    $param =array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                               }
                            }    
                        }
                       
                break;
                
            case "QUATER":
                        $avail =  $this->GetDateAvaliable($data);
                        $AvailDay = explode(",", $avail);
                        $curentdate  = strtotime("now");
                        $CurrentMonth  = date("F Y",$curentdate);
                        
                        // seasonal task 
                        $Seasonal_Task = $data->Seasonal_Task; 
                        if($Seasonal_Task =='Y' && $EndDate < $curentdate){
                            //update startdate end date
                            $update = array(
                                            'Start_date'=>$data->Seasonal_Start_Date,
                                            'End_date'=>$data->Seasonal_End_Date,
                                );
                        }
                        // end date 
                        //$EndDate = strtotime(date("Y-m-t",strtotime($data->End_date)));
                        if(!empty($data->End_date)){
                            $EndDate = strtotime(date("Y-m-t",strtotime($data->End_date)));
                            if($EndDate < $curentdate){
                                    return false;
                            }
                        }
                        if($data->Startdate_month=='lastday'){
                            $startdate = strtotime(date("y-m-t",$curentdate));
                            //$startdate = strtotime('28'.$data->Start_date);
                        }else{
                            $startdate = strtotime($data->Startdate_month.' '.$data->Start_date);
                        }
                        if($curentdate >= $startdate){
                            if($data->Interval_Data == 0){
                                if($data->Startdate_month == 'lastday'){
                                    $cdate = date("m-y",$curentdate);
                                    //$day = date('m', strtotime('+1 month', strtotime("01-".$cdate)));
                                    $day = date('N', strtotime(date('t-m-Y', strtotime('+3 month', strtotime("01-".$cdate)))));
                                    //$day = date('N', strtotime('+1 month', $curentdate));
                                    $cmpdate = date("d-m-Y", strtotime(date('t-m-Y', strtotime('+3 month', strtotime("01-".$cdate)))));
                                    $CurrentMonth  = date("F Y",strtotime($cmpdate));
                                }else{
                                    $day = date('N', strtotime('+3 month', $curentdate));
                                    $cmpdate = date("d-m-Y", strtotime('+3 month', $curentdate));
                                    $CurrentMonth  = date("F Y",strtotime($cmpdate));
                                }
                                
                            }else{
                                if($data->Startdate_month == 'lastday'){
                                    $cdate = date("m-y",$curentdate);
                                    //$day = date('m', strtotime('+1 month', strtotime("01-".$cdate)));
                                    $day = date('N', strtotime(date('t-m-Y', strtotime('+'.$data->Interval_Data.' month', strtotime("01-".$cdate)))));
                                    //$day = date('N', strtotime('+1 month', $curentdate));
                                    $cmpdate = date("d-m-Y", strtotime(date('t-m-Y', strtotime('+'.$data->Interval_Data.' month', strtotime("01-".$cdate)))));
                                    $CurrentMonth  = date("F Y",strtotime($cmpdate));
                                }else{
                                    $day = date('N', strtotime('+'.$data->Interval_Data.' month', $curentdate));
                                    $cmpdate = date("d-m-Y", strtotime('+'.$data->Interval_Data.' month', $curentdate));
                                    $CurrentMonth  = date("F Y",strtotime($cmpdate)); 
                                }

                                
                            }

                            if(in_array($day,$AvailDay)){

                                $date = $this->GetLastDay($cmpdate);
                                if($date == 'lastday'){

                                    $param = array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                                }else{

                                    $param = array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                                }
                            }else{

                                $getdate = $AvailDay[0];
                                $dayName  = $this->GetDayName($getdate);
                            
                                if($data->Interval_Data == 0){                     
                                    if($data->Startdate_month == 'lastday'){
     
                                        $CurrentMonth = date('F Y', strtotime('last '.$dayName,strtotime(date('t-m-Y', strtotime('+3 month', strtotime("01-".$cdate))))));
                                        $newdate = date("d-m-Y",strtotime('last '.$dayName,strtotime(date('t-m-Y', strtotime('+3 month', strtotime("01-".$cdate))))));
                                    }else{
                                        $CMonth = date('F Y', strtotime('next '.$dayName,strtotime('+3 month', $curentdate)));
                                        if(strtotime($CurrentMonth)==strtotime($CMonth)){
                                            $CurrentMonth = date('F Y', strtotime('next '.$dayName,strtotime('+3 month', $curentdate)));
                                            $newdate = date("d-m-Y",strtotime('next '.$dayName,strtotime('+3 month', $curentdate)));
                                        }else{
                                            $CurrentMonth = date('F Y', strtotime('last '.$dayName,strtotime('+3 month', $curentdate)));
                                            $newdate = date("d-m-Y",strtotime('last '.$dayName,strtotime('+3 month', $curentdate)));
                                        }
                                        
                                    }
                                    
                                }else{
                                    
                                    if($data->Startdate_month == 'lastday'){     
                                        $CurrentMonth = date('F Y', strtotime('last '.$dayName,strtotime(date('t-m-Y', strtotime('+'.$data->Interval_Data.' month', strtotime("01-".$cdate))))));
                                        $newdate = date("d-m-Y",strtotime('last '.$dayName,strtotime(date('t-m-Y', strtotime('+'.$data->Interval_Data.' month', strtotime("01-".$cdate))))));
                                    }else{
                                        
                                        $CMonth = date('F Y', strtotime('next '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                        if(strtotime($CurrentMonth)==strtotime($CMonth)){
                                            $CurrentMonth = date('F Y', strtotime('next '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                            $newdate = date("d-m-Y",strtotime('next '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                        }else{
                                            $CurrentMonth = date('F Y', strtotime('last '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                            $newdate = date("d-m-Y",strtotime('last '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                        }
                                        
                                        
                                    }

                                }

                                $date = $this->GetLastDay($newdate);
                                if($date == 'lastday'){
                                    $param =array("start_date"=>$CurrentMonth,"startdate_month"=>'lastdate');
                                }else{
                                    //$DateFormated = $date.$this->GetFormateDays($date);
                                    $param =array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                               }
                            }    
                        }
                break;
                
            case "YEAR":
                    $avail =  $this->GetDateAvaliable($data);
                    $AvailDay = explode(",", $avail);
                    $curentdate  = strtotime(date("Y-m-d",strtotime("now")));
                    
                    
                    // end date 
                    $EndDate = strtotime(date("Y-m-t",strtotime($data->End_date)));
                    if($EndDate < $curentdate){
                                return false;
                    }
                    
                    //$CurrentMonth  = date("F Y",strtotime("now"));
                    
                    if($data->Startdate_month=='lastday'){
                        $startdate = strtotime('28'.$data->Start_date);
                    }else{
                        $startdate = strtotime($data->Startdate_month.' '.$data->Start_date);
                    }
                    if($curentdate >= $startdate){
                            if($data->Interval_Data == 0){
                                if($data->Startdate_month == 'lastday'){
                                    $cdate = date("m-y",$curentdate);
                                    //$day = date('m', strtotime('+1 month', strtotime("01-".$cdate)));
                                    $day = date('N', strtotime(date('t-m-Y', strtotime('+3 month', strtotime("01-".$cdate)))));
                                    //$day = date('N', strtotime('+1 month', $curentdate));
                                    $cmpdate = date("d-m-Y", strtotime(date('t-m-Y', strtotime('+3 month', strtotime("01-".$cdate)))));
                                    $CurrentMonth  = date("F Y",strtotime($cmpdate));
                                }else{
                                    $day = date('N', strtotime('+3 month', $curentdate));
                                    $cmpdate = date("d-m-Y", strtotime('+3 month', $curentdate));
                                    $CurrentMonth  = date("F Y",strtotime($cmpdate));
                                }
                                
                            }else{
                                if($data->Startdate_month == 'lastday'){
                                    $cdate = date("m-y",$curentdate);
                                    //$day = date('m', strtotime('+1 month', strtotime("01-".$cdate)));
                                    $day = date('N', strtotime(date('t-m-Y', strtotime('+'.$data->Interval_Data.' month', strtotime("01-".$cdate)))));
                                    //$day = date('N', strtotime('+1 month', $curentdate));
                                    $cmpdate = date("d-m-Y", strtotime(date('t-m-Y', strtotime('+'.$data->Interval_Data.' month', strtotime("01-".$cdate)))));
                                    $CurrentMonth  = date("F Y",strtotime($cmpdate));
                                }else{
                                    $day = date('N', strtotime('+'.$data->Interval_Data.' month', $curentdate));
                                    $cmpdate = date("d-m-Y", strtotime('+'.$data->Interval_Data.' month', $curentdate));
                                    $CurrentMonth  = date("F Y",strtotime($cmpdate)); 
                                }

                                
                            }

                            if(in_array($day,$AvailDay)){

                                $date = $this->GetLastDay($cmpdate);
                                if($date == 'lastday'){

                                    $param = array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                                }else{

                                    $param = array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                                }
                            }else{

                                $getdate = $AvailDay[0];
                                $dayName  = $this->GetDayName($getdate);
                            
                                if($data->Interval_Data == 0){                     
                                    if($data->Startdate_month == 'lastday'){
     
                                        $CurrentMonth = date('F Y', strtotime('last '.$dayName,strtotime(date('t-m-Y', strtotime('+3 month', strtotime("01-".$cdate))))));
                                        $newdate = date("d-m-Y",strtotime('last '.$dayName,strtotime(date('t-m-Y', strtotime('+3 month', strtotime("01-".$cdate))))));
                                    }else{
                                        $CMonth = date('F Y', strtotime('next '.$dayName,strtotime('+3 month', $curentdate)));
                                        if(strtotime($CurrentMonth)==strtotime($CMonth)){
                                            $CurrentMonth = date('F Y', strtotime('next '.$dayName,strtotime('+3 month', $curentdate)));
                                            $newdate = date("d-m-Y",strtotime('next '.$dayName,strtotime('+3 month', $curentdate)));
                                        }else{
                                            $CurrentMonth = date('F Y', strtotime('last '.$dayName,strtotime('+3 month', $curentdate)));
                                            $newdate = date("d-m-Y",strtotime('last '.$dayName,strtotime('+3 month', $curentdate)));
                                        }
                                        
                                    }
                                    
                                }else{
                                    
                                    if($data->Startdate_month == 'lastday'){     
                                        $CurrentMonth = date('F Y', strtotime('last '.$dayName,strtotime(date('t-m-Y', strtotime('+'.$data->Interval_Data.' month', strtotime("01-".$cdate))))));
                                        $newdate = date("d-m-Y",strtotime('last '.$dayName,strtotime(date('t-m-Y', strtotime('+'.$data->Interval_Data.' month', strtotime("01-".$cdate))))));
                                    }else{
                                        
                                        $CMonth = date('F Y', strtotime('next '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                        if(strtotime($CurrentMonth)==strtotime($CMonth)){
                                            $CurrentMonth = date('F Y', strtotime('next '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                            $newdate = date("d-m-Y",strtotime('next '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                        }else{
                                            $CurrentMonth = date('F Y', strtotime('last '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                            $newdate = date("d-m-Y",strtotime('last '.$dayName,strtotime('+'.$data->Interval_Data.' month', $curentdate)));
                                        }
                                        
                                        
                                    }

                                }

                                $date = $this->GetLastDay($newdate);
                                if($date == 'lastday'){
                                    $param =array("start_date"=>$CurrentMonth,"startdate_month"=>'lastdate');
                                }else{
                                    //$DateFormated = $date.$this->GetFormateDays($date);
                                    $param =array("start_date"=>$CurrentMonth,"startdate_month"=>$date);
                               }
                            }    
                        }
                break;
        }
      
        print_r($param);
    
    }
   
    
    // lastdate checking 
    public function GetLastDay($Date){
        $lastdate = date('t',strtotime($Date));
        $date = date('d',strtotime($Date));
        if($lastdate==$date){
            return "lastday";
        }else{
            return $date;
        }        
    }
    
    // last date
    public function GetlastdayDate($month){
      return  date("Y-m-t", strtotime($month));
    }
 
    
// Get available date     
    
    public function GetDateAvaliable($data){
        $adj = $data->AU_sda_ID;
        switch($adj){
          case 1: 
              $days = '1,2,3,4,5';
              break;
          case 2:
             $days = '6,7';
              break; 
          case 3:
             $days = '7';
              break; 
          case 4:
             $days = '6';
              break; 
          case 5:
             $days = '1';
              break; 
          case 6:
             $days = '2';
              break; 
          case 7:
             $days = '3';
              break;
          case 8:
             $days = '4';
              break;
          case 9:
             $days = '5';
              break;
        }
        
        return $days;        
    }
 // Get date formate   
    public function GetFormateDays($n){
        if ($n >= 11 && $n <= 13){
            return "th";
        }
        switch ($n % 10) {
            case 1:  
                return "st";
                break;
            case 2:  return "nd";
                break;
            case 3:  return "rd";
                break;
            default: 
                return "th";
                break;
        }
    }
 // get date name   
    public function GetDayName($day){
        
        if(!empty($day)){
            
            $dayName ="";
            
            switch($day){
                case 1:
                    $dayName = "Monday";
                    break;
                case 2:
                    $dayName = "Tuesday";
                    break;
                case 3:
                    $dayName = "Wednesday";
                    break;
                case 4:
                    $dayName = "Thursday";
                    break;
                case 5:
                    $dayName = "Friday";
                    break;
                case 6:
                    $dayName = "Saturday";
                    break;
                case 7:
                    $dayName = "Sunday";
                    break;
            }
            return $dayName;
        }           
    }               
}