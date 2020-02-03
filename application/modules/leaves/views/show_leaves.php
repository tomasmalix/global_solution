<div class="content">
	<div class="row">
		<div class="col-sm-8 col-xs-3">
			<?php 
			$user_id = $this->uri->segment(3);
			$username = $this->db->get_where('dgt_account_details',array('user_id'=>$user_id))->row_array();
			
			?>
			<h4 class="page-title"><?php echo $username['fullname']?> Leaves</h4>
		</div>
		<div class="col-sm-4 col-xs-9 text-right m-b-30">
			<?php if (($this->tank_auth->user_role($this->tank_auth->get_role_id()) != 'admin') && ($this->tank_auth->user_role($this->tank_auth->get_role_id()) != 'superadmin')) { ?>
			<a href="javascript:;" class="btn btn-primary rounded pull-right New-Leave" onclick="$('.new_leave_reqst').show();$('#date_alert_msg').hide();" data-loginid="<?php echo $this->session->userdata('user_id'); ?>" ><i class="fa fa-plus"></i> <?='New Leave Request';?></a>
			<?php } ?>
		</div>
	</div>
	<?php  if($this->session->flashdata('alert_message')){?>
	<div class="panel panel-default" id="date_alert_msg">
		<div class="panel-heading font-bold" style="color:white; background:#FF0000">
			<i class="fa fa-info-circle"></i> Alert Details 
			<i class="fa fa-times pull-right" style="cursor:pointer" onclick="$('#date_alert_msg').hide();"></i>
		</div>
		<div class="panel-body">
			<p style="color:red"> Already you have make request for now requested Dates! Please Check...</p>
		</div>
	</div>
	<?php  }  ?>  
	
	<?php $leav_types =  $this->db->query("SELECT * FROM `dgt_common_leave_types` where status = 0")->result_array(); 
			$check_teamlead = $this->db->get_where('dgt_users',array('id'=>$this->session->userdata('user_id')))->row_array(); 

	 ?> 

	
	<?php if (($this->tank_auth->user_role($this->tank_auth->get_role_id()) == 'admin') || ($this->tank_auth->user_role($this->tank_auth->get_role_id()) == 'superadmin') || ($check_teamlead['is_teamlead'] =='yes')) { 


		
		$annual_leave = $this->db->get_where('dgt_common_leave_types',array('leave_id'=> 1))->row_array();
		$sick_leave = $this->db->get_where('dgt_common_leave_types',array('leave_id'=> 4))->row_array();
		$this->db->select_sum('leave_days');
		$this->db->where('leave_id !=','1');
		$this->db->where('leave_id !=','4');
		if($username['gender'] =='male'){
			$this->db->where('leave_id !=','6');
		}
		if($username['gender'] =='female'){
			$this->db->where('leave_id !=','7');
		}
		$this->db->where('status','0');
		$other_leave = $this->db->get('dgt_common_leave_types')->row_array();	

		 // echo "<pre>";	print($other_leave); exit;


		$this->db->select_sum('leave_days');
		if($username['gender'] =='male'){
			$this->db->where('leave_id !=','6');
		}
		if($username['gender'] =='female'){
			$this->db->where('leave_id !=','7');
		}
		$this->db->where('status','0');
		$total_leave = $this->db->get('dgt_common_leave_types')->row_array();


		$this->db->select_sum('leave_days');
		$annual_leave_count = $this->db->get_where('user_leaves',array('leave_type'=> 1,'user_id'=>$user_id,'status'=>1))->row_array();

		$this->db->select_sum('leave_days');
		$sick_leave_count = $this->db->get_where('user_leaves',array('leave_type'=> 4,'user_id'=>$user_id,'status'=>1))->row_array();

		$this->db->select_sum('leave_days');
		$this->db->where('leave_type !=','1');
		$this->db->where('leave_type !=','4');
		$other_leave_count = $this->db->get_where('user_leaves',array('user_id'=>$user_id,'status'=>1))->row_array();
		

		$this->db->select_sum('leave_days');
		$leave_count = $this->db->get_where('user_leaves',array('user_id'=> $user_id,'status'=>1))->row_array();  

		$this->db->select_sum('leave_days');
		$leave_dayss = $this->db->get('common_leave_types')->row_array();

		$sk_lops = ($sick_leave['leave_days'] - $sick_leave_count['leave_days']);
				if($sk_lops < 0 )
				{
					$sick_lop = abs($sk_lops);
				}else{
					$sick_lop = 0;
				}
				$tot_anu_count = ($annual_leave['leave_days'] - $annual_leave_count['leave_days']);
				if($tot_anu_count < 0 )
				{
					$anu_lop = abs($tot_anu_count);
				}else{
					$anu_lop = 0;
				}
				// $tot_hosp_count = ($hospiatality_leaves['leave_days'] - $total_hosp_leave);
				// if($tot_hosp_count < 0 )
				// {
				// 	$hosp_lop = abs($tot_hosp_count);
				// }else{
				// 	$hosp_lop = 0;
				// }

				$tot_other_count = ($other_leave['leave_days'] - $other_leave_count['leave_days']);
				if($tot_other_count < 0 )
				{
					$other_lop = abs($tot_other_count);
				}else{
					$other_lop = 0;
				}


				$total_lop = ($anu_lop + $sick_lop + $other_lop);
		  
		?>

		<!-- Leave Statistics -->
					<div class="row">
						<div class="col-md-3">
							<div class="stats-info">
								<h6>Annual Leave</h6>
								<h4> 
									<?php if($annual_leave_count['leave_days'] != '') { echo $annual_leave_count['leave_days']; } elseif($annual_leave_count['leave_days'] == '') { echo '0'; } ?> / <?php echo $annual_leave['leave_days'];?>
								</h4>
							</div>
						</div>
						<div class="col-md-3">
							<div class="stats-info">
								<h6>Sick Leave</h6>
								<h4><?php if($sick_leave_count['leave_days'] != '') { echo $sick_leave_count['leave_days']; } elseif($sick_leave_count['leave_days'] == '') { echo '0'; } ?> / <?php echo $sick_leave['leave_days'];?></h4>
							</div>
						</div>
						<div class="col-md-3">
							<div class="stats-info">
								<h6>Other Leaves</h6>
								<h4><?php if($other_leave_count['leave_days'] != '') { echo $other_leave_count['leave_days']; } elseif($other_leave_count['leave_days'] == '') { echo '0'; } ?> / <?php echo $other_leave['leave_days'];?></h4>
							</div>
						</div>
						<div class="col-md-3">
							<div class="stats-info">
								<h6>Total Leaves</h6>
								<h4><?php echo $leave_count['leave_days']?>/<?php echo($total_leave['leave_days'] != 0)?$total_leave['leave_days']:0;?></h4> 
								
							</div>
						</div>
						<div class="col-md-3">
							<div class="stats-info">
								<h6>Loss of Pay</h6>
								<h4>
								<!-- <?php
								if($leave_count['leave_days'] > $leave_dayss['leave_days'])
								{
									$lop = $leave_count['leave_days'] - $leave_dayss['leave_days'];
								} 
								else
								{
									$lop = 0;
									
								}
								?><?php echo $lop?> -->
								<?php  echo $total_lop;?>
								</h4>
							</div>
						</div>
					</div>
					<!-- /Leave Statistics -->






	

		<div class="row">
		<div class="col-md-12">
		<div class="table-responsive">
			<?php 
			$user_id = $this->uri->segment(3);
			

	  		
			$leave_details = $this->db->query("SELECT ul.*,lt.leave_type as l_type,ad.fullname
										FROM `dgt_user_leaves` ul
										left join dgt_common_leave_types lt on lt.leave_id = ul.leave_type
										left join dgt_account_details ad on ad.user_id = ul.user_id 
										where ul.user_id='".$user_id."' ")->result_array();
			 // print_r($leave_details); exit;
	   		?>
			 <table id="table-holidays" class="table table-striped custom-table m-b-0 AppendDataTables">
				<thead>
					<tr class="table_heading">
						<th> No </th>
						<th> Leave Type </th>
						<th> From </th>
						<th> To </th>
						<th> Reason </th> 
						<th> No.of Days </th>  
						<th> Status </th>  
						<th> Approval Reason </th>  
						
					</tr>
				</thead>
				<tbody id="admin_leave_tbl">
					<?php 
					if(!empty($leave_details)){
					 foreach($leave_details as $key => $details){  ?>
					
					<tr>
						<td><?=$key+1?></td>
						
						<td><?=$details['l_type']?></td>
						<td><?=date('d-m-Y',strtotime($details['leave_from']))?></td>
						<td><?=date('d-m-Y',strtotime($details['leave_to']))?></td>
						<td width="30%"><?=$details['leave_reason']?></td>
						<td>
							<?php 
							echo $details['leave_days'];
							if($details['leave_day_type'] == 1){
								echo ' ( Full Day )';
							}else if($details['leave_day_type'] == 2){
								echo ' ( First Half )';
							}else if($details['leave_day_type'] == 3){
								echo ' ( Second Half )';
							}?>
						  </td>
						<td>
						<?php
						if($details['status'] == 4){
								echo '<span class="label label-info"> TL - Approved</span><br>';
								echo '<span class="label label-danger"> Management - Pending</span>';
							}else if($details['status'] == 7){
										echo '<span class="label label-danger"> Deleted </span>';
									}
							if($details['status'] == 0){
								echo ' <span class="label" style="background:#D2691E"> Pending </span>';
							}else if($details['status'] == 1){
								echo '<span class="label label-success"> Approved </span> ';
							}else if($details['status'] == 2){
								echo '<span class="label label-danger"> Rejected</span>';
							}else if($details['status'] == 3){
								echo '<span class="label label-danger"> Cancelled</span>';
							}
							?>
						</td>
						<td><?php echo $details['reason']?$details['reason']:'-'; ?></td>
					</tr>
				 <?php  } ?>  
				 <?php  }else{ ?>
						 <tr><td class="text-center" colspan="9">No details were found</td></tr>
						 <?php } ?>  
				</tbody>
		   </table>    
	    </div>
		</div>
		</div>
		<!-- user leave end -->
		<?php } ?>
		

			

		


