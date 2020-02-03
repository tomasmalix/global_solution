<?php
$l = Lead::find($lead);
$cur = Lead::currency($l->co_id);
?>
<div class="header-fixed clearfix">
	<div class="row">
		<div class="col-xs-6">
			<h3 class="page-title m-b-0"><?=$l->company_name?> <span class="label label-default"><?=$l->company_ref?></span></h3>
		</div>
		<div class="col-xs-6">
			<a href="<?=base_url()?>leads/delete/<?=$l->co_id?>" class="btn btn-danger btn-sm pull-right" data-toggle="ajaxModal" title="<?=lang('delete')?>">
				<i class="fa fa-trash-o"></i>
			</a>
			<a href="<?=base_url()?>leads/update/<?=$l->co_id?>" class="btn btn-success btn-sm pull-right" data-toggle="ajaxModal" title="<?=lang('edit')?>" data-placement="left">
				<i class="fa fa-edit"></i> <?=lang('edit')?>
			</a>
			<a href="<?=base_url()?>leads/convert/<?=$l->co_id?>" class="btn btn-warning btn-sm pull-right" data-toggle="ajaxModal" title="<?=lang('convert')?>" data-placement="left">
				<i class="fa fa-paper-plane"></i> <?=lang('convert')?>
			</a>
		</div>
	</div>
</div>
<div class="content">
	<div class="sub-tab m-b-0">
		<ul class="nav nav-tabs nav-tabs-bottom bg-white">
			<li class="<?=($tab == 'dashboard') ? 'active' : '';?>">
				<a href="<?=base_url()?>leads/view/<?=$l->co_id?>/dashboard"><?=lang('overview')?></a>
			</li>
			<li class="<?=($tab == 'contacts') ? 'active' : '';?>">
				<a href="<?=base_url()?>leads/view/<?=$l->co_id?>/contacts"><?=lang('contacts')?></a>
			</li>
			<li class="<?=($tab == 'tasks') ? 'active' : '';?>">
				<a href="<?=base_url()?>leads/view/<?=$l->co_id?>/tasks"><?=lang('tasks')?></a>
			</li>
			<li class="<?=($tab == 'files') ? 'active' : '';?>">
				<a href="<?=base_url()?>leads/view/<?=$l->co_id?>/files"><?=lang('files')?></a>
			</li>
			<li class="<?=($tab == 'comments') ? 'active' : '';?>">
				<?php $count = $this->db->where(array('client_id'=>$l->co_id,'unread'=>1))->get('comments')->num_rows();?>
				<a href="<?=base_url()?>leads/view/<?=$l->co_id?>/comments">
					<?=lang('comments')?>
					<?=($count > 0) ? '<label class="label bg-success">'.$count.'</label>' : '';?>
				</a>
			</li>
		</ul>
	</div>
	<div class="leads-content">
		<?php $data = array('l' => $l,'cur' => $cur); ?>
		<?php $this->view('tab/view_'.$tab, $data); ?>
	</div>
</div>