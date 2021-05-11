

<div class="content">
	<div class="row">
		<div class="col-sm-4 col-xs-3">
			<h4 class="page-title"><?=lang('expenses')?></h4>
		</div>
		<div class="col-sm-8 col-xs-9 text-right m-b-0">
			<?php if (($this->tank_auth->user_role($this->tank_auth->get_role_id()) != 'admin') && ($this->tank_auth->user_role($this->tank_auth->get_role_id()) != 'superadmin')) { ?>
			<a href="<?=base_url()?>expenses/create" data-toggle="ajaxModal" title="<?=lang('create_expense')?>" class="btn btn-primary rounded pull-right"><i class="fa fa-plus"></i> <?=lang('create_expense')?></a>
			<?php } ?>
		</div>
	</div>

	<div class="row filter-row">
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">  
			<div class="form-group form-focus">
				<label class="control-label">Project Name</label>
				<input type="text" class="form-control floating" id="expenes_project" name="expenes_project">
				<label id="expenes_project_error" class="error display-none" for="expenes_project">Project Name Shouldn't be empty</label>
			</div>
		</div>
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">  
			<div class="form-group form-focus">
				<label class="control-label">Client</label>
				<input type="text" class="form-control floating" id="expenes_client" name="expenes_client">
				<label id="expenes_client_error" class="error display-none" for="expenes_client">Client Shouldn't be empty</label>
			</div>
		</div>
		<?php 
			$categories = App::get_by_where('categories',array('module'=>'expenses'));

		 ?>
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12"> 
			<div class="form-group form-focus select-focus">
				<label class="control-label">Category</label>
				<select class="form-control select floating" id="expenses_category" name="expenses_category"> 
					<option value="" selected="selected"> All Categories </option>
					<?php 
						if(!empty($categories)){
							foreach ($categories as $category) { ?>
							<option value="<?php echo $category->cat_name; ?>"><?php echo $category->cat_name; ?></option>	
							<?php }
						}
					 ?>
					
				</select>
				<label id="expenses_category_error" class="error display-none" for="expenses_category">Please Select a Category</label>
			</div>
		</div>
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">  
			<div class="form-group form-focus">
				<label class="control-label">From</label>
				<div class="cal-icon">
					<input class="form-control floating" id="expenses_date_from" name="expenses_date_from" type="text"></div>
					<label id="expenses_date_from_error" class="error display-none" for="expenses_date_from">To Date Shouldn't be empty</label>
			</div>
		</div>
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">  
			<div class="form-group form-focus">
				<label class="control-label">To</label>
				<div class="cal-icon">
					<input class="form-control floating" id="expenses_date_to" name="expenses_date_to" type="text"></div>
					<label id="expenses_date_to_error" class="error display-none" for="expenses_date_to">From Date Shouldn't be empty</label>
			</div>
		</div>
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">  
			<a href="javascript:void(0)" class="btn btn-success btn-block" id="search_expenses_btn"> Search </a>  
		</div>     
	</div>
	<?php if (($this->tank_auth->user_role($this->tank_auth->get_role_id()) == 'admin') || ($this->tank_auth->user_role($this->tank_auth->get_role_id()) == 'superadmin')) { 

	$expenses = $this->db->query("SELECT * FROM `dgt_expenses` order by id  ASC ")->result();
	?>
	<div class="row">
	<div class="col-md-12">
	<div class="table-responsive">
		<table id="table-expenses" class="table table-striped custom-table m-b-0">
			<thead>
				<tr>
					<th style="width:5px;">No</th>
					<th class=""><?=lang('project')?></th>
					<th class="col-currency"><?=lang('amount')?></th>
					<?php if(User::is_admin()) { ?> 
					<th class=""><?=lang('staff_name')?></th>					
					<?php } ?> 
					<th class=""><?=lang('category')?></th>
					<th style="width:5px; display:none;"></th>
					<th class=""><?=lang('expense_date')?></th>
					<th class=""><?=lang('status')?></th>
					<?php if(User::is_admin()) { ?> 
					<!--<th class=""><?=lang('expense_action')?></th>					-->
					<?php } ?> 
					<th class="text-right">Action</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($expenses)){
			 foreach ($expenses as $key => $e) { 
			if($e->project != '' || $e->project != 'NULL'){
			$p = Project::by_id($e->project);
			}else{ $p = NULL; } ?>
			<tr id="row_id_<?=$e->id?>" >
				<td ><?=$key+1;?></td>
				<td>
					<?php if($e->show_client != 'Yes'){ ?>
					<a href="<?=base_url()?>expenses/show/<?=$e->id?>" data-toggle="tooltip" data-title="<?=lang('show_to_client')?>" data-placement="right">
						<i class="fa fa-circle-o text-danger"></i>
					</a>
					<?php } ?>
					<?php if($e->receipt != NULL){ ?>
					<a href="<?=base_url()?>assets/uploads/<?=$e->receipt?>" target="_blank" data-toggle="tooltip" data-title="<?=$e->receipt?>" data-placement="right">
						<i class="fa fa-paperclip"></i>
					</a>
					<?php } ?>
					<?=($p != NULL) ? $p->project_title : 'N/A'; ?>
				</td>
				<td class="col-currency">
					<strong>
					<?php
					$cur = ($p != NULL) ? $p->currency : 'USD'; 
					$cur = ($e->client > 0) ? Client::client_currency($e->client)->code : $cur;
					?>
					<?php if($e->currency_symbol != ''){
								echo $e->currency_symbol. ''.$e->amount;
						} else{ 
							echo Applib::format_currency($cur, $e->amount); } ?>
					</strong>
				</td>
				<?php if(User::is_admin()) {
					$user_details = $this->db->get_where('account_details',array('user_id'=>$e->added_by))->row_array(); 
				 ?> 
				<td><a class="text-info" href="<?php echo base_url()?>expenses/show_expense/<?=$e->added_by?>"><?=$user_details['fullname']?></a></td>
				<?php } ?>
				<td>
					<?php echo App::get_category_by_id($e->category); ?>
				</td>
				<th style="width:5px; display:none;"><?php echo date('m/d/Y',strtotime($e->expense_date)); ?></th>
				<td>
					<?=strftime(config_item('date_format'), strtotime($e->expense_date))?>
				</td>
				<td>
				<?php 
					$approved_status = 'Pending';
					$label_color = 'warning';
					if(isset($e->expense_status)&&!empty($e->expense_status))
					{
						if($e->expense_status==1)
						{
							$approved_status = 'Approved';
							$label_color = 'success';
						}
						if($e->expense_status==2)
						{
							$approved_status = 'Rejected';
							$label_color = 'danger';
						}
					}					 
					?>
					<span class="small label label-<?=$label_color?>">
						<?=$approved_status;?>
					</span>
				</td>
				<?php if(User::is_admin()) { ?> 
				<!--<td>-->
				<!--<button class="btn btn-success" data-toggle="tooltip" title="<?=lang('accept_expense')?>" onclick="accept_expense(<?=$e->id?>)" ><i class="fa fa-check"></i></button>-->
				<!--<button class="btn btn-danger" data-toggle="tooltip" title="<?=lang('decline_expense')?>" onclick="decline_expense(<?=$e->id?>)" ><i class="fa fa-times"></i></button>				-->
				<!--</td>-->
				<?php } ?>
				<td class="text-right">
					<div class="dropdown">
						<a data-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i class="fa fa-ellipsis-v"></i></a>
						<ul class="dropdown-menu pull-right">
							<li>
								<a href="javascript:void(0)" onclick="accept_expense(<?=$e->id?>)" title="<?=lang('accept_expense')?>"><?=lang('approve')?></a>
							</li>
							<li>
								<a href="javascript:void(0)" onclick="decline_expense(<?=$e->id?>)"><?=lang('reject')?></a>
							</li>
							<li>
								<a href="<?=base_url()?>expenses/view/<?=$e->id?>"><?=lang('view_expense')?></a>
							</li>     
							<?php if(User::is_admin() || User::perm_allowed(User::get_id(),'edit_expenses')) { ?>  
							<li>
								<a href="<?=base_url()?>expenses/edit/<?=$e->id?>" data-toggle="ajaxModal">
									<?=lang('edit_expense')?>
								</a>
							</li>
								<?php } if(User::is_admin() || User::perm_allowed(User::get_id(),'delete_expenses')) { ?> 
							<li>
								<a href="<?=base_url()?>expenses/delete/<?=$e->id?>" data-toggle="ajaxModal">
									<?=lang('delete_expense')?>
								</a>
							</li>
							<?php } ?>
						</ul>
					</div>
				</td>
			</tr>
			<?php  } } else { ?>
				<td  colspan="8">No Records Found</td>
			<?php }?>
			</tbody>
		</table>
		</div>
		</div>
	</div>
<?php  } ?>

<?php if (($this->tank_auth->user_role($this->tank_auth->get_role_id()) != 'superadmin') && ($this->tank_auth->user_role($this->tank_auth->get_role_id()) != 'admin')) { 

	?>

	<?php 
	$check_teamlead = $this->db->get_where('dgt_users',array('id'=>$this->session->userdata('user_id')))->row_array(); 
	if($check_teamlead['is_teamlead'] == 'yes'){
	?>
	<ul class="nav nav-tabs nav-tabs-solid m-b-30">
	    <li class="choosetype personal" data-type="personal"><a href="javascript:void(0)">Personal Expenses</a></li>                       
	  	<li class="choosetype team active" data-type="team"><a href="javascript:void(0)" >Team Expenses</a></li>
 	</ul>
	
	<?php } ?>
<div class="row" id="team_expense">
	<div class="col-md-12">
	<div class="table-responsive">
	
				   <?php 
				   
				   // print_r($check_teamlead); exit;
				   if($check_teamlead['is_teamlead'] == 'no')
				   {
					$expenses = $this->db->query("SELECT * FROM `dgt_expenses` where added_by =".$this->tank_auth->get_user_id()." order by id  ASC ")->result();
				}
				if($check_teamlead['is_teamlead'] == 'yes')
				   {
					$expenses = $this->db->query("SELECT * FROM `dgt_expenses` where FIND_IN_SET(".$this->session->userdata('user_id').", expense_approvers) order by id  ASC ")->result();
				}
					
				   ?>
		<table id="table-expenses" class="table table-striped custom-table m-b-0">
			<thead>
				<tr>
					<th style="width:5px;">No</th>
					<th class=""><?=lang('project')?></th>
					<th class="col-currency"><?=lang('amount')?></th>
					<?php if($check_teamlead['is_teamlead'] == 'yes') {?> 
					<th class=""><?=lang('staff_name')?></th>					
					<?php } ?> 
					<th class=""><?=lang('category')?></th>
					<th style="width:5px; display:none;"></th>
					<th class=""><?=lang('expense_date')?></th>
					<th class=""><?=lang('status')?></th>
					<?php if(User::is_admin()) { ?> 
					<!--<th class=""><?=lang('expense_action')?></th>					-->
					<?php } ?> 
					<th class="text-right">Action</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($expenses)){

			foreach ($expenses as $key => $e) { 
			if($e->project != '' || $e->project != 'NULL'){
			$p = Project::by_id($e->project);
			}else{ $p = NULL; } ?>
			<tr id="row_id_<?=$e->id?>" >
				<td ><?=$key+1?></td>
				<td>
					<?php if($e->show_client != 'Yes'){ ?>
					<a href="<?=base_url()?>expenses/show/<?=$e->id?>" data-toggle="tooltip" data-title="<?=lang('show_to_client')?>" data-placement="right">
						<i class="fa fa-circle-o text-danger"></i>
					</a>
					<?php } ?>
					<?php if($e->receipt != NULL){ ?>
					<a href="<?=base_url()?>assets/uploads/<?=$e->receipt?>" target="_blank" data-toggle="tooltip" data-title="<?=$e->receipt?>" data-placement="right">
						<i class="fa fa-paperclip"></i>
					</a>
					<?php } ?>
					<?=($p != NULL) ? $p->project_title : 'N/A'; ?>
				</td>
				<td class="col-currency">
					<strong>
					<?php
					$cur = ($p != NULL) ? $p->currency : $e->currency_symbol; 
					$cur = ($e->client > 0) ? Client::client_currency($e->client)->code : $cur;
					?>
					

						<?php if($e->currency_symbol != ''){
								echo $e->currency_symbol. ''.$e->amount;
						} else{ 
							echo Applib::format_currency($cur, $e->amount); } ?>
						
					
					</strong>
				</td>
				<?php if($check_teamlead['is_teamlead'] == 'yes') { $user_details = $this->db->get_where('account_details',array('user_id'=>$e->added_by))->row_array(); ?>
								<td><a class="text-info" href="<?php echo base_url()?>expenses/show_expense/<?=$e->added_by?>"><?=$user_details['fullname']?></a></td> <?php } ?>

				<td>
					<?php echo App::get_category_by_id($e->category); ?>
				</td>
				<th style="width:5px; display:none;"><?php echo date('m/d/Y',strtotime($e->expense_date)); ?></th>
				<td>
					<?=strftime(config_item('date_format'), strtotime($e->expense_date))?>
				</td>
				<td>
				<?php 
					
					$label_color = 'warning';
					if($check_teamlead['is_teamlead'] == 'no')
				   {
				   	$approved_status = 'Pending';
						if(isset($e->expense_status)&&!empty($e->expense_status))
						{
							if($e->expense_status==1)
							{
								$approved_status = 'Approved';
								$label_color = 'success';
							}
							if($e->expense_status==2)
							{
								$approved_status = 'Rejected';
								$label_color = 'danger';
							}
						}
					}	

					if($check_teamlead['is_teamlead'] == 'yes')
				   {
				   	$approved_status = 'Requested';
						$expense_approvers_status   = $this->db->get_where('dgt_expense_approvers',array('approvers'=>$this->session->userdata('user_id'),'expense'=>$e->id))->row_array();
						if(isset($expense_approvers_status)&&!empty($expense_approvers_status))
						{
							if($expense_approvers_status['status']==1)
							{
								$approved_status = 'Approved';
								$label_color = 'success';
							}
							if($expense_approvers_status['status']==2)
							{
								$approved_status = 'Rejected';
								$label_color = 'danger';
							}
						}	
					}				 
					?>
					<span class="small label label-<?=$label_color?>">
						<?=$approved_status;?>
					</span>
				</td>
				<?php if(User::is_admin()) { ?> 
				<!--<td>-->
				<!--<button class="btn btn-success" data-toggle="tooltip" title="<?=lang('accept_expense')?>" onclick="accept_expense(<?=$e->id?>)" ><i class="fa fa-check"></i></button>-->
				<!--<button class="btn btn-danger" data-toggle="tooltip" title="<?=lang('decline_expense')?>" onclick="decline_expense(<?=$e->id?>)" ><i class="fa fa-times"></i></button>				-->
				<!--</td>-->
				<?php } ?>
				<td class="text-right">
					<div class="dropdown">
						<a data-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i class="fa fa-ellipsis-v"></i></a>
						<ul class="dropdown-menu pull-right">
							<?php if($check_teamlead['is_teamlead'] == 'yes')
				   { ?> 
							<li>
								<a href="javascript:void(0)" onclick="accept_expense(<?=$e->id?>)" title="<?=lang('accept_expense')?>"><?=lang('approve')?></a>
							</li>
							<li>
								<a href="javascript:void(0)" onclick="decline_expense(<?=$e->id?>)"><?=lang('reject')?></a>
							</li>
						<?php } ?>
							<li>
								<a href="<?=base_url()?>expenses/view/<?=$e->id?>"><?=lang('view_expense')?></a>
							</li>     
							<?php if(User::is_admin() || User::perm_allowed(User::get_id(),'edit_expenses')) { ?>  
							<li>
								<a href="<?=base_url()?>expenses/edit/<?=$e->id?>" data-toggle="ajaxModal">
									<?=lang('edit_expense')?>
								</a>
							</li>
								<?php } if(User::is_admin() || User::perm_allowed(User::get_id(),'delete_expenses')) { ?> 
							<li>
								<a href="<?=base_url()?>expenses/delete/<?=$e->id?>" data-toggle="ajaxModal">
									<?=lang('delete_expense')?>
								</a>
							</li>
							<?php } ?>
						</ul>
					</div>
				</td>
			</tr>
			<?php  }
		} else { ?>
			<td colspan="8"> No Records Found</td>
		<?php } ?>
			</tbody>
		</table>
		</div>
		</div>
	</div>
<?php } ?>

<div class="row" id="personal_expense" style="display: none;">
	<div class="col-md-12">
	<div class="table-responsive">

				   <?php 
				   
				   // print_r($check_teamlead); exit;
				   
					$expenses = $this->db->query("SELECT * FROM `dgt_expenses` where added_by =".$this->tank_auth->get_user_id()." order by id  ASC ")->result();
				
					
				   ?>
		<table id="table-expenses" class="table table-striped custom-table m-b-0">
			<thead>
				<tr>
					<th style="width:5px;">No</th>
					<th class=""><?=lang('project')?></th>
					<th class="col-currency"><?=lang('amount')?></th>
					<?php if(User::is_admin()) { ?> 
					<th class=""><?=lang('staff_name')?></th>					
					<?php } ?> 
					<th class=""><?=lang('category')?></th>
					<th style="width:5px; display:none;"></th>
					<th class=""><?=lang('expense_date')?></th>
					<th class=""><?=lang('status')?></th>
					<?php if(User::is_admin()) { ?> 
					<!--<th class=""><?=lang('expense_action')?></th>					-->
					<?php } ?> 
					<th class="text-right">Action</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($expenses)){
				foreach ($expenses as $key => $e) { 
			if($e->project != '' || $e->project != 'NULL'){
			$p = Project::by_id($e->project);
			}else{ $p = NULL; } ?>
			<tr id="row_id_<?=$e->id?>" >
				<td><?=$key +1;?></td>
				<td>
					<?php if($e->show_client != 'Yes'){ ?>
					<a href="<?=base_url()?>expenses/show/<?=$e->id?>" data-toggle="tooltip" data-title="<?=lang('show_to_client')?>" data-placement="right">
						<i class="fa fa-circle-o text-danger"></i>
					</a>
					<?php } ?>
					<?php if($e->receipt != NULL){ ?>
					<a href="<?=base_url()?>assets/uploads/<?=$e->receipt?>" target="_blank" data-toggle="tooltip" data-title="<?=$e->receipt?>" data-placement="right">
						<i class="fa fa-paperclip"></i>
					</a>
					<?php } ?>
					<?=($p != NULL) ? $p->project_title : 'N/A'; ?>
				</td>
				<td class="col-currency">
					<strong>
					<?php
					$cur = ($p != NULL) ? $p->currency : 'USD'; 
					$cur = ($e->client > 0) ? Client::client_currency($e->client)->code : $cur;
					?>
					<?php if($e->currency_symbol != ''){
								echo $e->currency_symbol. ''.$e->amount;
						} else{ 
							echo Applib::format_currency($cur, $e->amount); } ?>
					</strong>
				</td>
				<?php if(User::is_admin()) { ?> 
				<td>
					<?php // echo ($e->client > 0) ? Client::view_by_id($e->client)->company_name : 'N/A'; ?>
					<?php					 
						$user_id = isset($e->added_by)?$e->added_by:'';					 
						$user_details = User::profile_info($user_id);
						$user_name = isset($user_details->fullname)?$user_details->fullname:'No Name'; 
						echo $user_name;
					?>
				</td>	
				<?php } ?>
				<td>
					<?php echo App::get_category_by_id($e->category); ?>
				</td>
				<th style="width:5px; display:none;"><?php echo date('m/d/Y',strtotime($e->expense_date)); ?></th>
				<td>
					<?=strftime(config_item('date_format'), strtotime($e->expense_date))?>
				</td>
				<td>
				<?php 
					$approved_status = 'Pending';
					$label_color = 'warning';
					if(isset($e->expense_status)&&!empty($e->expense_status))
					{
						if($e->expense_status==1)
						{
							$approved_status = 'Approved';
							$label_color = 'success';
						}
						if($e->expense_status==2)
						{
							$approved_status = 'Rejected';
							$label_color = 'danger';
						}
					}					 
					?>
					<span class="small label label-<?=$label_color?>">
						<?=$approved_status;?>
					</span>
				</td>
				<?php if(User::is_admin()) { ?> 
				<!--<td>-->
				<!--<button class="btn btn-success" data-toggle="tooltip" title="<?=lang('accept_expense')?>" onclick="accept_expense(<?=$e->id?>)" ><i class="fa fa-check"></i></button>-->
				<!--<button class="btn btn-danger" data-toggle="tooltip" title="<?=lang('decline_expense')?>" onclick="decline_expense(<?=$e->id?>)" ><i class="fa fa-times"></i></button>				-->
				<!--</td>-->
				<?php } ?>
				<td class="text-right">
					<div class="dropdown">
						<a data-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i class="fa fa-ellipsis-v"></i></a>
						<ul class="dropdown-menu pull-right">
							<!-- <li>
								<a href="javascript:void(0)" onclick="accept_expense(<?=$e->id?>)" title="<?=lang('accept_expense')?>"><?=lang('approve')?></a>
							</li> -->
							<!-- <li>
								<a href="javascript:void(0)" onclick="decline_expense(<?=$e->id?>)"><?=lang('reject')?></a>
							</li> -->
							<li>
								<a href="<?=base_url()?>expenses/view/<?=$e->id?>"><?=lang('view_expense')?></a>
							</li>     
							<!-- <?php if(User::is_admin() || User::perm_allowed(User::get_id(),'edit_expenses')) { ?>  
							<li>
								<a href="<?=base_url()?>expenses/edit/<?=$e->id?>" data-toggle="ajaxModal">
									<?=lang('edit_expense')?>
								</a>
							</li>
								<?php } if(User::is_admin() || User::perm_allowed(User::get_id(),'delete_expenses')) { ?> 
							<li>
								<a href="<?=base_url()?>expenses/delete/<?=$e->id?>" data-toggle="ajaxModal">
									<?=lang('delete_expense')?>
								</a>
							</li> -->
							<!-- <?php } ?> -->
						</ul>
					</div>
				</td>
			</tr>
			<?php  } } else { ?>
				<td colspan="8">No Records Found</td>

			<?php } ?>
			</tbody>
		</table>
		</div>
		</div>
	</div>
</div>