<?php
$login = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'class'	=> 'form-control',
	'value' => set_value('login'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
if (config_item('use_username', 'tank_auth')) {
	$login_label = 'Email or login';
} else {
	$login_label = 'Email';
}
echo modules::run('sidebar/flash_msg');
?>  
<div class="account-content">
	<div class="container">
	
		<!-- Account Logo -->
		<div class="account-logo">
			<?php $display = config_item('logo_or_icon'); ?>
			<?php if ($display == 'logo' || $display == 'logo_title') { ?>
			<img src="<?=base_url()?>assets/images/<?=config_item('login_logo')?>" class="<?=($display == 'logo' ? "" : "login-logo")?>">
			<?php } ?>
		</div>
		<!-- /Account Logo -->
		
		<div class="account-box">
			<h3 class="account-title"><?=lang('forgot_password')?></h3>
			<p class="account-subtitle">Enter your email to get a password reset link</p>
			
			<!-- Account Form -->
			<?php $attributes = array('class' => ''); echo form_open($this->uri->uri_string(),$attributes); ?>
				<div class="form-group">
					<label class="control-label"><?=lang('email')?> or <?=lang('username')?></label>
					<?php echo form_input($login); ?>
					<span class="error"><?php echo form_error($login['name']); ?><?php echo isset($errors[$login['name']])?$errors[$login['name']]:''; ?></span>
				</div>
				<div class="form-group">
					<button type="submit" class="btn account-btn"><?=lang('get_new_password')?></button>
				</div>
				<div class="account-footer">
					<?php if (config_item('allow_client_registration') == 'TRUE'){ ?>
					<p>Don't have an account yet? <a href="<?=base_url()?>auth/register/"><?=lang('get_your_account')?></a></p>
					<?php } ?>
					<p><?=lang('already_have_an_account')?> <a href="<?=base_url()?>login"><?=lang('sign_in')?></a></p> 
				</div>
			<?php echo form_close(); ?>
			<!-- /Account Form -->
			
		</div>
	</div>
</div>