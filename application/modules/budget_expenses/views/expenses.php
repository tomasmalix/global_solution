<div class="content">
	<div class="row">
		<div class="col-sm-4 col-xs-3">
			<h4 class="page-title"><?=lang('expenses')?></h4>
		</div>
		<div class="col-sm-8 col-xs-9 text-right m-b-0">
			<?php if(User::is_admin() || User::perm_allowed(User::get_id(),'add_expenses')) { ?> 
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
	<div class="row">
	<div class="col-md-12">
	<div class="table-responsive">
		<table id="table-expenses" class="table table-striped custom-table m-b-0">
			<thead>
				<tr>
					<th style="width:5px; display:none;"></th>
					<th class=""><?=lang('project')?></th>
					<th class="col-currency"><?=lang('amount')?></th>
					<th class=""><?=lang('client')?></th>
					<th class=""><?=lang('invoiced')?></th>
					<th class=""><?=lang('category')?></th>
					<th style="width:5px; display:none;"></th>
					<th class=""><?=lang('expense_date')?></th>
					<th class="text-right">Action</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($expenses as $key => $e) { 
			if($e->project != '' || $e->project != 'NULL'){
			$p = Project::by_id($e->project);
			}else{ $p = NULL; } ?>
			<tr>
				<td style="display:none;"><?=$e->id?></td>
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
					<?=Applib::format_currency($cur, $e->amount)?>
					</strong>
				</td>
				<td>
					<?php echo ($e->client > 0) ? Client::view_by_id($e->client)->company_name : 'N/A'; ?>
				</td>
				<td>
					<span class="small label label-<?=($e->invoiced == '0') ? 'danger' : 'success';?>">
						<?=($e->invoiced == '0') ? 'No' :'Yes'; ?>
					</span>
				</td>
				<td>
					<?php echo App::get_category_by_id($e->category); ?>
				</td>
				<th style="width:5px; display:none;"><?php echo date('m/d/Y',strtotime($e->expense_date)); ?></th>
				<td>
					<?=strftime(config_item('date_format'), strtotime($e->expense_date))?>
				</td>
				<td class="text-right">
					<div class="dropdown">
						<a data-toggle="dropdown" class="action-icon dropdown-toggle" href="#"><i class="fa fa-ellipsis-v"></i></a>
						<ul class="dropdown-menu pull-right">
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
			<?php  } ?>
			</tbody>
		</table>
		</div>
		</div>
	</div>
</div>
