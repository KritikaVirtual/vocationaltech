function isDate(txtDate)
{
    var currVal = txtDate;
    if(currVal == '')
        return false;
    
    var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/; //Declare Regex
    var dtArray = currVal.match(rxDatePattern); // is format OK?
    
    if (dtArray == null)
        return false;
    
    //Checks for mm/dd/yyyy format.
    dtDay = dtArray[1];
    dtMonth= dtArray[3];
    dtYear = dtArray[5];        
    
    if (dtMonth < 1 || dtMonth > 12)
        return false;
    else if (dtDay < 1 || dtDay> 31)
        return false;
    else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)
        return false;
    else if (dtMonth == 2)
    {
        var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
        if (dtDay> 29 || (dtDay ==29 && !isleap))
                return false;
    }
    return true;
}



var NumberofDigitsAfterDecimal =  8 ;
function CheckUnit(obj)
{
   //alert(obj);
   if(obj =='' )
   {
    return  true;
   }
   else
   {
     if(isNaN(obj))        
    return  false;
     else
    {
        var val = obj;
        if(val.indexOf('.') > -1)
        {
             if(val.length - (val.indexOf('.')+1) > NumberofDigitsAfterDecimal)
                return  false;
             else
               return  true;
        }
        else
        {
              if(parseInt(val) >= 0)
                return  true;
              else
                 return  false;
        }
    }
  }
}