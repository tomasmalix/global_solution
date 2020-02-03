<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include APPPATH.'/libraries/Requests.php';
class Settings extends MX_Controller {

    function __construct()
    {
        parent::__construct();
        User::logged_in();
        if(!User::is_admin()) redirect('');

        $this -> invoice_logo_height = 0;
        $this -> invoice_logo_width = 0;
        $this->load->library(array('form_validation'));

        Requests::register_autoloader();
        $this->auth_key = config_item('api_key'); // Set our API KEY

        $this->load->module('layouts');
        $this->load->config('rest');
        $this->load->library('template');
        $this->template->title(lang('settings').' - '.config_item('company_name'));

        $this->load->model(array('Setting','Project','App'));

        $this->general_setting = '?settings=general';
        $this->invoice_setting = '?settings=invoice';
        $this->estimate_setting = '?settings=estimate';
        $this->system_setting = '?settings=system';
        $this->theme_setting = '?settings=theme';
        $this->email_setting = '?settings=email';
        $this->language_files = array(
            'fx_lang.php' => './application/',
            'tank_auth_lang.php' => './application/',
            'calendar_lang.php' => './system/',
            'date_lang.php' => './system/',
            'db_lang.php' => './system/',
            'email_lang.php' => './system/',
            'form_validation_lang.php' => './system/',
            'ftp_lang.php' => './system/',
            'imglib_lang.php' => './system/',
            'migration_lang.php' => './system/',
            'number_lang.php' => './system/',
            'profiler_lang.php' => './system/',
            'unit_test_lang.php' => './system/',
            'upload_lang.php' => './system/',
        );

        $this->applib->set_locale();
    }

    function index()
    {
        $settings = $this->input->get('settings', TRUE)?$this->input->get('settings', TRUE):'general';
        $data['page'] = lang('settings');
        $data['form'] = TRUE;
        $data['editor'] = TRUE;
        $data['fuelux'] = TRUE;
        $data['datatables'] = TRUE;
        $data['nouislider'] = TRUE;
        $data['postmark_config'] = TRUE;
        $data['translations'] = $this->applib->translations();
        $data['available'] = $this->available_translations();
        $data['languages'] = App::languages();
        $data['load_setting'] = $settings;
        $data['locale_name'] = App::get_locale()->name;

        if ($settings == 'system') {
            $data['countries'] = App::countries();
            $data['locales'] = App::locales();
            $data['currencies'] = App::currencies();
            $data['timezones'] = Setting::timezones();
        }
        if ($settings == 'menu') {
            $data['iconpicker'] = TRUE;
            $data['sortable'] = TRUE;
            $data['admin'] = $this->db->where('hook','main_menu_admin')-> where('parent','')-> where('access',1)-> order_by('order','ASC')->get('hooks')->result();   
             
            $data['client'] = $this->db->where('hook','main_menu_client')-> where('parent','')-> where('access',2)-> order_by('order','ASC')->get('hooks')->result();
            $data['staff'] = $this->db->where('hook','main_menu_staff')-> where('parent','')-> where('access',3)-> order_by('order','ASC')->get('hooks')->result();
        }
        if ($settings == 'crons') {
            $data['crons'] = $this->db->where('hook','cron_job_admin')-> where('access',1)-> order_by('name','ASC')->get('hooks')->result();
        }
        if($settings == 'general') {
            $data['countries'] = App::countries();
        }
        if($settings == 'setting_salary') {
            $data['salary_setting'] = App::salary_setting();

        }
        if ($settings == 'theme') {
            $data['iconpicker'] = TRUE;
        }
        if ($settings == 'translations') {
            $action = $this->uri->segment(3);
            $data['translation_stats'] = $this->Setting->translation_stats($this->language_files);
            if ($action == 'view') {
                $data['language'] = $this->uri->segment(4);
                $data['language_files'] = $this ->language_files;
            }
            if ($action == 'edit') {
                $language = $this->uri->segment(4);
                $file = $this->uri->segment(5);
                $path = $this->language_files[$file.'_lang.php'];
                $data['language'] = $language;
                $data['english'] = $this->lang->load($file, 'english', TRUE, TRUE, $path);
                if ($language == 'english') {
                    $data['translation'] = $data['english'];
                } else {
                    $data['translation'] = $this->lang->load($file, $language, TRUE, TRUE);
                }
                $data['language_file'] = $file;
            }
        }
        $this->template
            ->set_layout('users')
            ->build('settings',isset($data) ? $data : NULL);
    }

    function vE(){
        Settings::_vP();
    }

    function templates(){
        if ($_POST) {
            Applib::is_demo();
            $group = $this->input->post('email_group',TRUE);
            $data = array('subject' => $this->input->post('subject'),
                'template_body' => $this->input->post('email_template'),
            );
            Setting::update_template($group,$data);

            $return_url = $_POST['return_url'];

            $this->session->set_flashdata('response_status', 'success');
            $this->session->set_flashdata('message', lang('settings_updated_successfully'));
            redirect($return_url);
        }else{
            $this->index();
        }
    }

    function customize(){
        $this->load->helper('file');
        if($_POST){
            $data = $_POST['css-area'];
            if(write_file('./assets/css/style.css', $data)){
                $this->session->set_flashdata('response_status', 'success');
                $this->session->set_flashdata('message', lang('settings_updated_successfully'));
                redirect('settings/?settings=customize');
            }else{
                $this->session->set_flashdata('response_status', 'error');
                $this->session->set_flashdata('message', lang('operation_failed'));
                redirect('settings/?settings=customize');
            }
        }else{
            $this->index();
        }
    }

    function add_currency(){
        if ($_POST) {
            if($this->db->where('code',$this->input->post('code'))->get('currencies')->num_rows() == '0'){
                App::save_data('currencies',$this->input->post());
                $this->session->set_flashdata('response_status', 'success');
                $this->session->set_flashdata('message', lang('currency_added_successfully'));
                redirect($_SERVER['HTTP_REFERER']);
            }else{
                $this->session->set_flashdata('response_status', 'error');
                $this->session->set_flashdata('message', lang('currency_code_exists'));
                redirect($_SERVER['HTTP_REFERER']);
            }

        }else{

            $this->load->view('modal_add_currency',isset($data) ? $data : NULL);
        }
    }

    function xrates(){
        if ($_POST) {
            $this->db->where('config_key','xrates_app_id')->update('config', array('value' => $this->input->post('xrates_app_id')));
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $this->load->view('modal_open_xrates',isset($data) ? $data : NULL);
        }
    }




    function edit_currency($code = NULL){
        if ($_POST) {
            $prev_code = $this->input->post('oldcode');
            unset($_POST['oldcode']);
            $this->db->where('code',$prev_code)->update('currencies',$this->input->post());
            $this->session->set_flashdata('response_status', 'success');
            $this->session->set_flashdata('message', lang('currency_updated_successfully'));
            redirect($_SERVER['HTTP_REFERER']);

        }else{
            $data['code'] = $code;
            $this->load->view('modal_edit_currency',isset($data) ? $data : NULL);
        }
    }

    function add_category(){
        if ($_POST) {
            if($this->db->where('cat_name',$this->input->post('cat_name'))->get('categories')->num_rows() == '0'){
                App::save_data('categories',$this->input->post());

                $this->session->set_flashdata('response_status', 'success');
                $this->session->set_flashdata('message', lang('category_added_successfully'));
                redirect($_SERVER['HTTP_REFERER']);
            }else{
                $this->session->set_flashdata('response_status', 'error');
                $this->session->set_flashdata('message', lang('category_exists'));
                redirect($_SERVER['HTTP_REFERER']);
            }

        }else{

            $this->load->view('modal_add_category',isset($data) ? $data : NULL);
        }
    }

    function edit_category($id = NULL){
        if ($_POST) {
            $id = $this->input->post('id');
            switch ($this->input->post('delete_cat')) {
                case 'on':
                    $this->db->where('id',$id)->delete('categories');
                    $this->session->set_flashdata('response_status', 'success');
                    $this->session->set_flashdata('message', lang('operation_successful'));
                    break;

                default:
                    unset($_POST['delete_cat']);
                    $this -> db -> where('id',$id)->update('categories',$this->input->post());
                    $this->session->set_flashdata('response_status', 'success');
                    $this->session->set_flashdata('message', lang('category_updated_successfully'));

                    break;
            }
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $data['cat'] = $id;
            $this->load->view('modal_edit_category',isset($data) ? $data : NULL);
        }
    }

    function _vP(){
        Applib::pData();
        $data = array('value' => 'TRUE');
        Applib::update('config',array('config_key' => 'valid_license'),$data);
        // Applib::make_flashdata(array(
        //         'response_status' => 'success',
        //         'message' => 'Software validated successfully')
        // );
        $this->session->set_flashdata('tokbox_success', 'Software validated successfully');
        redirect($_SERVER['HTTP_REFERER']);
    }

    function departments(){
        if ($_POST) {
            $settings = $_POST['settings'];
            unset($_POST['settings']);
            App::save_data('departments',$this->input->post());

            // $this->session->set_flashdata('response_status', 'success');
            // $this->session->set_flashdata('message', lang('department_added_successfully'));
            $this->session->set_flashdata('tokbox_success', lang('department_added_successfully'));
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $this->index();
        }
    }

        function designations(){
        if ($_POST) {
            $settings = $_POST['settings'];
            unset($_POST['settings']);
            App::save_data('designation',$this->input->post());

            // $this->session->set_flashdata('response_status', 'success');
            // $this->session->set_flashdata('message', 'Designation Added successfully... ');
            $this->session->set_flashdata('tokbox_success', 'Designation Added successfully');
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $this->index();
        }
    }

    function add_custom_field(){
        if ($_POST) {
            if (isset($_POST['targetdept'])) {
                // select department and redirect to creating field
                // Applib::go_to('settings/?settings=fields&dept='.$_POST['targetdept'],'success','Department selected');
                $this->session->set_flashdata('tokbox_success', 'Department Selected');
                redirect('settings/?settings=fields&dept='.$_POST['targetdept']);
            }else{
                $_POST['uniqid'] = $this->_GenerateUniqueFieldValue();
                App::save_data('fields',$this->input->post());

                $this->session->set_flashdata('tokbox_success', 'Custom field added Successfully');
                redirect('settings/?settings=fields&dept='.$_POST['deptid']);
                // Applib::go_to('settings/?settings=fields&dept='.$_POST['deptid'],'success','Custom field added');
                // Insert to database additional fields

            }

        }else{

        }
    }

    function edit_custom_field($field = NULL){
        if ($_POST) {
            if(isset($_POST['delete_field']) AND $_POST['delete_field'] == 'on'){
                $this->db->where('id',$_POST['id'])->delete('fields');
                $this->session->set_flashdata('tokbox_error', lang('custom_field_deleted'));
                redirect($_SERVER['HTTP_REFERER']);
                // Applib::go_to($_SERVER['HTTP_REFERER'],'success',lang('custom_field_deleted'));
            }else{
                $this->db->where('id',$_POST['id'])->update('fields',$this->input->post());
                // Applib::go_to($_SERVER['HTTP_REFERER'],'success',lang('custom_field_updated'));
                $this->session->set_flashdata('tokbox_success', lang('custom_field_updated'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        }else{
            $data['field_info'] = $this->db->where(array('id'=>$field))->get('fields')->result();
            $this->load->view('fields/modal_edit_field',isset($data) ? $data : NULL);
        }
    }



    function edit_dept($deptid = NULL){
        if ($_POST) {
            if(isset($_POST['delete_dept']) AND $_POST['delete_dept'] == 'on'){
                $this->db->where('deptid',$_POST['deptid']) -> delete('departments');
                // Applib::go_to($_SERVER['HTTP_REFERER'],'success',lang('department_deleted'));
                $this->session->set_flashdata('tokbox_error', lang('department_deleted'));
                redirect($_SERVER['HTTP_REFERER']);
            }else{
                $this->db->where('deptid',$_POST['deptid']) -> update('departments',$this->input->post());
                // Applib::go_to($_SERVER['HTTP_REFERER'],'success',lang('department_updated'));
                $this->session->set_flashdata('tokbox_success', lang('department_updated'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        }else{
            $data['deptid'] = $deptid;
            $data['dept_info'] = $this->db ->where(array('deptid'=>$deptid)) -> get('departments') -> result();
            $this->load->view('modal_edit_department',isset($data) ? $data : NULL);
        }
    }


    function translations(){

        $action = $this->uri->segment(3);

        if ($_POST) {
            if ($action == 'save')
            {
                $jpost = array();
                $jsondata = json_decode(html_entity_decode($_POST['json']));
                foreach($jsondata as $jdata) {
                    $jpost[$jdata->name] = $jdata->value;
                }
                $jpost['_path'] = $this->language_files[$jpost['_file'].'_lang.php'];
                $data['json'] = $this->Setting->save_translation($jpost);
                $this->load->view('json',isset($data) ? $data : NULL);
                return;
            }
            if ($action == 'active')
            {
                $language = $this->uri->segment(4);
                return $this->db->where('name',$language)->update('languages',$this->input->post());
            }
        }else{
            if ($action == 'add')
            {
                $language = $this->uri->segment(4);
                $this->Setting->add_translation($language, $this->language_files);
                // $this->session->set_flashdata('response_status', 'success');
                // $this->session->set_flashdata('message', lang('translation_added_successfully'));
                $this->session->set_flashdata('tokbox_success', lang('translation_added_successfully'));
                redirect($_SERVER['HTTP_REFERER']);
            }
            if ($action == 'backup')
            {
                $language = $this->uri->segment(4);
                return $this->Setting->backup_translation($language, $this->language_files);
            }
            if ($action == 'restore')
            {
                $language = $this->uri->segment(4);
                return $this->Setting->restore_translation($language, $this->language_files);
            }
            if ($action == 'submit')
            {
                $language = $this->uri->segment(4);
                $path = "./application/language/".$language."/".$language."-backup.json";
                if (!file_exists($path)) {
                    $this->Setting->backup_translation($language, $this->language_files);
                }
                $params['recipient'] = 'dreamguystech@gmail.com';
                $params['subject'] = 'User submitted translation: '.ucwords(str_replace("_"," ", $language));
                $params['message'] = 'The .json language file is attached';
                $params['attached_file'] = $path;
                return modules::run('fomailer/send_email',$params);
            }
            $this->index();
        }
    }

    function available_translations()
    {
        $available = array();
        $ex =  App::languages();
        foreach ($ex as $e) { $existing[] = $e->name; }
        $ln = $this->db->group_by('language')->get('locales')->result();
        foreach ($ln as $l) { if (!in_array($l->language, $existing)) { $available[] = $l; } }
        return $available;

    }

    function update(){
        // print($_POST); exit;
        if ($_POST) {
            Applib::is_demo();
            switch ($_POST['settings'])
            {
                case 'general':
                    $this->_update_general_settings($this->general_setting);
                    break;
                case 'email':
                    $this->_update_email_settings();
                    break;
                case 'payments':
                    $this->_update_payment_settings();
                    break;
                case 'setting_salary':
                    $this->_update_salary_settings();
                    break;
                case 'system':
                    $this->_update_system_settings('system');
                    break;
                case 'theme':
                    if(file_exists($_FILES['iconfile']['tmp_name']) || is_uploaded_file($_FILES['iconfile']['tmp_name'])) {
                        $this->upload_favicon($this->input->post());
                    }
                    if(file_exists($_FILES['appleicon']['tmp_name']) || is_uploaded_file($_FILES['appleicon']['tmp_name'])) {
                        $this->upload_appleicon($this->input->post());
                    }
                    if(file_exists($_FILES['logofile']['tmp_name']) || is_uploaded_file($_FILES['logofile']['tmp_name'])) {
                        $this->upload_logo($this->input->post());
                    }
                    $this->_update_theme_settings('theme');
                    break;
                case 'estimate':
                    $this->_update_estimate_settings('estimate');
                    break;
                case 'crons':
                    $this->_update_cron_settings();
                    break;
                case 'invoice':
                    if(file_exists($_FILES['invoicelogo']['tmp_name']) || is_uploaded_file($_FILES['invoicelogo']['tmp_name'])) {
                        $this->upload_invoice_logo($this->input->post());
                    }
                    $this->_update_invoice_settings('invoice');
                    break;
            }

        }else{
            $this->index();
        }
    }

    function _update_general_settings($setting){
        Applib::is_demo();

        $this->form_validation->set_rules('company_name', 'Company Name', 'required');
        $this->form_validation->set_rules('company_address', 'Company Address', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            // $this->session->set_flashdata('response_status', 'error');
            // $this->session->set_flashdata('message', lang('settings_update_failed'));
            $this->session->set_flashdata('tokbox_error', lang('settings_update_failed'));
            redirect('settings/'.$this->general_setting);
        }else{
            foreach ($_POST as $key => $value) {
                $data = array('value' => $value);
                $this->db->where('config_key', $key)->update('config', $data);
                $exists = $this->db->where('config_key', $key)->get('config');
                if ($exists->num_rows() == 0) {
                    $this->db->insert('config',array("config_key"=>$key, "value"=>$value));
                }
            }
            // $this->session->set_flashdata('response_status', 'success');
            // $this->session->set_flashdata('message', lang('settings_updated_successfully'));
            $this->session->set_flashdata('tokbox_success', 'General Settings Update Successfully');
            redirect('settings/'.$this->general_setting);
        }

    }

    function _update_cron_settings(){
        Applib::is_demo();

        $this->form_validation->set_rules('cron_key', 'Cron Key', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata('tokbox_error', lang('settings_update_failed'));
            // $this->session->set_flashdata('response_status', 'error');
            // $this->session->set_flashdata('message', lang('settings_update_failed'));
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $this->load->library('encrypt');
            $_POST['mail_password'] = $this->encrypt->encode($this->input->post('mail_password'));

            foreach ($_POST as $key => $value) {
                if(strtolower($value) == 'on') {
                    $value = 'TRUE';
                } elseif(strtolower($value) == 'off') {
                    $value = 'FALSE';
                }
                $data = array('value' => $value);

                $this->db->where('config_key', $key)->update('config', $data);
            }
            // $this->session->set_flashdata('response_status', 'success');
            // $this->session->set_flashdata('message', lang('settings_updated_successfully'));
            $this->session->set_flashdata('tokbox_success', 'Cron Settings Update Successfully');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function project_settings(){
        
        $data = array('value' => json_encode($_POST));
        $this->db->where('config_key', 'default_project_settings')->update('config', $data);
        // $this->session->set_flashdata('response_status', 'success');
        // $this->session->set_flashdata('message', lang('settings_updated_successfully'));
        $this->session->set_flashdata('tokbox_success', 'Project Settings Update Successfully');
        redirect($_SERVER['HTTP_REFERER']);
    }

    function slack_conf(){
        foreach ($_POST as $key => $value) {
            if(strtolower($value) == 'on') {
                $value = 'TRUE';
            } elseif(strtolower($value) == 'off') {
                $value = 'FALSE';
            }
            $data = array('value' => $value);
            $this->db->where('config_key', $key)->update('config', $data);
        }
        $this->session->set_flashdata('tokbox_success', 'Slack Configuration Update Successfully');
        // $this->session->set_flashdata('response_status', 'success');
        // $this->session->set_flashdata('message', lang('settings_updated_successfully'));
        redirect($_SERVER['HTTP_REFERER']);
    }

    function _update_system_settings($setting){
        Applib::is_demo();

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<span style="color:red">', '</span><br>');
        $this->form_validation->set_rules('file_max_size', 'File Max Size', 'required');
        if ($this->form_validation->run() == FALSE)
        {
        $this->session->set_flashdata('tokbox_error', validation_errors());
            // $this->session->set_flashdata('response_status', 'error');
            // $this->session->set_flashdata('message', lang('settings_update_failed'));
            $this->session->set_flashdata('form_error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            foreach ($_POST as $key => $value) {
                if(strtolower($value) == 'on') {
                    $value = 'TRUE';
                } elseif(strtolower($value) == 'off') {
                    $value = 'FALSE';
                }
                $data = array('value' => $value);
                $this->db->where('config_key', $key)->update('config', $data);
            }

            //Set date format for date picker
            switch($_POST['date_format']) {
                case "%d-%m-%Y": $picker = "dd-mm-yyyy"; $phptime = "d-m-Y"; break;
                case "%m-%d-%Y": $picker = "mm-dd-yyyy"; $phptime = "m-d-Y"; break;
                case "%Y-%m-%d": $picker = "yyyy-mm-dd"; $phptime = "Y-m-d"; break;
                case "%d.%m.%Y": $picker = "dd.mm.yyyy"; $phptime = "d.m.Y"; break;
                case "%m.%d.%Y": $picker = "mm.dd.yyyy"; $phptime = "m.d.Y"; break;
                case "%Y.%m.%d": $picker = "yyyy.mm.dd"; $phptime = "Y.m.d"; break;
            }
            $this->db->where('config_key', 'date_picker_format')->update('config', array("value" => $picker));
            $this->db->where('config_key', 'date_php_format')->update('config', array("value" => $phptime));

            $this->session->set_flashdata('tokbox_success', 'System Settings Update Successfully');
            // $this->session->set_flashdata('response_status', 'success');
            // $this->session->set_flashdata('message', lang('settings_updated_successfully'));
            redirect('settings/'.$this->system_setting);
        }

    }

    function _update_theme_settings($setting){
        Applib::is_demo();
        foreach ($_POST as $key => $value) {
            if(strtolower($value) == 'on') {
                $value = 'TRUE';
            } elseif(strtolower($value) == 'off') {
                $value = 'FALSE';
            }
            $this->db->where('config_key', $key)->update('config', array('value' => $value));
        }
        $this->session->set_flashdata('tokbox_success', 'Theme Settings Update Successfully');
        // $this->session->set_flashdata('response_status', 'success');
        // $this->session->set_flashdata('message', lang('settings_updated_successfully'));
        redirect('settings/'.$this->theme_setting);
    }

    function _update_invoice_settings($setting){
        Applib::is_demo();

        $this->form_validation->set_rules('invoice_color', 'Invoice Color', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            // $this->session->set_flashdata('response_status', 'error');
            // $this->session->set_flashdata('message', lang('settings_update_failed'));
            $this->session->set_flashdata('tokbox_error', lang('settings_update_failed'));
            redirect('settings/'.$this->invoice_setting);
        }else{
            foreach ($_POST as $key => $value) {
                if(strtolower($value) == 'on') {
                    $value = 'TRUE';
                } elseif(strtolower($value) == 'off') {
                    $value = 'FALSE';
                }
                if ($key == 'invoice_logo_height' && $this->invoice_logo_height > 0) { $value = $this->invoice_logo_height; }
                if ($key == 'invoice_logo_width' && $this->invoice_logo_width > 0) { $value = $this->invoice_logo_width; }
                $data = array('value' => $value);
                $this->db->where('config_key', $key)->update('config', $data);
            }
            $this->session->set_flashdata('tokbox_success', 'Invoice Settings Update Successfully');
            // $this->session->set_flashdata('response_status', 'success');
            // $this->session->set_flashdata('message', lang('settings_updated_successfully'));
            redirect('settings/'.$this->invoice_setting);
        }

    }

    function _update_estimate_settings($setting){
        Applib::is_demo();

        $this->form_validation->set_rules('estimate_color', 'Estimate Color', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            // $this->session->set_flashdata('response_status', 'error');
            // $this->session->set_flashdata('message', lang('settings_update_failed'));
            $this->session->set_flashdata('tokbox_error', lang('settings_update_failed'));
            redirect('settings/'.$this->estimate_setting);
        }else{

            foreach ($_POST as $key => $value) {
                if(strtolower($value) == 'on') {
                    $value = 'TRUE';
                } elseif(strtolower($value) == 'off') {
                    $value = 'FALSE';
                }

                $data = array('value' => $value);
                $this->db->where('config_key', $key)->update('config', $data);
            }
            // $this->session->set_flashdata('response_status', 'success');
            // $this->session->set_flashdata('message', lang('settings_updated_successfully'));
            $this->session->set_flashdata('tokbox_success', 'Estimate Settings Update Successfully');
            redirect('settings/'.$this->estimate_setting);
        }

    }

    function _update_email_settings(){
        Applib::is_demo();

        $this->load->library('form_validation');
        $this->load->library('encrypt');
        $this->form_validation->set_error_delimiters('<span style="color:red">', '</span><br>');
        $this->form_validation->set_rules('settings', 'Settings', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            // $this->session->set_flashdata('response_status', 'error');
            // $this->session->set_flashdata('form_error', validation_errors());
            // $this->session->set_flashdata('message', lang('settings_update_failed'));
            // $this->session->set_flashdata('tokbox_error', lang('settings_update_failed'));
            $this->session->set_flashdata('tokbox_error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }else{

            foreach ($_POST as $key => $value) {
                if(strtolower($value) == 'on') {
                    $value = 'TRUE';
                } elseif(strtolower($value) == 'off') {
                    $value = 'FALSE';
                }

                $data = array('value' => $value);
                App::update('config',array('config_key'=>$key),$data);
            }
            if (isset($_POST['smtp_pass']))
            {
                $raw_smtp_pass =  $this->input->post('smtp_pass');
                $smtp_pass = $this->encrypt->encode($raw_smtp_pass);
                $data = array('value' => $smtp_pass);
                App::update('config',array('config_key' => 'smtp_pass'),$data);
            }

            if (isset($_POST['mail_password']))
            {
                $raw_mail_pass =  $this->input->post('mail_password');
                $mail_pass = $this->encrypt->encode($raw_mail_pass);
                $data = array('value' => $mail_pass);
                App::update('config',array('config_key' => 'mail_password'),$data);
            }


            $this->session->set_flashdata('tokbox_success', 'Email Settings Update Successfully');
            // $this->session->set_flashdata('response_status', 'success');
            // $this->session->set_flashdata('message', lang('settings_updated_successfully'));
            redirect($_SERVER['HTTP_REFERER']);
        }

    }
    function _update_salary_settings(){

        if ($this->input->post()) {
            Applib::is_demo();


            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<span style="color:red">', '</span><br>');
            $this->form_validation->set_rules('salary_da', 'Salary DA', 'required');
            $this->form_validation->set_rules('salary_hra', 'Salary HRA', 'required');
            if ($this->form_validation->run() == FALSE)
            {
                // $this->session->set_flashdata('response_status', 'error');
                // $this->session->set_flashdata('form_error', validation_errors());
                // $this->session->set_flashdata('message', lang('settings_update_failed'));
            $this->session->set_flashdata('tokbox_error', validation_errors());
                redirect($_SERVER['HTTP_REFERER']);
            }else{
                
                unset($_POST['settings']);
                
                foreach ($_POST as $key => $value) {
                    
                    $data = array('config_key' => $key);
                               $this->db->where($data); 
                      $count = $this->db->count_all_results('config');
                      
                    if($count == 0){
                        $data['value'] = $value;
                        $this->db->insert('config', $data);
                    }else{
                        $data['value'] = $value;
                        $this->db->where('config_key', $key)->update('config', $data);
                    }
                }


                // $this->session->set_flashdata('response_status', 'success');
                // $this->session->set_flashdata('message', lang('settings_updated_successfully'));
                $this->session->set_flashdata('tokbox_success', 'Salary Settings Update Successfully');
                redirect('payroll/settings');
            }
        }else{
            // $this->session->set_flashdata('response_status', 'error');
            // $this->session->set_flashdata('message', lang('settings_update_failed'));
            $this->session->set_flashdata('tokbox_error', lang('settings_update_failed'));
            redirect('payroll/settings');
        }

       
    }
    function _update_payment_settings(){
        if ($this->input->post()) {
            Applib::is_demo();


            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<span style="color:red">', '</span><br>');
            $this->form_validation->set_rules('paypal_email', 'Paypal Email', 'required');
            if ($this->form_validation->run() == FALSE)
            {
                // $this->session->set_flashdata('response_status', 'error');
                // $this->session->set_flashdata('form_error', validation_errors());
                // $this->session->set_flashdata('message', lang('settings_update_failed'));
            $this->session->set_flashdata('tokbox_error', lang('settings_update_failed'));
                redirect($_SERVER['HTTP_REFERER']);
            }else{

                foreach ($_POST as $key => $value) {
                    if(strtolower($value) == 'on') {
                        $value = 'TRUE';
                    } elseif(strtolower($value) == 'off') {
                        $value = 'FALSE';
                    }

                    $data = array('value' => $value);
                    $this->db->where('config_key', $key)->update('config', $data);
                }


                // $this->session->set_flashdata('response_status', 'success');
                // $this->session->set_flashdata('message', lang('settings_updated_successfully'));
                $this->session->set_flashdata('tokbox_success', 'Payment Settings Update Successfully');
                redirect('settings/?settings=payments');
            }
        }else{
            // $this->session->set_flashdata('response_status', 'error');
            // $this->session->set_flashdata('message', lang('settings_update_failed'));
            $this->session->set_flashdata('tokbox_error', lang('settings_update_failed'));
            redirect('settings/?settings=payments');
        }

    }

    function update_email_templates(){
        if ($this->input->post()) {
            Applib::is_demo();

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<span style="color:red">', '</span><br>');
            $this->form_validation->set_rules('email_estimate_message', 'Estimate Message', 'required');
            $this->form_validation->set_rules('email_invoice_message', 'Invoice Message', 'required');
            $this->form_validation->set_rules('reminder_message', 'Reminder Message', 'required');
            if ($this->form_validation->run() == FALSE)
            {
                // $this->session->set_flashdata('response_status', 'error');
                // $this->session->set_flashdata('message', lang('settings_update_failed'));
            $this->session->set_flashdata('tokbox_error', lang('settings_update_failed'));
                $_POST = '';
                $this->update('email');
            }else{
                foreach ($_POST as $key => $value) {
                    $data = array('value' => $value);
                    $this->db->where('config_key', $key)->update('config', $data);
                }

                // $this->session->set_flashdata('response_status', 'success');
                // $this->session->set_flashdata('message', lang('settings_updated_successfully'));
                $this->session->set_flashdata('tokbox_success', 'Email Template Update Successfully');
                redirect('settings/update/email');
            }
        }else{
            // $this->session->set_flashdata('response_status', 'error');
            // $this->session->set_flashdata('message', lang('settings_update_failed'));
            $this->session->set_flashdata('tokbox_error', lang('settings_update_failed'));
            redirect('settings/update/email');
        }

    }

    function upload_favicon($files){
        Applib::is_demo();

        if ($files) {
            $config['upload_path']   = './assets/images/';
            $config['allowed_types'] = 'jpg|jpeg|png|ico';
            $config['max_width']  = '300';
            $config['max_height']  = '300';
            $config['overwrite']  = TRUE;
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('iconfile'))
            {
                $data = $this->upload->data();
                $file_name = $data['file_name'];
                $data = array('value' => $file_name);
                $this->db->where('config_key', 'site_favicon')->update('config', $data);
                return TRUE;
            }else{
                // $this->session->set_flashdata('response_status', 'error');
                // $this->session->set_flashdata('message', lang('logo_upload_error'));
                $this->session->set_flashdata('tokbox_error', lang('logo_upload_error'));
                redirect('settings/'.$this->theme_setting);
            }
        }else{
            return FALSE;
        }
    }

    function upload_appleicon($files){
        Applib::is_demo();

        if ($files) {
            $config['upload_path']   = './assets/images/';
            $config['allowed_types'] = 'jpg|jpeg|png|ico';
            $config['overwrite']  = TRUE;
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('appleicon'))
            {
                $data = $this->upload->data();
                $file_name = $data['file_name'];
                $data = array('value' => $file_name);
                $this->db->where('config_key', 'site_appleicon')->update('config', $data);
                return TRUE;
            }else{
                // $this->session->set_flashdata('response_status', 'error');
                // $this->session->set_flashdata('message', lang('logo_upload_error'));
                $this->session->set_flashdata('tokbox_error', lang('logo_upload_error'));
                redirect('settings/'.$this->theme_setting);
            }
        }else{
            return FALSE;
        }
    }

    function upload_logo($files){
        Applib::is_demo();

        if ($files) {
            $config['upload_path']   = './assets/images/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['remove_spaces'] = TRUE;

            $config['overwrite']  = TRUE;
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logofile'))
            {
                $filedata = $this->upload->data();
                $file_name = $filedata['file_name'];
                $data = array('value' => $file_name);
                $this->db->where('config_key', 'company_logo')->update('config', $data);
                // $this->session->set_flashdata('response_status', 'success');
                // $this->session->set_flashdata('message', lang('file_uploaded_successfully'));
                $this->session->set_flashdata('tokbox_success', lang('file_uploaded_successfully'));
                redirect('settings/'.$this->theme_setting);
                return TRUE;
            }else{
                // $this->session->set_flashdata('response_status', 'error');
                // $this->session->set_flashdata('message', lang('logo_upload_error'));
                $this->session->set_flashdata('tokbox_error', lang('logo_upload_error'));
                redirect('settings/'.$this->theme_setting);
            }
        }else{
            return FALSE;
        }
    }
    function upload_invoice_logo($files){
        Applib::is_demo();

        if ($files) {
            $config['upload_path']   = './assets/images/logos/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_width']  = '800';
            $config['max_height']  = '300';
            $config['remove_spaces'] = TRUE;
            $config['overwrite']  = TRUE;
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('invoicelogo'))
            {
                $filedata = $this->upload->data();
                $file_name = $filedata['file_name'];
                $size = getimagesize ('./assets/images/logos/'.$file_name);
                $ratio = $size[1] / $size[0];
                $height = 60;
                if ($size[1] < $height) { $height = $size[1]; }
                $width = intval($height / $ratio);
                $this->invoice_logo_height = $height;
                $this->invoice_logo_width = $width;
                $this->db->where('config_key', 'invoice_logo')->update('config', array('value' => $file_name));
                return TRUE;
            }else{
                // $this->session->set_flashdata('response_status', 'error');
                // $this->session->set_flashdata('message', lang('logo_upload_error'));
                $this->session->set_flashdata('tokbox_error', lang('logo_upload_error'));
                redirect('settings/'.$this->invoice_setting);
            }
        }else{
            return FALSE;
        }
    }


    function _GenerateUniqueFieldValue()
    {
        $uniqid = uniqid('f');
        // Id should start with an character other than digit

        $this->db->where('uniqid', $uniqid)->get('fields');

        if ($this->db->affected_rows() > 0)
        {
            $this->GetUniqueFieldValue();
            // Recursion
        }
        else
        {
            return $uniqid;
        }

    }

    function database()
    {
        Applib::is_demo();
        $this->load->helper('file');
        $this->load->dbutil();
        $prefs = array(
            'format'      => 'zip',             // gzip, zip, txt
            'filename'    => 'database-full-backup_'.date('Y-m-d').'.zip',
            'add_drop'    => TRUE,              // Whether to add DROP TABLE statements to backup file
            'add_insert'  => TRUE,              // Whether to add INSERT data to backup file
            'newline'     => "\n"               // Newline character used in backup file
        );
        $backup =& $this->dbutil->backup($prefs);

        if ( ! write_file('./assets/backup/database-full-backup_'.date('Y-m-d').'.zip', $backup))
        {
            // $this->session->set_flashdata('response_status', 'error');
            // $this->session->set_flashdata('message', 'The assets/backup folder is not writable.');
            $this->session->set_flashdata('tokbox_error', 'The assets/backup folder is not writable.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->load->helper('download');
        force_download('database-full-backup_'.date('Y-m-d').'.zip', $backup);


    }

    function hook($action, $item)
    {
        switch ($action) {
            case "visible":
                $role = $this->input->post('access');
                $visible = $this->input->post('visible');
                return $this->db->where('module',$item)->where('access',$role)->update('hooks', array('visible' => $visible));
            case "enabled":
                $role = $this->input->post('access');
                $enabled = $this->input->post('enabled');
                return $this->db->where('module',$item)->where('access',$role)->update('hooks', array('enabled' => $enabled));
            case "icon":
                $role = $this->input->post('access');
                $icon = $this->input->post('icon');
                return $this->db->where('module',$item)->where('access',$role)->update('hooks', array('icon' => $icon));
            case "reorder":
                $items = $this->input->post('json', TRUE);
                $items = json_decode($items);
                foreach ($items[0] as $i => $mod) {
                    $this->db->where('module',$mod->module)->where('access',$mod->access)->update('hooks', array('order' => $i+1));
                }
                return TRUE;
        }
        return false;
    }
    function leave_types(){
        if ($this->input->post()) {
            
            $tbl_id               = $this->input->post('leave_type_tbl_id');
            $det['leave_type']    = $this->input->post('leave_type'); 
            $det['leave_days']    = $this->input->post('leave_days'); 
            if($tbl_id == ''){
                $this->db->insert('dgt_leave_types',$det);
                $this->session->set_flashdata('tokbox_success', 'Leave Type Added Successfully');
            }else{ 
                $this->db->update('dgt_leave_types',$det,array('id'=>$tbl_id));
            $this->session->set_flashdata('tokbox_success', 'Leave Type Update Successfully');
            } 
            redirect('settings/?settings=leaves');
        }
    }
    function delete_leave_types(){
        if ($this->input->post()) {
            $det['status']   = 1;
            $this->db->update('dgt_leave_types',$det,array('id'=>$this->input->post('leave_type_id'))); 
            $this->session->set_flashdata('tokbox_error', 'Leave Type Deleted Successfully');
            redirect('settings/?settings=leaves');
        }else{
            $data['leave_type_id'] = $this->uri->segment(3);
            $this->load->view('modal/delete_leave_type',$data);
        } 
    }
    
    
    function lead_reporter_add()
    {
        if($_POST){
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<span style="color:red">', '</span><br>');
            $this->form_validation->set_rules('reporter_name', 'Reporter Name', 'required');
            $this->form_validation->set_rules('reporter_email', 'Reporter Email', 'required');
            if ($this->form_validation->run() == FALSE)
            {
                // $this->session->set_flashdata('response_status', 'error');
                // $this->session->set_flashdata('form_error', validation_errors());
                // $this->session->set_flashdata('message', lang('settings_update_failed'));
                $this->session->set_flashdata('tokbox_error', validation_errors());
                redirect($_SERVER['HTTP_REFERER']);
            }else{
                $res = array(
                    'reporter_name' => $this->input->post('reporter_name'),
                    'reporter_email' => $this->input->post('reporter_email')
                );
                $this->db->insert('dgt_lead_reporter',$res);
                // $this->session->set_flashdata('lead_reporter_add', 'success');
                $this->session->set_flashdata('tokbox_success', 'Lead Reporter Added Successfully');
                redirect('settings/?settings=lead_reporter');
            }
        }else{
            $this->load->view('modal/add_reporter');
        }
    }

    function lead_reporter_edit($r_id)
    {
        if($_POST){
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<span style="color:red">', '</span><br>');
            $this->form_validation->set_rules('reporter_name', 'Reporter Name', 'required');
            $this->form_validation->set_rules('reporter_email', 'Reporter Email', 'required');
            if ($this->form_validation->run() == FALSE)
            {
                // $this->session->set_flashdata('response_status', 'error');
                // $this->session->set_flashdata('form_error', validation_errors());
                // $this->session->set_flashdata('message', lang('settings_update_failed'));
                $this->session->set_flashdata('tokbox_error', validation_errors());
                redirect($_SERVER['HTTP_REFERER']);
            }else{
                $res = array(
                    'reporter_name' => $this->input->post('reporter_name'),
                    'reporter_email' => $this->input->post('reporter_email')
                );
                $this->db->where('reporter_id',$r_id);
                $this->db->update('dgt_lead_reporter',$res);
                // $this->session->set_flashdata('lead_reporter_edit', 'success');
                $this->session->set_flashdata('tokbox_success', 'Lead Reporter Updated Successfully');
                redirect('settings/?settings=lead_reporter');
            }
        }else{

        $data['reporter_details'] = $this->Setting->GetReporterById($r_id);
        // echo "<pre>"; print_r($reporter_details); exit;
        $this->load->view('modal/edit_reporter',$data);
    }
    }

    function lead_reporter_delete($r_id)
    {
        if($_POST){
            $this->db->where('reporter_id',$r_id);
            $this->db->delete('dgt_lead_reporter');
                $this->session->set_flashdata('tokbox_error', 'Lead Reporter Deleted Successfully');
            // $this->session->set_flashdata('lead_reporter_delete', 'success');
            redirect('settings/?settings=lead_reporter');
        }else{
            $data['r_id'] = $r_id;
            $this->load->view('modal/delete_reporter',$data);
        }
    }

    public function check_reporter_mail(){
        $user_email = $this->input->post('user_email');
        $check_email_exist = $this->db->get_where('dgt_users',array('email'=>$user_email))->num_rows();
        if($check_email_exist == 0)
        {
            echo "new";
            exit;
        }else{
            echo "exists";
            exit;
        }
    }

    function new_role()
    {
        $this->load->view('modal/new_role');
    }

    function tokbox_settings()
    {
        $this->db->where('config_key', 'apikey_tokbox')->update('config', array('value' => $this->input->post('apikey_tokbox')));
        $this->db->where('config_key', 'apisecret_tokbox')->update('config', array('value' => $this->input->post('apisecret_tokbox')));
        $this->session->set_flashdata('tokbox_success', 'TokBox Settings Update Successfully');
        redirect('settings/?settings=tokbox_settings');
    }


   

}

/* End of file settings.php */