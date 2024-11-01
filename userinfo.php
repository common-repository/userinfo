<?php
/**
*Plugin Name: UserInfo
*Description: Save User information.
*Author: Rahul Ranjan
*Author URI: http://appzbit.com
*Plugin Uri: http://iglobsyn.com
*Tags: wp, custom form, employee record, shortcode
*Requires at least: 2.3
*Tested up to: 4.8.2
*Stable tag: 1.0
*Version: 1.0.0
**/

/**** start adding scripts  ****/

function ui_emp_front_scripts() {
  wp_enqueue_style( 'bootstrap-min-css', plugin_dir_url(__FILE__).'css/bootstrap.min.css', array(), null, 'all' );
  wp_enqueue_style( 'jquery-dataTables-min-css', plugin_dir_url(__FILE__).'css/jquery.dataTables.min.css', array(), null, 'all' );
  wp_enqueue_script( 'bootstrap-jquery', plugin_dir_url(__FILE__) .'js/bootstrap.min.js' , array('jquery'));
  wp_enqueue_script( 'datatable-jquery', plugin_dir_url(__FILE__) .'js/jquery.dataTables.min.js' , array('jquery'));
  wp_enqueue_script( 'jquery-validate', plugin_dir_url(__FILE__) .'js/jquery.validate.min.js' , array('jquery'));
}
add_action('admin_init', 'ui_emp_front_scripts' );
/**** End adding scripts  ****/

/*  start code for add menu */
add_action('admin_menu','ui_user_menu_control');

function ui_user_menu_control(){
  
  $page_title = 'Employee Info';
  $menu_title = 'Employee Info';
  $capability = 'manage_options';
  $menu_slug = 'employee_info';
  $function = 'ui_emp_information_func';
  $icon_url = 'dashicons-groups';

  add_menu_page($page_title,$menu_title,$capability,$menu_slug,$function,$icon_url);
  add_submenu_page($menu_slug,'Employee List','Employee List','manage_options','emp_list','ui_emp_list_function');
  add_submenu_page($menu_slug,'Add Employee','Add Employee','manage_options','add_emp','ui_add_emp_info_function');
}
/*  End code for add menu */

/*define menu function here*/
function ui_emp_information_func(){
  echo "<div class='wrap'>";
  echo "<h1>Welcome to Employee information Plugin.</h1>";
  echo "<hr>";
  echo "</div>";
}

function ui_emp_list_function(){
  echo "<script>
  jQuery(document).ready(function(){
      jQuery('#empdata').DataTable();
  });
  </script>";
  global $wpdb; 

  $allemps = $wpdb->get_results('select * from ui_employee');
  $tbldata = "";

  $table_name = 'ui_employee'; // do not forget about tables prefix
  $default = array(
        'id' => 0,
        'name' => '',
        'email' => '',
        'contact' => '',
        'city' => ''
    );

  echo "<div class='wrap'>";
  echo "<h1>Employee List here...</h1>";
  echo "<hr>";

  ?>
  <table id='empdata'>
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Contact</th>
        <th>City</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
    <?php $m = '1'; foreach($allemps as $emp){ ?>
      <tr>
          <td><?php echo $m; ?></td>
          <td><?php echo $emp->name; ?></td>
          <td><?php echo $emp->email; ?></td>
          <td><?php echo $emp->contact; ?></td>
          <td><?php echo $emp->city; ?></td>
          <td><?php echo ($emp->status == '1') ? "Active" : "InActive"; ?></td>
      </tr>
    <?php $m++; } ?>
  </tbody>
  </table>
  <?php
  echo "</div>";
}
add_shortcode('form_call','ui_add_emp_info_function');

function ui_add_emp_info_function(){
  global $wpdb;
  echo "<style>
  .error{
    color:red;
  }
  input.form-control.error {
    border-color: #F00;
}
  </style>";
  $form = '<div class="container"><div class="col-lg-6">
<form name="createpost" id="createpost" method="POST" action="">
  <div class="form-group">
    <label for="ename">Name:</label>
    <input type="text" id="ename" name="ename" class="form-control">
  </div>
  <div class="form-group">
    <label for="eemail">Email:</label>
    <input type="text" id="eemail" name="eemail" class="form-control">
  </div>
  <div class="form-group">
    <label for="econtact">Contact:</label>
    <input type="text" id="econtact" name="econtact" class="form-control">
  </div>
  <div class="form-group">
    <label for="estreet">Street:</label>
    <input type="text" id="estreet" name="estreet" class="form-control">
  </div>
  <div class="form-group">
    <label for="ecity">City:</label>
    <input type="text" id="ecity" name="ecity" class="form-control">
  </div>
  <div class="form-group">
    <label for="estate">State:</label>
    <input type="text" id="estate" name="estate" class="form-control">
  </div>
  <div class="form-group">
    <label for="ezip">Pincode:</label>
    <input type="text" id="ezip" name="ezip" class="form-control">
  </div>
  <div class="form-group">
    <label for="ecountry">Country:</label>
    <input type="text" id="ecountry" name="ecountry" class="form-control">
  </div>
  <div class="form-group">
    <label for="estatus">Status:</label>
    <select name="estatus" id="estatus" class="form-control">
      <option value="">Select Status</option>
      <option value="1">Active</option>
      <option value="2">InActive</option>
    </select>
  </div>
  <input type="hidden" name="ui-emp-ajax-nonce" id="ui-emp-ajax-nonce" value="' . wp_create_nonce( 'ui-emp-ajax-nonce' ) . '" />
  <input type="submit" id="postform" name="submit" class="btn btn-primary" value="Post">
</form>
        </div></div>
';
  echo "<div class='wrap'>";
  echo "<h1>Add Employee Information</h1>";
  echo "<div id='mssd'></div>";
  echo "<hr>";
  echo "<div style='color:red;border:0px 1px red;'>".$mssg."</div>";
  echo $form;
  echo "</div>";

  echo "<script>
      jQuery(document).ready(function(){
       /* var old_var = {code: '200', msg: 'Employee Added Successfully!!'};
        var parsedData = JSON.parse(old_var);
        console.log(parsedData);*/
          jQuery('#createpost').validate({
            rules:{
              ename: 'required',
              eemail: {
                required: true,
                email: true
              },
              econtact: 'required',
              estreet: 'required',
              ecity: 'required',
              estate: 'required',
              ezip: 'required',
              ecountry: 'required',
              estatus: 'required'
            },
            messages:{
              ename: 'Please enter Employee Name.',
              eemail: {
                required: 'Please enter Employee Email.',
                email: 'Please enter valid Email.'
              },
              econtact: 'Please enter Contact Number.',
              estreet: 'Please enter Street address.',
              ecity: 'Please enter City.',
              estate: 'Please enter State.',
              ezip: 'Please enter Zip Code.',
              Country: 'Please enter Country.',
              estatus: 'Please Select Status.'
            },
            submitHandler: function(form){
              var formdatas = jQuery('#createpost').serialize();

              formdatas = 'action=ui_myajaxcall&'+formdatas;
              jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                cache: false,
                data: formdatas,
                success: function(res){
                  if(res.code == '200'){
                    jQuery('#mssd').html(res.msg);
                    jQuery('#mssd').css('color','green');
                    jQuery('#createpost')[0].reset();
                  }else if(res.code == '301'){
                    jQuery('#mssd').html(res.msg);
                    jQuery('#mssd').css('color','red');
                  }
                  return false;
                }
              });
            }
          });
      });
  </script>"; 
}

function ui_myajaxcall(){
  global $wpdb;
  
  if(isset($_POST['submit']) && ($_POST['submit'] == "Post"))
  {
    check_ajax_referer('ui-emp-ajax-nonce','ui-emp-ajax-nonce');
    $resp_arr = [];
    $res = [];
    $name = (!empty($_POST['ename']))? sanitize_text_field($_POST['ename']) : "";
    $email = (!empty($_POST['eemail'])) ? sanitize_email($_POST['eemail']) : ""; 
    $contact = (!empty($_POST['econtact'])) ? sanitize_text_field($_POST['econtact']) : "";
    $street = ($_POST['estreet'] != "") ? sanitize_text_field($_POST['estreet']) : "";
    $city = ($_POST['ecity'] != "") ? sanitize_text_field($_POST['ecity']) : "";
    $state = ($_POST['estate'] != "") ? sanitize_text_field($_POST['estate']) : "";
    $zip = ($_POST['ezip'] != "") ? sanitize_text_field($_POST['ezip']) : "";
    $country = ($_POST['ecountry'] != "") ? sanitize_text_field($_POST['ecountry']) : "";
    $status = ($_POST['estatus'] != "") ? sanitize_text_field($_POST['estatus']) : "";

    

    if($name == ""){
          $res['code'] = "301";
          $res['msg'] = "Please Enter Employee Name";
          $resp_arr = $res;
          echo json_encode($resp_arr);
          exit; 
      }elseif($email == ""){
          $res['code'] = "301";
          $res['msg'] = "Please Enter Employee Email";
          $resp_arr = $res;
          echo json_encode($resp_arr);
          exit; 
      }elseif($contact == ""){
          $res['code'] = "301";
          $res['msg'] = "Please Enter Employee Contact";
          $resp_arr = $res;
          echo json_encode($resp_arr);
          exit;
      }elseif(!is_numeric($contact)){
          $res['code'] = "301";
          $res['msg'] = "Please Enter Employee valid Contact";
          $resp_arr = $res;
          echo json_encode($resp_arr);
          exit;
      }elseif($street == ""){
          $res['code'] = "301";
          $res['msg'] = "Please Enter Employee Street Address.";
          $resp_arr = $res;
          echo json_encode($resp_arr);
          exit;
      }elseif($city == ""){
          $res['code'] = "301";
          $res['msg'] = "Please Enter Employee City";
          $resp_arr = $res;
          echo json_encode($resp_arr);
          exit;
      }elseif($state == ""){
          $res['code'] = "301";
          $res['msg'] = "Please Enter Employee state";
          $resp_arr = $res;
          echo json_encode($resp_arr);
          exit;
      }elseif($zip == ""){
          $res['code'] = "301";
          $res['msg'] = "Please Enter mployee ZipCode";
          $resp_arr = $res;
          echo json_encode($resp_arr);
          exit;
      }elseif(!is_numeric($zip)){
          $res['code'] = "301";
          $res['msg'] = "Please Enter Employee valid ZipCode";
          $resp_arr = $res;
          echo json_encode($resp_arr);
          exit;
      }elseif($country == ""){
          $res['code'] = "301";
          $res['msg'] = "Please Enter Employee Country";
          $resp_arr = $res;
          echo json_encode($resp_arr);
          exit;
      }elseif($status == ""){
          $res['code'] = "301";
          $res['msg'] = "Please Enter Employee status";
          $resp_arr = $res;
          echo json_encode($resp_arr);
          exit;
      }else{
           $insertdata = $wpdb->insert('ui_employee',array(
            'name'=>$name,
            'email'=>$email,
            'contact'=>$contact,
            'street'=>$street,
            'city'=>$city,
            'state'=>$state,
            'pincode'=>$zip,
            'country'=>$country,
            'status'=>$status,
            'created_on'=>date("Y-m-d H:i:s")
        ),
        array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s',
            '%d',
            '%d'
        ));
        $insid = $wpdb->insert_id;
        if($insid){
          $res['code'] = "200";
          $res['msg'] = "Employee Added Successfully!!";
        }else{
          $res['code'] = "301";
          $res['msg'] = "Error while Adding Emplyee Information.";
        }
        $resp_arr = $res;
      }
      echo json_encode($resp_arr);
      exit;
  }
}
add_action( 'wp_ajax_ui_myajaxcall', 'ui_myajaxcall' );
add_action( 'wp_ajax_nopriv_ui_myajaxcall', 'ui_myajaxcall' );

global $ui_jal_db_version;
$ui_jal_db_version = '1.0';

function ui_emp_create_tbl(){
  global $wpdb;
  global $ui_jal_db_version;

  $table_name = 'ui_employee';
  
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
     `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `email` VARCHAR(255) NOT NULL , `contact` VARCHAR(255) NOT NULL , `street` VARCHAR(255) NOT NULL , `city` VARCHAR(255) NOT NULL , `state` VARCHAR(255) NOT NULL , `pincode` INT(10) NOT NULL , `country` VARCHAR(255) NOT NULL , `status` INT(1) NOT NULL , `created_on` TIMESTAMP NOT NULL , `updated_on` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );

  add_option( 'ui_jal_db_version', $ui_jal_db_version );
}

register_activation_hook(__FILE__ , 'ui_emp_create_tbl');

?>