<section class="w-48 fr ch-home-form" style="z-index:9999">
<section class="ch-form-header">
 <div class="rowGrid">
                <div class="col01">#</div>
                <div class="col02">Account Number</div>
                <div class="col02">Company Name</div>
                <div class="col02">Date Activated</div>
                <div class="col02">Status</div>
                <div class="col02 lastcol"><a href="javascript:void(0);" id="openaddcompanydiv">Add Company</a></div>
</div>			
             
</section>
<div class="gridContainer">
<div class="tableGrid">
               <?php if(count($this->companyListing)>0){$i=1;foreach($this->companyListing as $companydata){?>              
              <div class="rowGrid <?php if($i%2!==0){?>utr<?php }?>">
                <div class="col01"><?php echo $companydata['cust_id'];?></div>
                <div class="col02"><?php echo $companydata['corp_account_number'];?></div>
                <div class="col02"><?php echo $companydata['companyName'];?></div>
                <div class="col02"><?php echo date('M d, Y',strtotime($companydata['activationDate']));?></div>
                <div class="col02"><?php if($companydata['status']==1){echo "Yes";}else{echo "No";};?></div>
                <div class="col02 lastcol"><a href="javascript:void(0);" class="editcompany" data-id="<?php echo $companydata['cust_id']?>">Edit</a> | <a href="javascript:void(0);" class="deleteCompany" data-id="<?php echo $companydata['cust_id']?>">Delete</a></div>
              </div>
			  <?php $i++;}}else{?>
			  Empty records
			  <?php }?>
               
</div>
</div>

</section>