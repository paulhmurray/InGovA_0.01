<?php
   /* 
    Plugin Name: email subscription popup
    Plugin URI:http://www.i13websolution.com/wordpress-pro-plugins/wordpress-newsletter-subscription-pro-plugin.html
    Author URI:http://www.i13websolution.com/
    Description: This is beautiful email subscription modal popup plugin for wordpress.Each time new user visit your site user will see modal popup for email subscription.Even you can setup email subscription form by widget.
    Author:I Thirteen Web Solution
    Version:1.2.5
    Text Domain:email-subscription-popup
    Domain Path: /languages
    */
    
    add_action('admin_menu', 'email_subscription_popup_admin_menu');
    //add_action( 'admin_init', 'email_subscription_popup_admin_admin_init' );
    register_activation_hook(__FILE__,'install_email_subscription_popup_admin');
    add_action('wp_enqueue_scripts', 'email_subscription_popup_load_styles_and_js');
    add_action('wp_footer','addModalPopupHtmlToWpFooter');
    add_action('wp_head','unsubscribe_user_func');
    add_action( 'wp_ajax_getEmailTemplate', 'getEmailTemplate' );
    add_action( 'widgets_init', 'nksnewslettersubscriberSet' );
    add_action( 'wp_ajax_store_email', 'store_email_callback' );
    add_action( 'wp_ajax_nopriv_store_email', 'store_email_callback' );
    add_action('plugins_loaded', 'load_lang_for_email_subscription_popup');
   
    add_filter( 'wp_default_editor', 'force_default_editor_email_subscriber' );
    function force_default_editor_email_subscriber() {
        //allowed: tinymce, html
        return 'tinymce';
    }
     
    
    
    function load_lang_for_email_subscription_popup() {

                load_plugin_textdomain( 'email-subscription-popup', false, basename( dirname( __FILE__ ) ) . '/languages/' );
    }

    function nksnewslettersubscriberSet() {
        register_widget( 'nksnewslettersubscriber' );
    
    }
   
    function install_email_subscription_popup_admin(){
        
        
           global $wpdb;
           $table_name = $wpdb->prefix . "nl_subscriptions";
           $table_name2 = $wpdb->prefix . "newsletters_management";
           $charset_collate = $wpdb->get_charset_collate();
           
                  $sql = "CREATE TABLE IF NOT EXISTS  " . $table_name . " (
                        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                        `name` varchar(200) NOT NULL,
                        `email` varchar(250) NOT NULL,
                        `subscribed_on` datetime NOT NULL,
                        `is_subscribed` tinyint(1) NOT NULL DEFAULT '1',
                        `unsubs_key` varchar(100) NOT NULL,
                        PRIMARY KEY  (id)
                ) $charset_collate;";
               require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
               dbDelta($sql);
         
         
        $wp_news_letter_settings=array(
        
            'newsletter_show_on'=>'any',
            'newsletter_cookie'=>'1',
            'heading'=>'Subscribe to our newsletter',
            'subheading'=>'Want to be notified when our article is published? Enter your email address and name below to be the first to know.',
            'email'=>'Email',
            'name'=>'Name',
            'submitbtn'=>'SIGN UP FOR NEWSLETTER NOW',
            'requiredfield'=>'This field is required.',
            'iinvalidemail'=>'Please enter valid email address.',
            'wait'=>'Please wait...',
            'invalid_request'=>'Invalid request.',
            'email_exist'=>'This email is already exist.',
            'success'=>'You have successfully subscribed to our Newsletter!',
            'outgoing_email_limit'=>'150',
            'unsubscribe_message'=>'You have successfully unsubscribed from email newsletter.Thank you...',
            'show_name_field'=>'1',
            'show_agreement'=>'0',
            'agreement_text'=>'I agree to <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>',
            'agreement_error'=>'Please read and agree to our terms & conditions.'
            
         );
        
        $existingopt=get_option('wp_news_letter_settings');
        if(!is_array($existingopt)){
            
            update_option('wp_news_letter_settings', $wp_news_letter_settings); 
            
        }
        else{
            
            if(!isset($existingopt['unsubscribe_message'])){
                
                $existingopt['unsubscribe_message']='You have successfully unsubscribed from email newsletter.Thank you...';
                
            }        
            if(!isset($existingopt['show_name_field'])){
                
                $existingopt['show_name_field']='1';
                
            }    
            
            if(!isset($existingopt['show_agreement'])){
                
                $existingopt['show_agreement']='0';
                
            } 
            
            if(!isset($existingopt['agreement_text'])){
                
                $existingopt['agreement_text']='I agree to <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>';
                
            }        
            
            if(!isset($existingopt['agreement_error'])){
                
                $existingopt['agreement_error']='Please read and agree to our terms & conditions.';
                
            }        
            
            update_option('wp_news_letter_settings', $existingopt); 
        }
        
    }
   
    function email_subscription_popup_admin_menu(){
  
        
        add_menu_page( __( 'Email Subscription','email-subscription-popup'), __( 'Email Subscription','email-subscription-popup'), 'administrator', 'email_subscription_popup', 'email_subscription_popup_admin_options' );
        add_submenu_page( 'email_subscription_popup', __( 'Email Subscription Form Setting','email-subscription-popup'), __( 'Email Subscription Form Setting','email-subscription-popup' ),'administrator', 'email_subscription_popup', 'email_subscription_popup_admin_options' );
        add_submenu_page( 'email_subscription_popup', __( 'Manage Subscribers','email-subscription-popup'), __( 'Manage Subscribers','email-subscription-popup'),'administrator', 'email_subscription_popup_subscribers_management', 'massEmailToEmail_Subscriber_Func' );
        add_submenu_page( 'email_subscription_popup', __( 'Unsubscribers List','email-subscription-popup'), __( 'Unsubscribers List','email-subscription-popup'),'administrator', 'Newssletter-Email-Unsubscriber', 'email_subscription_unsubscribers_func' );
        
  
        
         
    
  }
  
  
   function unsubscribe_user_func(){
    
    if(isset($_GET) and isset($_GET['action']) and isset($_GET['unsc'])){
            
            if(trim($_GET['unsc'])!=''){
                
                $unsubscriberEmail=trim(urldecode($_GET['unsc']));
                $wp_news_letter_settings=get_option('wp_news_letter_settings');
                $unsubscriberEmail=sanitize_text_field($unsubscriberEmail);
                $unsubscriberEmail=esc_html($unsubscriberEmail);
                                   
                global $wpdb;  
                $query = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'nl_subscriptions where unsubs_key = %s',array($unsubscriberEmail)); 
                $myrow  = $wpdb->get_row($query);
           
                   if(is_object($myrow)){

                      $key = md5(uniqid(rand(), true));
                      
                      $wpdb->update(
                           
                            $wpdb->prefix.'nl_subscriptions',
                            array( 
                                    'is_subscribed' => 0,	// column & new value
                                    'unsubs_key' => $key	// column & new value
                             ), 
                             array( 
                                'unsubs_key' => $unsubscriberEmail,          // where clause(s)
                               
                              ), 
                              array( 
                                        '%d',	
                                        '%s'
                                ),
                               array( 
                                        '%s'
                                )
                        );
                      
                      echo "<script>alert('".$wp_news_letter_settings['unsubscribe_message']."')</script>";
                      $url=get_bloginfo( 'url' );   
                      echo "<script>window.location.href='".$url."';</script>";
                      exit;

                    }
                
                
           }  
        }
    } 
  
  function email_subscription_unsubscribers_func(){
   
   
 $selfpage=$_SERVER['PHP_SELF']; 
   
 $action='';  
 if(isset($_REQUEST['action'])){
    $action=$_REQUEST['action']; 
 }
?>

<?php         
 switch($action){
 
  default: 
      
      
      
      if(isset($_POST['deleteEmails'])){
       
          if(!check_admin_referer('action_resubscribe_add_edit','resubscribe_and_delete_subsciber')){

                wp_die('Security check fail'); 
            }

        global $wpdb;
        $subscribersSelectedEmails=$_POST['ckboxs'];
        $mass_email_queue=get_option('mass_email_queue_news_subscriber');
        foreach($subscribersSelectedEmails as $em){
         
            $em=htmlentities(strip_tags($em),ENT_QUOTES);
             if($em!=""){
              
                $query = "delete from  ".$wpdb->prefix."nl_subscriptions where email='$em'";
                $wpdb->query($query); 
                 if(is_array($mass_email_queue)){
                     
                    $key=(int)array_search($eml,$mass_email_queue);
                    if(array_search($eml,$mass_email_queue)>=0){
                      
                       unset($mass_email_queue[$key]);
                    }
                 }   
             }         
          
         }
         
         update_option( 'mass_email_subscribers_succ',__( 'Selected subscribers deleted successfully.','email-subscription-popup') );
         update_option('mass_email_queue_news_subscriber',$mass_email_queue);  
     
       
   }else if(isset($_POST['resubscribe'])){
  

        if(!check_admin_referer('action_resubscribe_add_edit','resubscribe_and_delete_subsciber')){

              wp_die('Security check fail'); 
          }

       global $wpdb;
       $subscribersSelectedEmails=$_POST['ckboxs'];
       foreach($subscribersSelectedEmails as $em){
         
           $em=htmlentities(strip_tags($em),ENT_QUOTES);
             if($em!=""){
              
                $query = "update ".$wpdb->prefix."nl_subscriptions set is_subscribed=1  where email='$em'";
                $wpdb->query($query); 
             }
       }
       
        update_option( 'mass_email_subscribers_succ', __('Selected subscribers successfully re-subscribed.','email-subscription-popup') );
       
       
   }
        $url = plugin_dir_url(__FILE__); 
        $url = str_replace("\\","/",$url); 
        $urlCss=$url."/css/styles.css";
        
  ?>       
     <div style="width: 100%;">  
        <div style="float:left;width:65%;" >
                                                                                
  <link rel='stylesheet' href='<?php echo $urlCss; ?>' type='text/css' media='all' />   
  
<?php       
    global $wpdb;
    
    $query="SELECT * from ".$wpdb->prefix."nl_subscriptions where is_subscribed=0 ";
    
    if(isset($_GET['searchuser']) and $_GET['searchuser']!=''){
      $term=trim(urldecode($_GET['searchuser']));   
      $query.="  and ( name like '%$term%' or email like '%$term%'  )  " ; 
    } 
    
    $emails=$wpdb->get_results($query,'ARRAY_A');
    $totalRecordForQuery=sizeof($emails);
    $selfPage=$_SERVER['PHP_SELF'].'?page=Newssletter-Email-Unsubscriber'; 
     global $wp_rewrite;
    
    $rows_per_page = 10;
    if(isset($_GET['setPerPage']) and $_GET['setPerPage']!=""){
        
       $rows_per_page=$_GET['setPerPage'];
    } 
    
    $current = (isset($_GET['entrant'])) ? ($_GET['entrant']) : 1;
    $pagination_args = array(
        'base' => @add_query_arg('entrant','%#%'),
        'format' => '',
        'total' => ceil(sizeof($emails)/$rows_per_page),
        'current' => $current,
        'show_all' => false,
        'type' => 'plain',
    );
                
    $start = ($current - 1) * $rows_per_page;
    $end = $start + $rows_per_page;
    $end = (sizeof($emails) < $end) ? sizeof($emails) : $end;
    
    $selfpage=$_SERVER['PHP_SELF'];
        
    if($totalRecordForQuery>0){
        
             
             
?>              
  <?php
                $SuccMsg=get_option('mass_email_subscribers_succ');
                update_option( 'mass_email_subscribers_succ', '' );
               
                $errMsg=get_option('mass_email_subscribers_err');
                update_option( 'mass_email_subscribers_err', '' );
                ?> 
                 
                
                <?php if($SuccMsg!=""){ echo "<div class='notice notice-success is-dismissible'><p>"; echo $SuccMsg; echo "</p></div>";$SuccMsg="";}?>
                 <?php if($errMsg!=""){ echo "<div class='notice notice-error is-dismissible' ><p>"; _e($errMsg); echo "</p></div>";$errMsg="";}?>
              
                <h3><?php echo __( 'Unsubscribers','email-subscription-popup');?> </h3>                
                    <?php
                    $setacrionpage='admin.php?page=Newssletter-Email-Unsubscriber';
                    
                    if(isset($_GET['entrant']) and $_GET['entrant']!=""){
                     $setacrionpage.='&entrant='.$_GET['entrant'];   
                    }
                
                    if(isset($_GET['setPerPage']) and $_GET['setPerPage']!=""){
                     $setacrionpage.='&setPerPage='.$_GET['setPerPage'];   
                    }
                    
                    $seval="";
                    if(isset($_GET['searchuser']) and $_GET['searchuser']!=""){
                     $seval=trim($_GET['searchuser']);   
                    }
                   
                ?>
                <div style="padding-top:5px;padding-bottom:5px"><b><?php echo __( 'Search User','email-subscription-popup');?>: </b><input type="text" value="<?php echo $seval;?>" id="searchuser" name="searchuser">&nbsp;<input type='submit'  value='Search Unsubscribers' name='searchusrsubmit' class='button-primary' id='searchusrsubmit' onclick="SearchredirectTO();" >&nbsp;<input type='submit'  value='Reset Search' name='searchreset' class='button-primary' id='searchreset' onclick="ResetSearch();" ></div>  
                <script type="text/javascript" >
                 function SearchredirectTO(){
                   var redirectto='<?php echo $setacrionpage; ?>';
                   var searchval=jQuery('#searchuser').val();
                   redirectto=redirectto+'&searchuser='+jQuery.trim(encodeURIComponent(searchval));    
                   window.location.href=redirectto;
                 }
                function ResetSearch(){
                    
                     var redirectto='<?php echo $setacrionpage; ?>';
                     window.location.href=redirectto;
                }
                </script>
               <form method="post" action="" id="sendemail" name="sendemail">
                <input type="hidden" value="sendEmailForm" name="action" id="action">
                
              <table class="widefat fixed" cellspacing="0" style="width:97% !important" >
                <thead>
                <tr>
                        <th scope="col" id="name" class="manage-column column-name" style=""><input onclick="chkAll(this)" type="checkbox" name="chkallHeader" id='chkallHeader'>&nbsp;<?php echo __( 'Select All Emails','email-subscription-popup');?></th>
                        <th scope="col" id="name" class="manage-column column-name" style=""><?php echo __( 'Name','email-subscription-popup');?></th>
                        
                </tr>
                </thead>

                <tfoot>
                <tr>
                        <th scope="col" id="name" class="manage-column column-name" style=""><input onclick="chkAll(this)" type="checkbox" name="chkallfooter" id='chkallfooter'>&nbsp;<?php echo __( 'Select All Emails','email-subscription-popup');?></th>
                        <th scope="col" id="name" class="manage-column column-name" style=""><?php echo __( 'Name','email-subscription-popup');?></th>
                        
                        
                </tr>
                </tfoot>

                <tbody id="the-list" class="list:cat">
               <?php                 
                   
                                            
                         for ($i=$start;$i < $end ;++$i ) 
                         {
                             
                            if($emails[$i]!=""){ 
                           
                               $userId=$emails[$i]['id'];
                               $name=$emails[$i]['name'];
                               $email=$emails[$i]['email'];
                               
                               if(in_array($email,$mass_email_queue)) 
                                 $checked="checked='checked'";
                               else
                                 $checked="";
                                 
                           
                               echo"<tr class='iedit alternate'>
                                <td  class='name column-name' style='border:1px solid #DBDBDB;padding-left:13px;'><input type='checkBox' name='ckboxs[]' $checked  value='".$email."'>&nbsp;".$email."</td>";
                                echo "<td  class='name column-name' style='border:1px solid #DBDBDB;'> ".stripslashes($name)."</td>";
                                echo "</tr>";
                            }   
                               
                         }  
                           
                     
                       
                   ?>  
                 </tbody>       
                </table>
                <table>
                  <tr>
                    <td>
                      <?php
                       if(sizeof($emails)>0){
                         echo "<div class='pagination' style='padding-top:10px'>";
                         echo paginate_links($pagination_args);
                         echo "</div>";
                        }
                        
                       ?>
                
                    </td>
                    <td>
                      <b>&nbsp;&nbsp;<?php echo __( 'Per Page','email-subscription-popup');?> : </b>
                      <?php
                        $setPerPageadmin='admin.php?page=Newssletter-Email-Unsubscriber';
                        /*if(isset($_GET['entrant']) and $_GET['entrant']!=""){
                            $setPerPageadmin.='&entrant='.(int)trim($_GET['entrant']);
                        }*/
                        $setPerPageadmin.='&setPerPage=';
                      ?>
                      <select name="setPerPage" onchange="document.location.href='<?php echo $setPerPageadmin;?>' + this.options[this.selectedIndex].value + ''">
                        <option <?php if($rows_per_page=="10"): ?>selected="selected"<?php endif;?>  value="10">10</option>
                        <option <?php if($rows_per_page=="20"): ?>selected="selected"<?php endif;?> value="20">20</option>
                        <option <?php if($rows_per_page=="30"): ?>selected="selected"<?php endif;?>value="30">30</option>
                        <option <?php if($rows_per_page=="40"): ?>selected="selected"<?php endif;?> value="40">40</option>
                        <option <?php if($rows_per_page=="50"): ?>selected="selected"<?php endif;?> value="50">50</option>
                        <option <?php if($rows_per_page=="60"): ?>selected="selected"<?php endif;?> value="60">60</option>
                        <option <?php if($rows_per_page=="70"): ?>selected="selected"<?php endif;?> value="70">70</option>
                        <option <?php if($rows_per_page=="80"): ?>selected="selected"<?php endif;?> value="80">80</option>
                        <option <?php if($rows_per_page=="90"): ?>selected="selected"<?php endif;?> value="90">90</option>
                        <option <?php if($rows_per_page=="100"): ?>selected="selected"<?php endif;?> value="100">100</option>
                        <option <?php if($rows_per_page=="500"): ?>selected="selected"<?php endif;?> value="500">500</option>
                        <option <?php if($rows_per_page=="1000"): ?>selected="selected"<?php endif;?> value="1000">1000</option>
                        <option <?php if($rows_per_page=="2000"): ?>selected="selected"<?php endif;?> value="2000">2000</option>
                        <option <?php if($rows_per_page=="3000"): ?>selected="selected"<?php endif;?> value="3000">3000</option>
                        <option <?php if($rows_per_page=="4000"): ?>selected="selected"<?php endif;?> value="4000">4000</option>
                        <option <?php if($rows_per_page=="5000"): ?>selected="selected"<?php endif;?> value="5000">5000</option>
                      </select>  
                    </td>
                  </tr>
                </table>
                <table> 
                    <tr>
                    <td class='name column-name' style='padding-top:15px;padding-left:10px;'>
                       
                         <script type="text/javascript">
                        function sendEmailToAll(obj){

                      	  var txt;
                      	  var r = confirm("<?php echo __( 'It is not recommaded to send email to all at once as there is always hosting server limit for send emails hourly basis. Most of hosting providers allow 250 emails per hour. Please upgrade to pro version and use cron job newsletter to send email automatically. Do you still want to continue ?','email-subscription-popup');?>");
                      	  if (r == true) {
                      	     return true;
                      	  } else {
                      		return false;
                      	  }
                      	  	  

                        }
                        </script>
                        <?php wp_nonce_field('action_resubscribe_add_edit','resubscribe_and_delete_subsciber'); ?> 
                        <input onclick="return validateSendEmailAndDeleteEmail(this)" type='submit' value='<?php echo __( 'Re-subscribe selected subscribers','email-subscription-popup');?>' name='resubscribe' class='button-primary' id='resubscribe' >&nbsp;&nbsp;<input onclick="return validateSendEmailAndDeleteEmail(this)" type='submit' value='<?php echo __( 'Delete Selected Subscribers','email-subscription-popup');?>' name='deleteEmails' class='button-primary' id='deleteEmails' ></td>
                    </tr>
                      
                </table>
                </form>  
      
                  
     <?php
                   
      }
     else
      {
             echo '<center><div style="padding-bottom:50pxpadding-top:50px;"><h3>'.__( 'No Email Un-Subscription Found','email-subscription-popup').'</h3></div></center>';
             
             
      } 
     ?>
     </div>
     <div id="postbox-container-1" class="postbox-container" style="float:right;width:35%;margin-top: 50px" > 

                <div class="postbox"> 
                    <center><h3 class="hndle"><span></span><?php echo __( 'Access All Themes In One Price','email-subscription-popup');?></h3> </center>
                    <div class="inside">
                        <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="<?php echo plugins_url( 'images/300x250.gif', __FILE__ ) ;?>" width="250" height="250"></a></center>

                        <div style="margin:10px 5px">

                        </div>
                    </div></div>
                <div class="postbox"> 
                    <center><h3 class="hndle"><span></span><?php echo __( 'Google For Business','email-subscription-popup');?></h3> </center>
                    <div class="inside">
                        <center><a target="_blank" href="https://goo.gl/OJBuHT"><img style="max-width:350px" src="<?php echo plugins_url( 'images/gsuite_promo.png', __FILE__ ) ;?>" width="" height="250" border="0"></a></center>
                        <div style="margin:10px 5px">
                        </div>
                    </div></div>

            </div>
<div class="clear"></div>             

    <?php 
     break;
     
  } 
 
?>
 <script type="text/javascript" >
 
 jQuery("input[name='ckboxs[]']").click(function() {
    uncheckedmanagement(this); 
       
});

function uncheckedmanagement(elementset){
   
     //alert(jQuery(this).is(':checked'));
     
     if(jQuery("#uncheckedemails").length>0){
        var hiddenvals=jQuery("#uncheckedemails").val();
     }
     else
       hiddenvals="|||";
       
     var emailval=jQuery(elementset).val();
     var emailsUn= hiddenvals.split('|||');
     
     if(jQuery(elementset).is(':checked')){
         
         if(jQuery.isArray(emailsUn)==true){
             
            emailsUn.splice(jQuery.inArray(emailval, emailsUn),1); 
            var strconvert=emailsUn.join('|||'); 
            jQuery("#uncheckedemails").val(strconvert); 
         }
        else{
            
             var addtohidden=emailval.toString()+'|||';
             jQuery("#uncheckedemails").val(addtohidden);
        }  
         
     }
     else{
            
            if(jQuery.isArray(emailsUn)==true){
                
                if(jQuery.inArray(emailval, emailsUn)<=0){
                    emailsUn.push(emailval);      
                    var strconvert=emailsUn.join('|||');             
                    jQuery("#uncheckedemails").val(strconvert); 
                }
                
            }
           else{
                    var addtohidden=emailval.toString()+'|||';
                    jQuery("#uncheckedemails").val(addtohidden);
               
           }         
     }
     
       
}

  function chkAll(id){
  
  if(id.name=='chkallfooter'){
  
    var chlOrnot=id.checked;
    document.getElementById('chkallHeader').checked= chlOrnot;
   
  }
 else if(id.name=='chkallHeader'){ 
  
      var chlOrnot=id.checked;
     document.getElementById('chkallfooter').checked= chlOrnot;
  
   }
 
     if(id.checked){
     
          var objs=document.getElementsByName("ckboxs[]");
           
           for(var i=0; i < objs.length; i++)
          {
             objs[i].checked=true;
              uncheckedmanagement(objs[i]);
            }

     
     } 
    else {

          var objs=document.getElementsByName("ckboxs[]");
           
           for(var i=0; i < objs.length; i++)
          {
              objs[i].checked=false;
              uncheckedmanagement(objs[i]);
            }  
      } 
  } 
  
  function validateSendEmailAndDeleteEmail(idobj){
       
       var objs=document.getElementsByName("ckboxs[]");
       var ischkBoxChecked=false;
       for(var i=0; i < objs.length; i++){
         if(objs[i].checked==true){
         
             ischkBoxChecked=true;
             break;
           }
       
        }  
      
      if(ischkBoxChecked==false)
      {
         if(idobj.name=='resubscribe'){
         alert('<?php echo __( 'Please select atleast one email.','email-subscription-popup');?>')  ;
         return false;
        
         }
        else if(idobj.name=='deleteEmails') 
         {
            alert('<?php echo __( 'Please select atleast one email to delete.','email-subscription-popup');?>')  
             return false;  
         }
      }
     else{
           if(idobj.name=='deleteEmails') {
               
           
                var r = confirm("<?php echo __( 'Are you sure to delete selected subscribers ?','email-subscription-popup');?>");
                if (r == true) {
                    return true;
                }else{

                    return false;
                }
            
            }

       
        } 
        
  } 
     
  </script>
 
<?php  
   
}

 function email_subscription_popup_load_styles_and_js(){
     
     wp_enqueue_script('jquery');         
     wp_enqueue_style( 'wp-email-subscription-popup', plugins_url('/css/wp-email-subscription-popup.css', __FILE__) );
     wp_enqueue_script('wp-email-subscription-popup-js',plugins_url('/js/wp-email-subscription-popup-js.js', __FILE__));
     wp_enqueue_script('subscribe-popup',plugins_url('/js/subscribe-popup.js', __FILE__));
     wp_enqueue_style('subscribe-popup',plugins_url('/css/subscribe-popup.css', __FILE__));
     
 } 
        
 function addModalPopupHtmlToWpFooter(){
    $imgUrl=plugin_dir_url(__FILE__)."images/";
  
    $loader=$imgUrl.'AjaxLoader.gif';
    $wp_news_letter_settings=get_option('wp_news_letter_settings');
    ob_start();  
 ?>
 <div class="overlay" id="mainoverlayDiv" ></div> 
 
 <div class="mydiv" id='formFormEmail' >
     <div class="container_n">
        
       <form id="newsletter_signup" name="newsletter_signup">
          
          
        <div class="header">
            <div class="AjaxLoader"><img src="<?php echo $loader;?>"/><?php echo $wp_news_letter_settings['wait'];?></div>
            <div id="myerror_msg" class="myerror_msg"></div>
            <div id="mysuccess_msg" class="mysuccess_msg"></div>
         
            <h3><?php echo $wp_news_letter_settings['heading'];?></h3>
            
            <div class="subheading"><?php echo $wp_news_letter_settings['subheading'];?></div>
            
        </div>
        
        <div class="sep"></div>

        <div class="inputs">
        
             <input type="email" class="textfield"  onblur="restoreInput(this,'<?php echo $wp_news_letter_settings['email'];?>')" onfocus="return clearInput(this,'<?php echo $wp_news_letter_settings['email'];?>');"  value="<?php echo $wp_news_letter_settings['email'];?>" name="youremail" id="youremail"  />
             <div style="clear:both"></div>
             <div class="errorinput"></div>
             <?php if($wp_news_letter_settings['show_name_field']):?>
                <input type="text" class="textfield" id="yourname" onblur="restoreInput(this,'<?php echo $wp_news_letter_settings['name'];?>')" onfocus="return clearInput(this,'<?php echo $wp_news_letter_settings['name'];?>');"  value="<?php echo $wp_news_letter_settings['name'];?>" name="yourname" />
                <div style="clear:both"></div>
                <div class="errorinput"></div>
             <?php endif;?>
             <?php if($wp_news_letter_settings['show_agreement']):?>
                <input type="checkbox"  id="chkagreeornot" value="1" name="chkagreeornot" style="display:inline" /><span class="agree_term"> <?php echo html_entity_decode ($wp_news_letter_settings['agreement_text']);?></span>
                <div style="clear:both"></div>
                <div class="errorinput"></div>
             <?php endif;?>
             <a id="submit_newsletter"  onclick="submit_newsletter($n);" name="submit_newsletter"><?php echo $wp_news_letter_settings['submitbtn'];?></a>
        
        </div>

    </form>

    </div>      
</div>                     
    <script type='text/javascript'>
    
      var $n = jQuery.noConflict();  
      /* if ( $n.browser.msie && $n.browser.version >= 9 )
        {
            $n.support.noCloneEvent = true
        }*/
    
     var htmlpopup=$n("#formFormEmail").html();   
      $n("#formFormEmail").remove();
         
     $n('body').on('click', '.shownewsletterbox', function() {
        
          $n.fancybox_ns({ 
             
                'overlayColor':'#000000',
                'hideOnOverlayClick':false,
                'padding': 10,
                'autoScale': true,
                'showCloseButton'   : true,
                'content' :htmlpopup,
                'transitionIn':'fade',
                'transitionOut':'elastic',
                'width':560,
                'height':360
            });
        
      });
    
    <?php if($wp_news_letter_settings['newsletter_show_on']=='any'): ?>
        
        
        $n(document).ready(function() {
            
         if(readCookie('newsLatterPopup')==null){
             
             
             $n.fancybox_ns({ 
             
                'overlayColor':'#000000',
                'hideOnOverlayClick':false,
                'padding': 10,
                'autoScale': true,
                'showCloseButton'   : true,
                'content' :htmlpopup,
                'transitionIn':'fade',
                'transitionOut':'elastic',
                'width':560,
                'height':360
            });

               
              createCookie('newsLatterPopup','donotshow',<?php echo $wp_news_letter_settings['newsletter_cookie'];?>);
              
             }
         }); 
    <?php elseif($wp_news_letter_settings['newsletter_show_on']=='home'):?>
        <?php if(is_front_page()):?>
            
             $n(document).ready(function() {
            
                  if(readCookie('newsLatterPopup')==null){


                    $n.fancybox_ns({ 

                       'overlayColor':'#000000',
                       'hideOnOverlayClick':false,
                       'padding': 10,
                       'autoScale': true,
                       'showCloseButton'   : true,
                       'content' :htmlpopup,
                       'transitionIn':'fade',
                       'transitionOut':'elastic',
                       'width':560,
                       'height':360
                   });


                   createCookie('newsLatterPopup','donotshow',<?php echo $wp_news_letter_settings['newsletter_cookie'];?>);

                    }
                }); 

        <?php endif;?>    
    <?php endif;?>     
      

     
       function clearInput(source, initialValue){     
           
            if(source.value.toUpperCase()==initialValue.toUpperCase())
                source.value='';
                
            return false;    
        }

        function restoreInput(source, initialValue)
        {   
            if(source.value == '')  
                source.value = initialValue;
         
            return false;    
        }    
        
       
    
    
    function submit_newsletter(){        
        
           
            var emailAdd=$n.trim($n("#youremail").val());
            var yourname=$n.trim($n("#yourname").val());
            
            var returnval=false;
            var isvalidName=false;
            var isvalidEmail=false;
            var is_agreed=false;
             if($n('#yourname').length >0){
                
               var yourname=$n.trim($n("#yourname").val());
                if(yourname!="" && yourname!=null && yourname.toLowerCase()!='<?php echo $wp_news_letter_settings['name'];?>'.toLowerCase()){
                    
                    var element=$n("#yourname").next().next();
                    isvalidName=true;
                    $n(element).html('');
                }
                else{
                        var element=$n("#yourname").next().next();
                        $n(element).html('<div class="image_error"><?php echo $wp_news_letter_settings['requiredfield'];?></div>');
                       // emailAdd=false;

                }
           
           }
           else{
            
                isvalidName=true;
           
           }
           
           if(emailAdd!=""){
               
               
                var element=$n("#youremail").next().next();
                if(emailAdd.toLowerCase()=='<?php echo $wp_news_letter_settings['email'];?>'.toLowerCase()){
                    
                    $n(element).html('<div  class="image_error"><?php echo $wp_news_letter_settings['requiredfield'];?></div>');
                    isvalidEmail=false;
                }else{
                    
                       var JsRegExPatern = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/

                        if(JsRegExPatern.test(emailAdd)){
                            
                            isvalidEmail=true;
                            $n(element).html('');    
                            
                        }else{
                            
                             var element=$n("#youremail").next().next();
                             $n(element).html('<div class="image_error"><?php echo $wp_news_letter_settings['iinvalidemail'];?></div>');
                             isvalidEmail=false;
                            
                        }
                        
                }
               
           }else{
               
                    var element=$n("#yourname").next().next();
                    $n(element).html('<div class="image_error"><?php echo $wp_news_letter_settings['requiredfield'];?></div>');
                    isvalidEmail=false;
               
           } 
           
             if($n('#chkagreeornot').length >0){
                 
                if($n("#chkagreeornot").is(':checked')){

                    var element=$n("#chkagreeornot").next().next();
                    $n(element).html('');    
                    is_agreed=true;
                }
                else{


                     var element=$n("#chkagreeornot").next().next();
                     $n(element).html('<div class="image_error"><?php echo $wp_news_letter_settings['agreement_error'];?></div>');
                     is_agreed=false;

                }
             }
             else{
             
                is_agreed=true;
             }

            
            if(isvalidName==true && isvalidEmail==true && is_agreed==true){
                
                $n(".AjaxLoader").show();
                $n('#mysuccess_msg').html('');
                $n('#mysuccess_msg').hide();
                $n('#myerror_msg').html('');
                $n('#myerror_msg').hide();
                
                var name="";
                if($n('#yourname').length >0){
                    
                     name =$n("#yourname").val();  
                }
                var nonce ='<?php echo wp_create_nonce('newsletter-nonce'); ?>';
                var url = '<?php echo plugin_dir_url(__FILE__);?>';  
                var email=$n("#youremail").val(); 
                var str="action=store_email&email="+email+'&name='+name+'&is_agreed='+is_agreed+'&sec_string='+nonce;
                $n.ajax({
                   type: "POST",
                   url: '<?php echo admin_url('admin-ajax.php'); ?>',
                   data:str,
                   async:true,
                   success: function(msg){
                       if(msg!=''){
                           
                             var result=msg.split("|"); 
                             if(result[0]=='success'){
                                 
                                 $n(".AjaxLoader").hide();
                                 $n('#mysuccess_msg').html(result[1]);
                                 $n('#mysuccess_msg').show();  
                                 
                                 setTimeout(function(){
                                  
                                     $n.fancybox_ns.close();


                                     
                                },2000);
                                 
                             }
                             else{
                                   $n(".AjaxLoader").hide(); 
                                   $n('#myerror_msg').html(result[1]);
                                   $n('#myerror_msg').show();
                             }
                           
                       }
                 
                    }
                }); 
                
            }
            
            
        
      
              
      }
    </script>
    
<?php     
    $output = ob_get_clean();
    echo $output;
 }
 
 
 function email_subscription_popup_admin_options(){
     
     if(isset($_POST['btnsave'])){
         
          if(!check_admin_referer( 'action_settings_add_edit','add_edit_nonce' )){

                wp_die('Security check fail'); 
           }
           
         $newsletter_show_on='none';
         $newsletter_cookie=0;
         if(isset($_POST['newsletter_show_on'])){
             $newsletter_show_on=htmlentities(strip_tags($_POST['newsletter_show_on']),ENT_QUOTES);
             if($newsletter_show_on=='home')
                 $newsletter_cookie=htmlentities(strip_tags($_POST['cookieTimeUpUniqueHomePage']),ENT_QUOTES);
             else if($newsletter_show_on=='any')
                 $newsletter_cookie=htmlentities(strip_tags($_POST['cookieTimeUpUniqueAnyPage']),ENT_QUOTES);
                 
           
         }
         
         $options=array();
         $options['newsletter_cookie']          =trim($newsletter_cookie);  
         $options['newsletter_show_on']          =trim($newsletter_show_on);  
         $options['heading']                     =trim(htmlentities(strip_tags($_POST['heading']),ENT_QUOTES));  
         $options['subheading']                  =trim(htmlentities(strip_tags($_POST['subheading']),ENT_QUOTES));
         $options['email']                       =trim(htmlentities(strip_tags($_POST['email']),ENT_QUOTES));  
         $options['name']                        =trim(htmlentities(strip_tags($_POST['name']),ENT_QUOTES));  
         $options['submitbtn']                   =trim(htmlentities(strip_tags($_POST['submitbtn']),ENT_QUOTES));  
         $options['requiredfield']               =trim(htmlentities(strip_tags($_POST['requiredfield']),ENT_QUOTES));
         $options['iinvalidemail']               =trim(htmlentities(strip_tags($_POST['iinvalidemail']),ENT_QUOTES));
         $options['wait']                        =trim(htmlentities(strip_tags($_POST['wait']),ENT_QUOTES));
         $options['invalid_request']             =trim(htmlentities(strip_tags($_POST['invalid_request']),ENT_QUOTES));
         $options['email_exist']                 =trim(htmlentities(strip_tags($_POST['email_exist']),ENT_QUOTES));
         $options['success']                     =trim(htmlentities(strip_tags($_POST['success']),ENT_QUOTES));  
         $options['unsubscribe_message']         =trim(htmlentities(strip_tags($_POST['unsubscribe_message']),ENT_QUOTES));  
         $options['show_name_field']             =trim(htmlentities(strip_tags($_POST['show_name_field']),ENT_QUOTES));  
         $options['show_agreement']              =trim(htmlentities(strip_tags($_POST['show_agreement']),ENT_QUOTES));  
         $options['agreement_text']              =trim(htmlentities(strip_tags(stripslashes( $_POST['agreement_text']),'<a><b><p><strong><em><i>'),ENT_QUOTES));  
         $options['agreement_error']              =trim(htmlentities(strip_tags($_POST['agreement_error']),ENT_QUOTES));  
         
         $settings=update_option('wp_news_letter_settings',$options); 
         $email_subscription_popup_messages=array();
         $email_subscription_popup_messages['type']='succ';
         $email_subscription_popup_messages['message']='Settings saved successfully.';
         update_option('email_subscription_popup_messages', $email_subscription_popup_messages);

        
         
     }  
      $wp_news_letter_settings=get_option('wp_news_letter_settings');
      
      if(! isset($wp_news_letter_settings['unsubscribe_message'])){
          
          $wp_news_letter_settings['unsubscribe_message']='You have successfully unsubscribed from email newsletter.Thank you...';
      }
      
      if(! isset($wp_news_letter_settings['subscribe_message'])){
          
          $wp_news_letter_settings['subscribe_message']='You have successfully subscribed for email newsletter.Thank you...';
      }
      
      if(! isset($wp_news_letter_settings['show_name_field'])){
          
          $wp_news_letter_settings['show_name_field']='1';
      }
      
      if(! isset($wp_news_letter_settings['show_agreement'])){
          
          $wp_news_letter_settings['show_agreement']='0';
      }
      
      
      if(! isset($wp_news_letter_settings['agreement_text'])){
          
          
          $wp_news_letter_settings['agreement_text']='I agree to <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>';
      }
      
       if(!isset($wp_news_letter_settings['agreement_error'])){
                
            $wp_news_letter_settings['agreement_error']='Please read and agree to our terms & conditions.';

        }  
      
      
?>      
       <?php  $url = plugin_dir_url(__FILE__);
           $urlJS=$url."js/jqueryValidate.js";
           $urlCss=$url."/css/styles.css";

     ?>
    <script src="<?php echo $urlJS; ?>"></script>
    <link rel='stylesheet' href='<?php echo $urlCss; ?>' type='text/css' media='all' />

    <style type="">
    .fieldsetAdmin {
        margin: 10px 0px;
        padding: 10px;
        border: 1px solid rgb(221, 221, 221);
        font-size: 15px;
    }
        .fieldsetAdmin legend {
            font-weight: bold;
            color: #222222;
            
        }
    </style>
     <div style="width: 100%;">  
        <div style="float:left;width:65%;">
            <div class="wrap">
                
              <?php
                    $messages=get_option('email_subscription_popup_messages'); 
                    $type='';
                    $message='';
                    if(isset($messages['type']) and $messages['type']!=""){

                    $type=$messages['type'];
                    $message=$messages['message'];

                    }  


                    if($type=='err'){ echo "<div class='notice notice-error is-dismissible'><p>"; echo $message; echo "</p></div>";}
                    else if($type=='succ'){ echo "<div class='notice notice-success is-dismissible'><p>"; echo $message; echo "</p></div>";}


                    update_option('email_subscription_popup_messages', array());     
              ?>     
               <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                            <td>
                                <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                                    <img id="help us for free plugin" height="30" width="90" src="<?php echo plugins_url( 'images/paypaldonate.jpg', __FILE__ );?>" border="0" alt="help us for free plugin" title="help us for free plugin">
                                </a>
                            </td>
                        </tr>
                    </table> 
               <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-pro-plugins/wordpress-newsletter-subscription-pro-plugin.html">UPGRADE TO PRO VERSION</a></h3></span>
                 
             <h2>Settings</h2>
            <br>
            <div id="poststuff">
              <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                  <form method="post" action="" id="subscriptionFrmsettiings" name="subscriptionFrmsettiings" >
                     <fieldset class="fieldsetAdmin">
                      <legend><?php echo __( 'Email Lightbox Popup Settings','email-subscription-popup');?></legend>
                      <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Show Modal Popup On','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                     <table>
                                         <tr>
                                             <td style="vertical-align: top">
                                                 <input type="radio" name="newsletter_show_on" id="unique_home_page" value="home" style="width:10px">
                                             </td>
                                             <td>
                                                 <b><?php echo __( 'Show Newsletter modal Popup On Unique Request Only For Home page','email-subscription-popup');?></b>
                                                 <br/>
                                                 <div id="cookTimeHomepageRequest" style="display:none">
                                                    <?php echo __( 'Cookie Time :','email-subscription-popup');?>
                                                    <input style="width:50px" type="text" size="5" name="cookieTimeUpUniqueHomePage" value="<?php echo $wp_news_letter_settings['newsletter_cookie'];?>"  id="cookieTimeUpUniqueHomePage"/> <?php echo __( 'In Days','email-subscription-popup');?>
                                                     <div style="clear:both"></div>
                                                     <div></div>
                                                 </div>
                                                 <script>
                                                      $n=jQuery.noConflict();
                                                      $n( "#unique_home_page" ).click(function() {
                                                          
                                                          $n("#cookTimeAnypageRequest").hide();
                                                          $n("#cookTimeHomepageRequest").show();
                                                       });
                                                 </script>    
                                             </td>
                                         </tr>
                                          <tr>
                                             <td style="vertical-align: top">
                                                 <input type="radio" name="newsletter_show_on" id="unique_any" value="any" style="width:10px">
                                             </td>
                                             <td>
                                                 <b><?php echo __( 'Show Newsletter modal Popup On Unique Request any page','email-subscription-popup');?></b>
                                                 <br/>
                                                 <div id="cookTimeAnypageRequest" style="display:none">
                                                    <?php echo __( 'Cookie Time :','email-subscription-popup');?>
                                                    <input style="width:50px" type="text" size="5" name="cookieTimeUpUniqueAnyPage" value="<?php echo $wp_news_letter_settings['newsletter_cookie'];?>" id="cookieTimeUpUniqueAnyPage"/> <?php echo __( 'In Days','email-subscription-popup');?>
                                                      <div style="clear:both"></div>
                                                       <div></div>
                                                 </div>
                                                 <script>
                                                      $n=jQuery.noConflict();
                                                      $n( "#unique_any" ).click(function() {
                                                           $n("#cookTimeHomepageRequest").hide();
                                                          $n("#cookTimeAnypageRequest").show();
                                                       });
                                                 </script> 
                                                 
                                                    </td>
                                         </tr>
                                            <tr>
                                               <td style="vertical-align: top">
                                                   <input  type="radio" name="newsletter_show_on" value="none" id="show_none" style="width:10px">

                                               </td>
                                               <td>
                                                   <b><?php echo __( 'No, I will use my custom link','email-subscription-popup');?></b>
                                                  <script>
                                                      $n=jQuery.noConflict();
                                                      $n( "#show_none" ).click(function() {
                                                           $n("#cookTimeHomepageRequest").hide();
                                                          $n("#cookTimeAnypageRequest").hide();
                                                       });
                                                 </script> 
                                                </td>
                                           </tr>
                                             <tr>
                                             <td>
                                                
                                                 
                                             </td>
                                             <td>
                                                 <br/>
                                                 <b><?php echo __( 'To show Newsletter modal Popup On Custom Link Click Use','email-subscription-popup');?> <i>shownewsletterbox</i> css class</b>
                                                <br/>
                                                <br/>
                                                 <b><?php echo __( 'Example :','email-subscription-popup');?> </b>
                                                 <pre><?php echo htmlspecialchars('<a href="#" class="shownewsletterbox">Subscribe to Newsletter</a>');?></pre>
                                             </td>
                                             
                                         </tr>
                                       
                                     </table>
                                     <hr/>
                                     <table>
                                           <tr>
                                            <td class="label" style="width:35%">
                                                <h3 style="font-size: 13px"><label for="show_name_field"><?php echo __('Show Name Field In Newsletter Popup ?','email-subscription-popup');?> <span class="required">*</span></label></h3>
                                            </td>
                                            <td class="value" style="width:65%">
                                                 <select id="show_name_field" name="show_name_field" class="select">
                                                    <option value=""><?php echo __('Select','email-subscription-popup');?></option>
                                                    <option <?php if($wp_news_letter_settings['show_name_field']=='1'):?> selected="selected" <?php endif;?>  value="1" ><?php echo __('Yes','email-subscription-popup');?></option>
                                                    <option <?php if($wp_news_letter_settings['show_name_field']=='0'):?> selected="selected" <?php endif;?>  value="0"><?php echo __('No','email-subscription-popup');?></option>
                                                </select> 
                                                <div style="clear:both"></div>
                                                <div class="error_label"></div> 
                                            </td>
                                        </tr>
                                         <tr>
                                            <td class="label" style="width:35%">
                                                <h3 style="font-size: 13px"><label for="show_agreement"><?php echo __('Show Checkbox For Terms and Conditions Agreement','email-subscription-popup');?> <span class="required">*</span></label></h3>
                                            </td>
                                            <td class="value" style="width:65%">
                                                 <select id="show_agreement" name="show_agreement" class="select">
                                                    <option value=""><?php echo __('Select','email-subscription-popup');?></option>
                                                    <option <?php if($wp_news_letter_settings['show_agreement']=='1'):?> selected="selected" <?php endif;?>  value="1" ><?php echo __('Yes','email-subscription-popup');?></option>
                                                    <option <?php if($wp_news_letter_settings['show_agreement']=='0'):?> selected="selected" <?php endif;?>  value="0"><?php echo __('No','email-subscription-popup');?></option>
                                                </select> 
                                                <div style="clear:both"></div>
                                                <div class="error_label"></div> 
                                            </td>
                                        </tr>
                                         <tr>
                                            <td class="label" style="width:35%">
                                                <h3 style="font-size: 13px"><label for="agreement_text"><?php echo __('Agreement Text','email-subscription-popup');?> <span class="required">*</span></label></h3>
                                            </td>
                                            <td class="value" style="width:65%">
                                                <textarea name="agreement_text" id="agreement_text" style="width: 100%;height: 74px;"><?php echo html_entity_decode($wp_news_letter_settings['agreement_text']);?></textarea>
                                                <div style="clear:both;font-size: 12px;color:black"><?php echo __('Replace # with your Terms of Service and Privacy Policy full Url.','email-subscription-popup');?></div>
                                                <div class="error_label"></div> 
                                            </td>
                                        </tr>
                                     </table>    
                                   <div style="clear:both"></div>
                                   <div></div>
                                  
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                             <script>
                                 <?php if($wp_news_letter_settings['newsletter_show_on']=='any'): ?>
                                     $n('#unique_any').trigger('click');
                                 <?php elseif($wp_news_letter_settings['newsletter_show_on']=='home'): ?>
                                      $n('#unique_home_page').trigger('click');
                                 <?php else: ?>
                                     $n("#show_none").trigger('click');
                                 <?php endif;?>    
                             </script>    
                         </div>
                      </div>
                     
                     </fieldset> 
                     <fieldset class="fieldsetAdmin">
                      <legend><?php echo __( 'Subscription Form Settings Messages & Label Settings','email-subscription-popup');?></legend>
                      <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Heading','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <input type="text" id="heading" size="50" name="heading" value="<?php echo $wp_news_letter_settings['heading'];?>" style="width:550px;">
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                      </div>
                      <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Subheading','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <textarea id="subheading" style="width:550px;height:60px" size="50" name="subheading" ><?php echo $wp_news_letter_settings['subheading'];?></textarea>
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                      </div>
                      <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Email Label','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <input type="text" id="email" size="50" name="email" value="<?php echo $wp_news_letter_settings['email'];?>" style="width:550px;">
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                      </div>   
                      <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Name Label','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <input type="text" id="name" size="50" name="name" value="<?php echo $wp_news_letter_settings['name'];?>" style="width:550px;">
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                       </div>  
                         <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Submit Button Label','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <input type="text" id="submitbtn" size="50" name="submitbtn" value="<?php echo $wp_news_letter_settings['submitbtn'];?>" style="width:550px;">
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                      </div>   
                     </fieldset> 
                     <fieldset class="fieldsetAdmin">
                      <legend><?php echo __( 'Errors & validation Messages Settings','email-subscription-popup');?></legend>
                      <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Required Field Message','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <input type="text" id="requiredfield" size="50" name="requiredfield" value="<?php echo $wp_news_letter_settings['requiredfield'];?>" style="width:550px;">
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                      </div>
                      <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Invalid Email Message','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <input type="text" id="iinvalidemail" size="50" name="iinvalidemail" value="<?php echo $wp_news_letter_settings['iinvalidemail'];?>" style="width:550px;">
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                      </div>
                      <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Invalid Request Message','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <input type="text" id="invalid_request" size="50" name="invalid_request" value="<?php echo $wp_news_letter_settings['invalid_request'];?>" style="width:550px;">
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                      </div>   
                      <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Email Exist Message','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <input type="text" id="email_exist" size="50" name="email_exist" value="<?php echo $wp_news_letter_settings['email_exist'];?>" style="width:550px;">
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                       </div>  
                      <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Agreement Error','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <input type="text" id="agreement_error" size="50" name="agreement_error" value="<?php echo $wp_news_letter_settings['agreement_error'];?>" style="width:550px;">
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                       </div>  
                       <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Success Message','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <input type="text" id="success" size="50" name="success" value="<?php echo $wp_news_letter_settings['success'];?>" style="width:550px;">
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                      </div> 
                      <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Unsubscribe Message','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <input type="text" id="unsubscribe_message" size="50" name="unsubscribe_message" value="<?php echo $wp_news_letter_settings['unsubscribe_message'];?>" style="width:550px;">
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                      </div> 
                      <div class="stuffbox" id="namediv" style="min-width:550px;">
                         <h3><label><?php echo __( 'Wait Message','email-subscription-popup');?></label></h3>
                        <div class="inside">
                             <table>
                               <tr>
                                 <td>
                                   <input type="text" id="wait" size="50" name="wait" value="<?php echo $wp_news_letter_settings['wait'];?>" style="width:550px;">
                                   <div style="clear:both"></div>
                                   <div></div>
                                 </td>
                               </tr>
                             </table>
                             <div style="clear:both"></div>
                         </div>
                      </div>   
                     </fieldset>
                     <?php wp_nonce_field('action_settings_add_edit','add_edit_nonce' ); ?> 
                    <input type="submit"  name="btnsave" id="btnsave" value="<?php echo __( 'Save Changes','email-subscription-popup');?>" class="button-primary">
                                  
                 </form> 
                  <script type="text/javascript">
                  
                     var $n = jQuery.noConflict();  
                     $n(document).ready(function() {
                     
                     $n.validator.addMethod("checkHomeCookie", function(value, element) {
                         
                         
                         if($n('input[name="newsletter_show_on"]:checked').val()=='home' && $n.trim($n("#cookieTimeUpUniqueHomePage").val())==''){
                             return false;
                         }
                         else{
                             return true;
                         }
                             
                        
                      }, "Please enter cookie value");
                      
                       $n.validator.addMethod("checkanypageCookie", function(value, element) {
                         
                         if($n('input[name="newsletter_show_on"]:checked').val()=='any' && $n.trim($n("#cookieTimeUpUniqueAnyPage").val())==''){
                             return false;
                         }
                         else{
                             return true;
                         }
                             
                        
                      }, "Please enter cookie value");
                        $n("#subscriptionFrmsettiings").validate({
                            rules: {
                                      cookieTimeUpUniqueHomePage: {
                                      checkHomeCookie:true,
                                      digits:true
                                   
                                    },  
                                      cookieTimeUpUniqueAnyPage: {
                                      checkanypageCookie:true,
                                      digits:true
                                   
                                    },  
                                    heading: {
                                      required:true
                                    },subheading: {
                                      required:true 
                                    },
                                    email:{
                                        required:true
                                    },
                                    name:{
                                      required:true
                                    },
                                    submitbtn:{
                                      required:true
                                    },
                                   requiredfield:{
                                      required:true
                                    },
                                    iinvalidemail:{
                                      required:true
                                    },
                                    invalid_request:{
                                      required:true
                                    },
                                    email_exist:{
                                      required:true
                                    },
                                    success:{
                                      required:true
                                    },
                                    success:{
                                      required:true
                                    },
                                    unsubscribe_message:{
                                      required:true
                                    }
                                    ,wait:{
                                      required:true
                                    }
                                    
                               },
                                 errorClass: "image_error",
                                 errorPlacement: function(error, element) {
                                 error.appendTo( element.next().next());
                             } 
                             

                        })
                    });
                  
                </script> 

                </div>
          </div>
        </div>  
     </div>      
</div>
         <div id="postbox-container-1" class="postbox-container" style="float:right;width:35%;margin-top: 50px" > 

                <div class="postbox"> 
                    <center><h3 class="hndle"><span></span><?php echo __( 'Access All Themes In One Price','email-subscription-popup');?></h3> </center>
                    <div class="inside">
                        <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="<?php echo plugins_url( 'images/300x250.gif', __FILE__ ) ;?>" width="250" height="250"></a></center>

                        <div style="margin:10px 5px">

                        </div>
                    </div></div>
                <div class="postbox"> 
                    <center><h3 class="hndle"><span></span><?php echo __( 'Google For Business','email-subscription-popup');?></h3> </center>
                    <div class="inside">
                        <center><a target="_blank" href="https://goo.gl/OJBuHT"><img style="max-width:350px" src="<?php echo plugins_url( 'images/gsuite_promo.png', __FILE__ ) ;?>" width="" height="250" border="0"></a></center>
                        <div style="margin:10px 5px">
                        </div>
                    </div></div>

            </div>
<div class="clear"></div></div>  
<?php
 }
?>
<?php
function massEmailToEmail_Subscriber_Func(){
   
   
 $selfpage=$_SERVER['PHP_SELF']; 
   
 $action='';  
 if(isset($_REQUEST['action'])){
      $action=$_REQUEST['action']; 
 }
?>

<?php         
 switch($action){
     
  
  case 'sendEmailSend':
    
     if ( !check_admin_referer( 'action_settings_add_edit','sendEmailSend')){

            wp_die('Security check fail'); 
         }
         
    $emailTo= preg_replace('/\s\s+/', ' ', $_POST['emailTo']);
    $toSendEmail=explode(",",$emailTo);
    global $wpdb;
    
    $flag=false;
    foreach($toSendEmail as $key=>$val){
        
        $val=trim(htmlentities(strip_tags($val),ENT_QUOTES));
        
        $subject=stripslashes($_POST['email_subject']);
        //$subject=trim(htmlentities(strip_tags($subject),ENT_QUOTES));
        $from_name=stripslashes($_POST['email_From_name']);
       // $from_name=trim(htmlentities(strip_tags($from_name),ENT_QUOTES));
        
        $from_email=htmlentities(strip_tags($_POST['email_From']),ENT_QUOTES);
        $emailBody=$_POST['txtArea'];
     
        $query="SELECT * FROM ".$wpdb->prefix."nl_subscriptions WHERE email='$val'";
        
        $userInfo  = $wpdb->get_row($query);
        $user_full_name="";
        $user_email="";
        $unsubscribeLinkHtml="";
        $unsubscribeLinkPlain="";
        
        if(is_object($userInfo)){
            
          $uerIdunsbs=  urldecode($userInfo->unsubs_key);
          $user_email=$userInfo->email;
          $user_full_name = stripslashes($userInfo->name);
          
        }
        $url = get_home_url();
        $unsubs=$url.'?action=nks_unsubscribeuser&unsc='.$uerIdunsbs;  
        $unsubscribeLinkHtml='<a href="'.$unsubs.'" target="_blank">Unsubscribe me from all email messages</a>';  
        $unsubscribeLinkPlain=$unsubs;
    

        $emailBody=stripslashes($emailBody);
        
        $emailBody=str_replace('[user_full_name]',$user_full_name,$emailBody); 
        $emailBody=str_replace('[user_email]',$user_email,$emailBody); 
        $emailBody=str_replace('[unsubscribe_link_plain]',$unsubscribeLinkPlain,$emailBody); 
        $emailBody=str_replace('[unsubscribe_link_html]',$unsubscribeLinkHtml,$emailBody); 
        
        $charSet=get_bloginfo('charset');
       
        $mailheaders='';
        //$mailheaders .= "X-Priority: 1\n";
        $mailheaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
        $mailheaders .= "From: $from_name <$from_email>" . "\r\n";
        //$mailheaders .= "Bcc: $emailTo" . "\r\n";
        $message=$emailBody;
        
        $message=nl2br($message,true); 
        $Rreturns=wp_mail($val, $subject, $message, $mailheaders);
        
        if($Rreturns)
           $flag=true;
        
    }  
     $adminUrl=get_admin_url();
     if($flag){
     
        update_option( 'mass_email_subscribers_succ', __('Email sent successfully.','email-subscription-popup') );
        $entrant=empty($_POST['entrant'])?1:(int)$_POST['entrant'];
        $setPerPage=empty($_POST['setPerPage'])?10:(int)$_POST['setPerPage'];
        $searchuser=htmlentities(strip_tags($_POST['searchuser']),ENT_QUOTES);
        
        echo "<script>window.location.href='". $adminUrl."admin.php?page=email_subscription_popup_subscribers_management&entrant=$entrant&setPerPage=$setPerPage&searchuser=$searchuser"."';</script>"; 
        exit;
     
     }
    else{
        
           $entrant=empty($_POST['entrant'])?1:(int)$_POST['entrant'];
           $setPerPage=empty($_POST['setPerPage'])?10:(int)$_POST['setPerPage'];
           $searchuser=htmlentities(strip_tags($_POST['searchuser']),ENT_QUOTES);
           
           update_option( 'mass_email_subscribers_err', __('Unable to send email to newsletter subscribers.','email-subscription-popup') );
           echo "<script>window.location.href='". $adminUrl."admin.php?page=email_subscription_popup_subscribers_management&entrant=$entrant&setPerPage=$setPerPage&searchuser=$searchuser"."';</script>";
           exit;
    } 
   break;
   
  case 'sendEmailForm' :
   $referer=$_SERVER['HTTP_REFERER'];
  
  
   if(isset($_POST['deleteEmails'])){
       
       if ( !check_admin_referer( 'action_settings_add_edit','queue_and_delete_subsciber')){

            wp_die('Security check fail'); 
         }
         
        global $wpdb;
        $subscribersSelectedEmails=$_POST['ckboxs'];
        $mass_email_queue=get_option('mass_email_queue_news_subscriber');
        foreach($subscribersSelectedEmails as $em){
         
            
             if($em!=""){
              
                $query = "delete from  ".$wpdb->prefix."nl_subscriptions where email='$em'";
                $wpdb->query($query); 
                 if(is_array($mass_email_queue)){
                     
                    $key=(int)array_search($em,$mass_email_queue);
                    if(array_search($em,$mass_email_queue)>=0){
                      
                       unset($mass_email_queue[$key]);
                    }
                 }   
             }         
          
         }
         
         update_option( 'mass_email_subscribers_succ', __( 'Selected subscribers deleted successfully.','email-subscription-popup') );
         update_option('mass_email_queue_news_subscriber',$mass_email_queue);  
         echo "<script>location.href='".$referer."';</script>";   
         exit;
       
   }    
   else if(isset($_POST['resetemailqueue'])){
       
      if ( !check_admin_referer( 'action_settings_add_edit','queue_and_delete_subsciber')){

            wp_die('Security check fail'); 
         } 
      update_option('mass_email_queue_news_subscriber',false); 
      update_option( 'mass_email_subscribers_succ', __( 'Email queue reseted successfully.','email-subscription-popup')); 
      $setacrionpage='admin.php?page=email_subscription_popup_subscribers_management';
      echo "<script>location.href='".$setacrionpage."';</script>"; 
      exit;
       
   }
   $lastaccessto=$_SERVER['QUERY_STRING'];
   
   parse_str($lastaccessto);
   
    if(isset($_POST['sendEmailAll'])){
   	
   	 global $wpdb;
   	 
   	
   	 
   	 $query="SELECT email as emails from ".$wpdb->prefix."nl_subscriptions where is_subscribed=1";
   	 
   	 $emails=$wpdb->get_results($query,'OBJECT');
         $convertToString='';   
   	 $count=0;
   	 foreach($emails as $mail){
              
   	 	$convertToString.=$mail->emails.",\n";
                $count++;
   	 }
   	 $convertToString=trim($convertToString,",\n");
            
   }
   else{
        if(isset($_POST['sendEmailQueue'])){

            $convertToString=$_POST['queueemails'];

        }
        else{
             $subscribersSelectedEmails=$_POST['ckboxs'];
             $convertToString=implode(",\n",$subscribersSelectedEmails); 
       } 
   }
 ?>    
<h3><?php echo __( 'Send Email To Newsletter Subscribers','email-subscription-popup');?> </h3>  
<?php  $url = plugin_dir_url(__FILE__);
       $urlJS=$url."js/jqueryValidate.js";
       $tinymceJS=$url."ckeditorReq/";
       $urlCss=$url."/css/styles.css";
       
 ?> 
 <script src="<?php echo $urlJS; ?>"></script>
 
 <link rel='stylesheet' href='<?php echo $urlCss; ?>' type='text/css' media='all' />

 <form name="frmSendEmailsToUserSend" id='frmSendEmailsToUserSend' method="post" action="" >
<input type="hidden" value="sendEmailSend" name="action"> 
<?php wp_nonce_field('action_settings_add_edit','sendEmailSend'); ?>  
<input type="hidden" value="<?php echo @$entrant; ?>" name="entrant"> 
<input type="hidden" value="<?php echo @$setPerPage; ?>" name="setPerPage"> 
<input type="hidden" value="<?php echo @$searchuser; ?>" name="searchuser"> 
<table class="form-table" style="width:100%" >
<tbody>
   
  <tr valign="top" id="subject">
     <th scope="row" style="width:30%;text-align: right;"><?php echo __( 'Subject','email-subscription-popup');?> *</th>
     <td>    
        <input type="text" id="email_subject" name="email_subject"  class="valid" size="70">
        <div style="clear: both;"></div><div></div>
      </td>
   </tr>
   <tr valign="top" id="subject">
     <th scope="row" style="width:30%;text-align: right"><?php echo __( 'Email From Name','email-subscription-popup');?> *</th>
     <td>    
        <input type="text" id="email_From_name" name="email_From_name"  class="valid" size="70">
         <br/>(ex. admin)  
        <div style="clear: both;"></div><div></div>
       
      </td>
   </tr>
   <tr valign="top" id="subject">
     <th scope="row" style="width:30%;text-align: right"><?php echo __( 'Email From','email-subscription-popup');?> *</th>
     <td>    
        <input type="text" id="email_From" name="email_From"  class="valid" size="70">
        <br/>(ex. admin@yoursite.com) 
        <div style="clear: both;"></div><div></div>
  
      </td>
   </tr>
   <tr valign="top" id="subject">
     <th scope="row" style="width:30%;text-align: right"><?php echo  __( 'Email To','email-subscription-popup');?> *</th>
     <td>    
        <textarea id='emailTo'  name="emailTo" cols="58" rows="4"><?php echo $convertToString;?></textarea>
        <div style="clear: both;"></div><div></div>
      </td>
   </tr>
   <tr valign="top" id="subject">
     <th scope="row" style="width:30%;text-align: right"><?php echo __( 'Email Body','email-subscription-popup');?> *</th>
     <td>    
       <div class="wrap">
       <?php wp_editor( '', 'txtArea' );?>
        <input type="hidden" name="editor_val" id="editor_val" />  
        <div style="clear: both;"></div><div></div> 
        <?php echo __( 'you can use [user_full_name],[user_email],[unsubscribe_link_plain],[unsubscribe_link_html] place holder into email content','email-subscription-popup');?> 
       </div>
      </td>
   </tr>
 
   <tr valign="top" id="subject">
     <th scope="row" style="width:30%"></th>
     <td> 
       
       <input type='submit'  value='<?php echo __( 'Send Email','email-subscription-popup');?>' name='sendEmailsend' class='button-primary' id='sendEmailsend' >  
      </td>
   </tr>
   
</table>
</form>
<script type="text/javascript">

 var $n = jQuery.noConflict();  
 $n(document).ready(function() {

 $n.validator.addMethod("chkCont", function(value, element) {
                                            
                                              

              var editorcontent=tinyMCE.get('txtArea').getContent();

              if (editorcontent.length){
                return true;
              }
              else{
                 return false;
              }
         
      
        },
             "Please enter email content"
     );

$n("#frmSendEmailsToUserSend").validate({
                    errorClass: "error_admin_massemail",
                    rules: {
                                 email_subject: { 
                                        required: true
                                  },
                                  email_From_name: { 
                                        required: true
                                  },  
                                  email_From: { 
                                        required: true ,email:true
                                  }, 
                                  emailTo:{
                                      
                                     required: true 
                                  },
                                 editor_val:{
                                    chkCont: true 
                                 }  
                            
                       }, 
      
                            errorPlacement: function(error, element) {
                            error.appendTo( element.next().next());
                      }
                      
                 });
                      

  });
 
 </script> 
 <?php 
  break;
  default: 
        $url = plugin_dir_url(__FILE__); 
        $url = str_replace("\\","/",$url); 
        $urlCss=$url."/css/styles.css";
        
  ?>       
     <div style="width: 100%;">  
        <div style="float:left;width:65%;" >
                                                                                
  <link rel='stylesheet' href='<?php echo $urlCss; ?>' type='text/css' media='all' />   
  
<?php       
    global $wpdb;
    
    $query="SELECT * from ".$wpdb->prefix."nl_subscriptions where is_subscribed=1 ";
    
    if(isset($_GET['searchuser']) and $_GET['searchuser']!=''){
      $term=trim(urldecode($_GET['searchuser']));   
      $query.="  and ( name like '%$term%' or email like '%$term%'  )  " ; 
    } 
    
    $emails=$wpdb->get_results($query,'ARRAY_A');
    $totalRecordForQuery=sizeof($emails);
    $selfPage=$_SERVER['PHP_SELF'].'?page=email_subscription_popup_subscribers_management'; 
     global $wp_rewrite;
    
    $rows_per_page = 10;
    if(isset($_GET['setPerPage']) and $_GET['setPerPage']!=""){
        
       $rows_per_page=$_GET['setPerPage'];
    } 
    
    $current = (isset($_GET['entrant'])) ? ($_GET['entrant']) : 1;
    $pagination_args = array(
        'base' => @add_query_arg('entrant','%#%'),
        'format' => '',
        'total' => ceil(sizeof($emails)/$rows_per_page),
        'current' => $current,
        'show_all' => false,
        'type' => 'plain',
    );
                
    $start = ($current - 1) * $rows_per_page;
    $end = $start + $rows_per_page;
    $end = (sizeof($emails) < $end) ? sizeof($emails) : $end;
    
    $selfpage=$_SERVER['PHP_SELF'];
        
    if($totalRecordForQuery>0){
        
             
             
?>              
  <?php
                $SuccMsg=get_option('mass_email_subscribers_succ');
                update_option( 'mass_email_subscribers_succ', '' );
               
                $errMsg=get_option('mass_email_subscribers_err');
                update_option( 'mass_email_subscribers_err', '' );
                ?> 
                   
                <?php if($SuccMsg!=""){ echo "<div class='notice notice-success is-dismissible'><p>"; echo $SuccMsg; echo "</p></div>";$SuccMsg="";}?>
                 <?php if($errMsg!=""){ echo "<div class='notice notice-error is-dismissible' ></p>"; _e($errMsg); echo "</p></div>";$errMsg="";}?>
              
                <h3><?php echo __( 'Send Email To Newsletter Subscribers','email-subscription-popup');?></h3>
                <?php
                    $setacrionpage='admin.php?page=email_subscription_popup_subscribers_management';
                    
                    if(isset($_GET['entrant']) and $_GET['entrant']!=""){
                     $setacrionpage.='&entrant='.$_GET['entrant'];   
                    }
                
                    if(isset($_GET['setPerPage']) and $_GET['setPerPage']!=""){
                     $setacrionpage.='&setPerPage='.$_GET['setPerPage'];   
                    }
                    
                    $seval="";
                    if(isset($_GET['searchuser']) and $_GET['searchuser']!=""){
                     $seval=trim($_GET['searchuser']);   
                    }
                   
                ?>
                <div style="padding-top:5px;padding-bottom:5px"><b><?php echo __( 'Search User','email-subscription-popup');?> : </b><input type="text" value="<?php echo $seval;?>" id="searchuser" name="searchuser">&nbsp;<input type='submit'  value='<?php echo __( 'Search Subscribers','email-subscription-popup');?>' name='searchusrsubmit' class='button-primary' id='searchusrsubmit' onclick="SearchredirectTO();" >&nbsp;<input type='submit'  value='<?php echo __( 'Reset Search','email-subscription-popup');?>' name='searchreset' class='button-primary' id='searchreset' onclick="ResetSearch();" ></div>  
                <script type="text/javascript" >
                 function SearchredirectTO(){
                   var redirectto='<?php echo $setacrionpage; ?>';
                   var searchval=jQuery('#searchuser').val();
                   redirectto=redirectto+'&searchuser='+jQuery.trim(encodeURIComponent(searchval));    
                   window.location.href=redirectto;
                 }
                function ResetSearch(){
                    
                     var redirectto='<?php echo $setacrionpage; ?>';
                     window.location.href=redirectto;
                     exit;
                }
                </script>
               <form method="post" action="" id="sendemail" name="sendemail">
                <input type="hidden" value="sendEmailForm" name="action" id="action">
                
              <table class="widefat fixed" cellspacing="0" style="width:97% !important" >
                <thead>
                <tr>
                        <th scope="col" id="name" class="manage-column column-name" style=""><input onclick="chkAll(this)" type="checkbox" name="chkallHeader" id='chkallHeader'>&nbsp;<?php echo __( 'Select All Emails','email-subscription-popup');?></th>
                        <th scope="col" id="name" class="manage-column column-name" style=""><?php echo __( 'Name','email-subscription-popup');?></th>
                        
                </tr>
                </thead>

                <tfoot>
                <tr>
                        <th scope="col" id="name" class="manage-column column-name" style=""><input onclick="chkAll(this)" type="checkbox" name="chkallfooter" id='chkallfooter'>&nbsp;<?php  echo __( 'Select All Emails','email-subscription-popup');?></th>
                        <th scope="col" id="name" class="manage-column column-name" style=""><?php  echo __( 'Name','email-subscription-popup');?></th>
                        
                        
                </tr>
                </tfoot>

                <tbody id="the-list" class="list:cat">
               <?php                 
                    $mass_email_queue=array();               
                    if(get_option('mass_email_queue_news_subscriber')!=false and is_array(get_option('mass_email_queue_news_subscriber')))
                      $mass_email_queue=get_option('mass_email_queue_news_subscriber');
                                            
                         for ($i=$start;$i < $end ;++$i ) 
                         {
                             
                            if($emails[$i]!=""){ 
                           
                               $userId=$emails[$i]['id'];
                               $name=$emails[$i]['name'];
                               $email=$emails[$i]['email'];
                               
                               if(in_array($email,$mass_email_queue)) 
                                 $checked="checked='checked'";
                               else
                                 $checked="";
                                 
                           
                               echo"<tr class='iedit alternate'>
                                <td  class='name column-name' style='border:1px solid #DBDBDB;padding-left:13px;'><input type='checkBox' name='ckboxs[]' $checked  value='".$email."'>&nbsp;".$email."</td>";
                                echo "<td  class='name column-name' style='border:1px solid #DBDBDB;'> ".stripslashes($name)."</td>";
                                echo "</tr>";
                            }   
                               
                         }  
                           
                     
                       
                   ?>  
                 </tbody>       
                </table>
                <table>
                  <tr>
                    <td>
                      <?php
                       if(sizeof($emails)>0){
                         echo "<div class='pagination' style='padding-top:10px'>";
                         echo paginate_links($pagination_args);
                         echo "</div>";
                        }
                        
                       ?>
                
                    </td>
                    <td>
                      <b>&nbsp;&nbsp;<?php echo __( 'Per Page :','email-subscription-popup');?> </b>
                      <?php
                        $setPerPageadmin='admin.php?page=email_subscription_popup_subscribers_management';
                        /*if(isset($_GET['entrant']) and $_GET['entrant']!=""){
                            $setPerPageadmin.='&entrant='.(int)trim($_GET['entrant']);
                        }*/
                        $setPerPageadmin.='&setPerPage=';
                      ?>
                      <select name="setPerPage" onchange="document.location.href='<?php echo $setPerPageadmin;?>' + this.options[this.selectedIndex].value + ''">
                        <option <?php if($rows_per_page=="10"): ?>selected="selected"<?php endif;?>  value="10">10</option>
                        <option <?php if($rows_per_page=="20"): ?>selected="selected"<?php endif;?> value="20">20</option>
                        <option <?php if($rows_per_page=="30"): ?>selected="selected"<?php endif;?>value="30">30</option>
                        <option <?php if($rows_per_page=="40"): ?>selected="selected"<?php endif;?> value="40">40</option>
                        <option <?php if($rows_per_page=="50"): ?>selected="selected"<?php endif;?> value="50">50</option>
                        <option <?php if($rows_per_page=="60"): ?>selected="selected"<?php endif;?> value="60">60</option>
                        <option <?php if($rows_per_page=="70"): ?>selected="selected"<?php endif;?> value="70">70</option>
                        <option <?php if($rows_per_page=="80"): ?>selected="selected"<?php endif;?> value="80">80</option>
                        <option <?php if($rows_per_page=="90"): ?>selected="selected"<?php endif;?> value="90">90</option>
                        <option <?php if($rows_per_page=="100"): ?>selected="selected"<?php endif;?> value="100">100</option>
                        <option <?php if($rows_per_page=="500"): ?>selected="selected"<?php endif;?> value="500">500</option>
                        <option <?php if($rows_per_page=="1000"): ?>selected="selected"<?php endif;?> value="1000">1000</option>
                        <option <?php if($rows_per_page=="2000"): ?>selected="selected"<?php endif;?> value="2000">2000</option>
                        <option <?php if($rows_per_page=="3000"): ?>selected="selected"<?php endif;?> value="3000">3000</option>
                        <option <?php if($rows_per_page=="4000"): ?>selected="selected"<?php endif;?> value="4000">4000</option>
                        <option <?php if($rows_per_page=="5000"): ?>selected="selected"<?php endif;?> value="5000">5000</option>
                      </select>  
                    </td>
                  </tr>
                </table>
                <table> 
                    <tr>
                    <td class='name column-name' style='padding-top:15px;padding-left:10px;'>
                       
                         <script type="text/javascript">
                        function sendEmailToAll(obj){

                      	  var txt;
                      	  var r = confirm("<?php echo __( 'It is not recommaded to send email to all at once as there is always hosting server limit for send emails hourly basis. Most of hosting providers allow 250 emails per hour. Please upgrade to pro version and use cron job newsletter to send email automatically. Do you still want to continue ?','email-subscription-popup');?>");
                      	  if (r == true) {
                      	     return true;
                      	  } else {
                      		return false;
                      	  }
                      	  	  

                        }
                        </script>
                        <?php wp_nonce_field('action_settings_add_edit','queue_and_delete_subsciber'); ?> 
                        <input onclick="return validateSendEmailAndDeleteEmail(this)" type='submit' value='<?php echo __( 'Send Email To Selected Subscribers','email-subscription-popup');?>' name='sendEmail' class='button-primary' id='sendEmail' >&nbsp;<input onclick="return sendEmailToAll(this)" type='submit' value='<?php echo __( 'Send Email To All Subscribers','email-subscription-popup');?>' name='sendEmailAll' class='button-primary' id='sendEmailAll' >&nbsp;<input onclick="return validateSendEmailAndDeleteEmail(this)" type='submit' value='<?php echo __( 'Delete Selected Subscribers','email-subscription-popup');?>' name='deleteEmails' class='button-primary' id='deleteEmails' ></td>
                    </tr>
                   
                    <?php
                        $mass_email_queue=get_option('mass_email_queue_news_subscriber');
                        if($mass_email_queue!=false and $mass_email_queue!=null ){ 
                           if(is_array($mass_email_queue)){ ?>
                            <tr>
                               <td>
                                  <h3><?php echo __( 'Emails In Queue','email-subscription-popup');?></h3>
                                  <textarea readonly="readonly" name="queueemails" id="queueemails" cols="70" rows="10"><?php
                                     foreach($mass_email_queue as $email_){
                                      echo "$email_".",\n";   
                                     }
                                    ?></textarea>
                                    <br/>
                                     <input type="hidden" name="uncheckedemails" id="uncheckedemails" value="">
                                     <input  type='submit' value='<?php echo __( 'Send Email To Subscribers In Queue','email-subscription-popup');?>' name='sendEmailQueue' class='button-primary' id='sendEmailQueue' >&nbsp;<input  type='submit' value='<?php echo __( 'Reset Email Queue','email-subscription-popup');?>' name='resetemailqueue' class='button-primary' id='resetemailqueue' >
                               </td>
                            </tr>
                        <?php } 
                           }
                        ?>    
                </table>
                </form>  
      
                  
     <?php
                   
      }
     else
      {
             echo '<center><div style="padding-bottom:50pxpadding-top:50px;"><h3>'.__( 'No Email Subscription Found','email-subscription-popup').'</h3></div></center>';
             
            //echo '<center><div style="padding-bottom:50pxpadding-top:50px;"><h3><a href="admin.php?page=email_subscription_popup_subscribers_management">Click Here To Continue..</a></h3></div></center>';
      ?>
          <?php
                             $exportUrl=plugin_dir_url(__FILE__);
                             $exportUrl.='export_subscribers.php';
                             $importUrl = admin_url( 'admin.php?page=email_subscription_popup_subscribers_management&action=importform' );
                             $subscriberUlr = admin_url( 'admin.php?page=email_subscription_popup_subscribers_management' );    
                            ?>
              
     <?php        
      } 
     ?>
     </div>
    <div id="postbox-container-1" class="postbox-container" style="float:right;width:35%;margin-top: 50px" > 

           <div class="postbox"> 
               <center><h3 class="hndle"><span></span><?php echo __( 'Access All Themes In One Price','email-subscription-popup');?></h3> </center>
               <div class="inside">
                   <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="<?php echo plugins_url( 'images/300x250.gif', __FILE__ ) ;?>" width="250" height="250"></a></center>

                   <div style="margin:10px 5px">

                   </div>
               </div></div>
           <div class="postbox"> 
               <center><h3 class="hndle"><span></span><?php echo __( 'Google For Business','email-subscription-popup');?></h3> </center>
               <div class="inside">
                   <center><a target="_blank" href="https://goo.gl/OJBuHT"><img style="max-width:350px" src="<?php echo plugins_url( 'images/gsuite_promo.png', __FILE__ ) ;?>" width="" height="250" border="0"></a></center>
                   <div style="margin:10px 5px">
                   </div>
               </div></div>

       </div>
<div class="clear">
  </div>             

    <?php 
     break;
     
  } 
 
?>
 <script type="text/javascript" >
 
 jQuery("input[name='ckboxs[]']").click(function() {
    uncheckedmanagement(this); 
       
});

function uncheckedmanagement(elementset){
   
     //alert(jQuery(this).is(':checked'));
     
     if(jQuery("#uncheckedemails").length>0){
        var hiddenvals=jQuery("#uncheckedemails").val();
     }
     else
       hiddenvals="|||";
       
     var emailval=jQuery(elementset).val();
     var emailsUn= hiddenvals.split('|||');
     
     if(jQuery(elementset).is(':checked')){
         
         if(jQuery.isArray(emailsUn)==true){
             
            emailsUn.splice(jQuery.inArray(emailval, emailsUn),1); 
            var strconvert=emailsUn.join('|||'); 
            jQuery("#uncheckedemails").val(strconvert); 
         }
        else{
            
             var addtohidden=emailval.toString()+'|||';
             jQuery("#uncheckedemails").val(addtohidden);
        }  
         
     }
     else{
            
            if(jQuery.isArray(emailsUn)==true){
                
                if(jQuery.inArray(emailval, emailsUn)<=0){
                    emailsUn.push(emailval);      
                    var strconvert=emailsUn.join('|||');             
                    jQuery("#uncheckedemails").val(strconvert); 
                }
                
            }
           else{
                    var addtohidden=emailval.toString()+'|||';
                    jQuery("#uncheckedemails").val(addtohidden);
               
           }         
     }
     
       
}

  function chkAll(id){
  
  if(id.name=='chkallfooter'){
  
    var chlOrnot=id.checked;
    document.getElementById('chkallHeader').checked= chlOrnot;
   
  }
 else if(id.name=='chkallHeader'){ 
  
      var chlOrnot=id.checked;
     document.getElementById('chkallfooter').checked= chlOrnot;
  
   }
 
     if(id.checked){
     
          var objs=document.getElementsByName("ckboxs[]");
           
           for(var i=0; i < objs.length; i++)
          {
             objs[i].checked=true;
              uncheckedmanagement(objs[i]);
            }

     
     } 
    else {

          var objs=document.getElementsByName("ckboxs[]");
           
           for(var i=0; i < objs.length; i++)
          {
              objs[i].checked=false;
              uncheckedmanagement(objs[i]);
            }  
      } 
  } 
  
  function validateSendEmailAndDeleteEmail(idobj){
       
       var objs=document.getElementsByName("ckboxs[]");
       var ischkBoxChecked=false;
       for(var i=0; i < objs.length; i++){
         if(objs[i].checked==true){
         
             ischkBoxChecked=true;
             break;
           }
       
        }  
      
      if(ischkBoxChecked==false)
      {
         if(idobj.name=='sendEmail' || idobj.name=='sendEmailqueue' || idobj.name=='exportSelected'){
         alert('<?php echo __( 'Please select atleast one email.','email-subscription-popup');?>')  ;
         return false;
        
         }
        else if(idobj.name=='deleteEmails') 
         {
            alert('<?php echo __( 'Please select atleast one email to delete.','email-subscription-popup');?>')  
             return false;  
         }
      }
     else{
         if(idobj.name=='deleteEmails') {
             
                var r = confirm("<?php echo __( 'Are you sure to delete selected subscribers?','email-subscription-popup');?>");
                if (r == true) {
                    return true;
                }else{

                    return false;
                }

            }
       
        }
        
  } 
     
  </script>
 
<?php  
   
}

class nksnewslettersubscriber extends WP_Widget {

        
        
        function __construct() {

            $widget_ops = array('classname' => 'nksnewslettersubscriber', 'description' => 'Nks WordPress Newsletter');
            parent::__construct('nksnewslettersubscriber', 'Newsletter Subscribe',$widget_ops);
        }

        function widget( $args, $instance ) {

            if(is_array($args)){

                extract( $args );
            }

            $Heading = apply_filters('widget_title', empty( $instance['Heading'] ) ? 'Subscribe to our newsletter' :$instance['Heading']);   
            include_once(ABSPATH . WPINC . '/feed.php');
            echo @$before_widget;
            echo @$before_title.$Heading.$after_title;   
            $Subheading=empty( $instance['Subheading'] ) ? 'Want to be notified when our article is published? Enter your email address and name below to be the first to know.' :$instance['Subheading']; 
            $EmailLabel=empty( $instance['EmailLabel'] ) ? 'Email' :$instance['EmailLabel']; 
            $NameLabel=empty( $instance['NameLabel'] ) ? 'Name' :$instance['NameLabel']; 
            $SubmitButtonLabel=empty( $instance['SubmitButtonLabel'] ) ? 'SIGN UP FOR NEWSLETTER NOW' :$instance['SubmitButtonLabel']; 
            $RequiredFieldMessage=empty( $instance['RequiredFieldMessage'] ) ? 'This field is required.' :$instance['RequiredFieldMessage']; 
            $InvalidEmailMessage=empty( $instance['InvalidEmailMessage'] ) ? 'Please enter valid email address.' :$instance['InvalidEmailMessage']; 
            $InvalidRequestMessage=empty( $instance['InvalidRequestMessage'] ) ? 'Invalid request.' :$instance['InvalidRequestMessage']; 
            $EmailExistMessage=empty( $instance['EmailExistMessage'] ) ? 'This email is already exist.' :$instance['EmailExistMessage']; 
            $SuccessMessage=empty( $instance['SuccessMessage'] ) ? 'You have successfully subscribed to our Newsletter!' :$instance['SuccessMessage']; 
            $WaitMessage=empty( $instance['WaitMessage'] ) ? 'Please wait...' :$instance['WaitMessage']; 
            $ShowNameField=empty( $instance['ShowNameField'] ) ? 1 :$instance['ShowNameField']; 
            $show_agreement=empty( $instance['show_agreement'] ) ? 0 :$instance['show_agreement']; 
            $agreement_text=empty( $instance['agreement_text'] ) ? 'I agree to <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>' :$instance['agreement_text']; 
            $agreement_error=empty( $instance['agreement_error'] ) ? 'Please read and agree to our terms & conditions.' :$instance['agreement_error']; 
            $imgUrl=plugin_dir_url(__FILE__)."images/";
            $loader=$imgUrl.'AjaxLoader.gif';
            $rand=uniqid('filed_');
            $rand_func=uniqid('fun');
            
           ?>
 
        <div class="<?php echo $rand;?>_AjaxLoader ajaxLoaderWidget"  id="<?php echo $rand;?>_AjaxLoader"><img src="<?php echo $loader;?>"/><?php echo $WaitMessage;?></div>
         <div class="<?php echo $rand;?>_myerror_msg myerror_msg" id="<?php echo $rand;?>_myerror_msg"></div>         
         <div class="<?php echo $rand;?>_mysuccess_msg mysuccess_msg" id="<?php echo $rand;?>_mysuccess_msg"></div>
       <div class="Nknewsletter_description"><?php echo $Subheading;?></div>
         <div class="Nknewsletter-widget">
             <input type="text" name="<?php echo $rand;?>_youremail" id="<?php echo $rand;?>_youremail" class="Nknewsletter_email"  value="<?php echo $EmailLabel;?>" onfocus="return clearInput(this,'<?php echo $EmailLabel;?>');" onblur="restoreInput(this,'<?php echo $EmailLabel;?>')"/>
             <div class="" id="<?php echo $rand;?>_errorinput_email"></div>
             
             <?php if($ShowNameField=="1"):?>
                <div class="Nknewsletter_space" id="<?php echo $rand;?>_name_Nknewsletter_space" ></div>
                <input type="text" name="<?php echo $rand;?>_yourname" id="<?php echo $rand;?>_yourname" class="Nknewsletter_name" value="<?php echo $NameLabel;?>" onfocus="return clearInput(this,'<?php echo $NameLabel;?>');" onblur="restoreInput(this,'<?php echo $NameLabel;?>')" />
                <div class="errorinput_widget" id="<?php echo $rand;?>_errorinput_name"></div>
                <div class="Nknewsletter_space" id="<?php echo $rand;?>_name_Nknewsletter_space" ></div>   
             <?php else:?>
                <div class="Nknewsletter_space" id="<?php echo $rand;?>_name_Nknewsletter_space" ></div>
             <?php endif;?>
             
             <?php if($show_agreement=="1"):?>
                <input class="nk_newsletter_agree" style="display:inline-block" type="checkbox"  id="<?php echo $rand;?>_agree" value="1" name="<?php echo $rand;?>_agree" /><span class="nk_newslteer_agree_term"> <?php echo html_entity_decode ($agreement_text);?></span>
                <div style="clear:both"></div>
                <div class="errorinput_widget" id="<?php echo $rand;?>_errorinput_agree"></div>
                
             <?php else:?>
                <div class="Nknewsletter_space" id="<?php echo $rand;?>_agree_Nknewsletter_space" ></div>
             <?php endif;?>
                
             <div class="Nknewsletter_space" id="<?php echo $rand;?>submit_space" ></div>
             <input class="Nknewsletter_space_submit" type="submit" value="<?php echo $SubmitButtonLabel;?>" onclick="return <?php echo $rand_func;?>_submit_newsletter();" name="<?php echo $rand;?>_submit" />
         </div>
 <script>
     
    function <?php echo $rand_func;?>_submit_newsletter(){        
        
           
            var emailAdd=$n.trim($n("#<?php echo $rand;?>_youremail").val());
            var yourname=$n.trim($n("#<?php echo $rand;?>_yourname").val());
            
            var returnval=false;
            var isvalidName=false;
            var isvalidEmail=false;
            var isagree=false;
            
              if($n("#<?php echo $rand;?>_yourname").length >0){
                
                
                var yourname=$n.trim($n("#<?php echo $rand;?>_yourname").val());
                
                if(yourname!="" && yourname!=null && yourname.toLowerCase()!='<?php echo $NameLabel;?>'.toLowerCase()){

                    var element=$n("#<?php echo $rand;?>_yourname").next().next();
                    isvalidName=true;
                    $n(element).html('');
                }
                else{
                        var element=$n("#<?php echo $rand;?>_yourname").next().next();
                        $n(element).html('<div class="image_error"><?php echo $RequiredFieldMessage;?></div>');
                        $n("#<?php echo $rand;?>_name_Nknewsletter_space").css( { marginBottom : "20px" } );
                       // emailAdd=false;

                }
                
               
            }
            else{
                   isvalidName=true;
                
            }
           
           if($n("#<?php echo $rand;?>_agree").length >0){
           
                if($n("#<?php echo $rand;?>_agree").is(':checked')){

                    var element=$n("#<?php echo $rand;?>_agree").next().next();
                    $n(element).html('');    
                    isagree=true;
                }
                else{


                     var element=$n("#<?php echo $rand;?>_agree").next().next();
                     $n(element).html('<div class="image_error"><?php echo $agreement_error;?></div>');
                     $n("#<?php echo $rand;?>_email_Nknewsletter_space").css( { marginBottom : "0px" } );
                     isagree=false;

                }
           }
           else{
           
                isagree=true;
           
           }
           
           
           if(emailAdd!=""){
               
               
                var element=$n("#<?php echo $rand;?>_youremail").next().next();
                if(emailAdd.toLowerCase()=='<?php echo $EmailLabel;?>'.toLowerCase()){
                    
                    $n(element).html('<div  class="image_error"><?php echo $RequiredFieldMessage;?></div>');
                    isvalidEmail=false;
                    
                    $n("#<?php echo $rand;?>_email_Nknewsletter_space").css( { marginBottom : "0px" } );
                    
                }else{
                    
                         var JsRegExPatern = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
                         if(JsRegExPatern.test(emailAdd)){
                            
                            isvalidEmail=true;
                            $n("#<?php echo $rand;?>_email_Nknewsletter_space").css( { marginBottom : "20px" } );
                            $n(element).html('');    
                            
                        }else{
                            
                             var element=$n("#<?php echo $rand;?>_youremail").next().next();
                             $n(element).html('<div class="image_error"><?php echo $InvalidEmailMessage;?></div>');
                             $n("#<?php echo $rand;?>_email_Nknewsletter_space").css( { marginBottom : "0px" } );
                             isvalidEmail=false;
                            
                        }
                        
                }
               
           }else{
               
                    var element=$n("#<?php echo $rand;?>_yourname").next().next();
                    $n(element).html('<div class="image_error"><?php echo $RequiredFieldMessage;?></div>');
                    $n("#<?php echo $rand;?>_email_Nknewsletter_space").css( { marginBottom : "0px" } );
                    isvalidEmail=false;
               
           } 
            
                if(isvalidName==true && isvalidEmail==true && isagree==true){
                
                $n("#<?php echo $rand;?>_name_Nknewsletter_space").css( { marginBottom : "20px" } );
                $n("<?php echo $rand;?>_email_Nknewsletter_space").css( { marginBottom : "20px" } );
                
                $n("#<?php echo $rand;?>_AjaxLoader").show();
                $n('#<?php echo $rand;?>_mysuccess_msg').html('');
                $n('#<?php echo $rand;?>_mysuccess_msg').hide();
                $n('#<?php echo $rand;?>_myerror_msg').html('');
                $n('#<?php echo $rand;?>_myerror_msg').hide();
                
                var nonce ='<?php echo wp_create_nonce('newsletter-nonce'); ?>';
                var url = '<?php echo plugin_dir_url(__FILE__);?>';  
                var email=$n("#<?php echo $rand;?>_youremail").val(); 
                var name="";
                if($n("#<?php echo $rand;?>_yourname").length >0){
                    
                     name =$n("#<?php echo $rand;?>_yourname").val();  
                } 
                var str="action=store_email&email="+email+'&name='+name+'&is_agreed='+isagree+'&sec_string='+nonce;
                $n.ajax({
                   type: "POST",
                   url: '<?php echo admin_url('admin-ajax.php'); ?>',
                   data:str,
                   async:true,
                   success: function(msg){
                       if(msg!=''){
                           
                             var result=msg.split("|"); 
                             if(result[0]=='success'){
                                 
                                 $n("#<?php echo $rand;?>_AjaxLoader").hide();
                                 $n('.<?php echo $rand;?>_mysuccess_msg').html(result[1]);
                                 $n('.<?php echo $rand;?>_mysuccess_msg').show(); 
                                 setTimeout(function(){
                                  
                                      $n('#<?php echo $rand;?>_mysuccess_msg').hide();
                                      $n('#<?php echo $rand;?>_mysuccess_msg').html('');
                                      $n("#<?php echo $rand;?>_youremail").val('<?php echo $EmailLabel;?>');
                                      $n("#<?php echo $rand;?>_yourname").val('<?php echo $NameLabel;?>');

                                     
                                },2000);
                                 
                                 
                                 
                                 
                             }
                             else{
                                   $n("#<?php echo $rand;?>_AjaxLoader").hide(); 
                                   $n('#<?php echo $rand;?>_myerror_msg').html(result[1]);
                                   $n('#<?php echo $rand;?>_myerror_msg').show();
                                   setTimeout(function(){
                                  
                                      $n('#<?php echo $rand;?>_myerror_msg').hide();
                                      $n('#<?php echo $rand;?>_myerror_msg').html('');
                                      
                                    

                                     
                                },2000);
                                
                             }
                           
                       }
                 
                    }
                }); 
                
            }
           
            
        
      
              
      }
    </script>
 <?php           
            echo $after_widget; 
        }



        function update( $new_instance, $old_instance ) {


            $instance = $old_instance;
            $instance['Heading'] = strip_tags($new_instance['Heading']);
            $instance['Subheading'] = strip_tags($new_instance['Subheading']);
            $instance['EmailLabel'] = strip_tags($new_instance['EmailLabel']);
            $instance['NameLabel'] = strip_tags($new_instance['NameLabel']);
            $instance['SubmitButtonLabel'] = strip_tags($new_instance['SubmitButtonLabel']);
            $instance['RequiredFieldMessage'] = strip_tags($new_instance['RequiredFieldMessage']);
            $instance['InvalidEmailMessage'] = strip_tags($new_instance['InvalidEmailMessage']);
            $instance['InvalidRequestMessage'] = strip_tags($new_instance['InvalidRequestMessage']);
            $instance['EmailExistMessage'] = strip_tags($new_instance['EmailExistMessage']);
            $instance['SuccessMessage'] = strip_tags($new_instance['SuccessMessage']);
            $instance['WaitMessage'] = strip_tags($new_instance['WaitMessage']);
            $instance['ShowNameField'] = strip_tags($new_instance['ShowNameField']);
            $instance['show_agreement'] = strip_tags($new_instance['show_agreement']);
            $instance['agreement_text'] = trim(htmlentities(strip_tags(stripslashes( $new_instance['agreement_text']),'<a><b><p><strong><em><i>'),ENT_QUOTES));  
            $instance['agreement_error'] = strip_tags($new_instance['agreement_error']);
          
            return $instance;


        }
        function form( $instance ) {

            //Defaults
            $instance = wp_parse_args( (array) $instance, array(
                                                                'Heading'=>'Subscribe to our newsletter',
                                                                'Subheading'=>'Want to be notified when our article is published? Enter your email address and name below to be the first to know.',
                                                                'EmailLabel'=>'Email',
                                                                'NameLabel' => 'Name',
                                                                'SubmitButtonLabel' => 'SIGN UP FOR NEWSLETTER NOW',
                                                                'RequiredFieldMessage' => 'This field is required.',
                                                                'InvalidEmailMessage' => 'Please enter valid email address.',
                                                                'InvalidRequestMessage'=>'Invalid request.',
                                                                'EmailExistMessage'=>'This email is already exist.',
                                                                'SuccessMessage'=>'You have successfully subscribed to our Newsletter!',
                                                                'WaitMessage'=>'Please wait...',
                                                                'ShowNameField'=>"1",
                                                                'show_agreement'=>'0',
                                                                'agreement_text'=>'I agree to <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>',
                                                                'agreement_error'=>'Please read and agree to our terms & conditions.',
          
                                                            )
                                        );
            
            
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('ShowNameField'); ?>"><b><?php echo __( 'Show Name Field','email-subscription-popup');?>:</b></label><br/>
            <input <?php if($instance['ShowNameField']=='1'){?>checked="checked" <?php } ?> type="radio" name="<?php echo $this->get_field_name('ShowNameField');?>"  id="s_type_show_field_yes" value="1"> Yes
            <input <?php if($instance['ShowNameField']=='2'){?>checked="checked" <?php } ?> type="radio" name="<?php echo $this->get_field_name('ShowNameField');?>"   id="s_type_show_field_no" value="2"> No
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('show_agreement'); ?>"><b><?php echo __( 'Show Agreement','email-subscription-popup');?>:</b></label><br/>
            <input <?php if($instance['show_agreement']=='1'){?>checked="checked" <?php } ?> type="radio" name="<?php echo $this->get_field_name('show_agreement');?>"  id="s_type_show_agreement_yes" value="1"> Yes
            <input <?php if($instance['show_agreement']=='2'){?>checked="checked" <?php } ?> type="radio" name="<?php echo $this->get_field_name('show_agreement');?>"   id="s_type_show_agreement_no" value="2"> No
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('Heading'); ?>"><b><?php echo __( 'Heading:','email-subscription-popup');?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id('Heading'); ?>"
                name="<?php echo $this->get_field_name('Heading'); ?>" type="text" value="<?php echo $instance['Heading']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('Subheading'); ?>"><b><?php echo __( 'Subheading:','email-subscription-popup');?></b></label><br/>
            <textarea rows="4" cols="30" name="<?php echo $this->get_field_name('Subheading');?>" id="Subheading"><?php echo $instance['Subheading'];?></textarea>
        </p>
         <p>
            <label for="<?php echo $this->get_field_id('EmailLabel'); ?>"><b><?php echo __( 'Email Label:','email-subscription-popup');?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id('EmailLabel'); ?>"
                name="<?php echo $this->get_field_name('EmailLabel'); ?>" type="text" value="<?php echo $instance['EmailLabel']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('NameLabel'); ?>"><b><?php echo __( 'Name Label:','email-subscription-popup');?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id('NameLabel'); ?>"
                name="<?php echo $this->get_field_name('NameLabel'); ?>" type="text" value="<?php echo $instance['NameLabel']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('SubmitButtonLabel'); ?>"><b><?php echo __( 'Submit Button Label:','email-subscription-popup');?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id('SubmitButtonLabel'); ?>"
                name="<?php echo $this->get_field_name('SubmitButtonLabel'); ?>" type="text" value="<?php echo $instance['SubmitButtonLabel']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('RequiredFieldMessage'); ?>"><b><?php echo __( 'Required Field Message:','email-subscription-popup');?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id('RequiredFieldMessage'); ?>"
                name="<?php echo $this->get_field_name('RequiredFieldMessage'); ?>" type="text" value="<?php echo $instance['RequiredFieldMessage']; ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('InvalidEmailMessage'); ?>"><b><?php echo __( 'Invalid Email Message:','email-subscription-popup');?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id('InvalidEmailMessage'); ?>"
                name="<?php echo $this->get_field_name('InvalidEmailMessage'); ?>" type="text" value="<?php echo $instance['InvalidEmailMessage']; ?>" />
        </p>
         <p>
            <label for="<?php echo $this->get_field_id('InvalidRequestMessage'); ?>"><b><?php echo __( 'Invalid Request Message:','email-subscription-popup');?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id('InvalidRequestMessage'); ?>"
                name="<?php echo $this->get_field_name('InvalidRequestMessage'); ?>" type="text" value="<?php echo $instance['InvalidRequestMessage']; ?>" />
        </p>
         <p>
            <label for="<?php echo $this->get_field_id('EmailExistMessage'); ?>"><b><?php echo __( 'Email Exist Message:','email-subscription-popup');?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id('EmailExistMessage'); ?>"
                name="<?php echo $this->get_field_name('EmailExistMessage'); ?>" type="text" value="<?php echo $instance['EmailExistMessage']; ?>" />
        </p>
         <p>
            <label for="<?php echo $this->get_field_id('SuccessMessage'); ?>"><b><?php echo __( 'Success Message:','email-subscription-popup');?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id('SuccessMessage'); ?>"
                name="<?php echo $this->get_field_name('SuccessMessage'); ?>" type="text" value="<?php echo $instance['SuccessMessage']; ?>" />
        </p>
         <p>
            <label for="<?php echo $this->get_field_id('WaitMessage'); ?>"><b><?php echo __( 'Wait Message:','email-subscription-popup');?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id('WaitMessage'); ?>"
                name="<?php echo $this->get_field_name('WaitMessage'); ?>" type="text" value="<?php echo $instance['WaitMessage']; ?>" />
        </p>
         <p>
            <label for="<?php echo $this->get_field_id('agreement_text'); ?>"><b><?php echo __( 'Agreement Text:','email-subscription-popup');?></b></label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('agreement_text'); ?>"
                      name="<?php echo $this->get_field_name('agreement_text'); ?>" ><?php echo $instance['agreement_text']; ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('agreement_error'); ?>"><b><?php echo __( 'Agreement Message:','email-subscription-popup');?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id('agreement_error'); ?>"
                name="<?php echo $this->get_field_name('agreement_error'); ?>" type="text" value="<?php echo $instance['agreement_error']; ?>" />
        </p>
      
        <?php
        } // function form
    } // widget class
    
    
    function store_email_callback(){

            if(isset($_POST['email']) and  isset($_POST['name']) and isset($_POST['sec_string'])){
                
                           $wp_news_letter_settings=get_option('wp_news_letter_settings'); 
                           $nonce = $_POST['sec_string'];
                           $is_agreed=sanitize_text_field($_POST['is_agreed']);
                           $is_agreed=esc_html($is_agreed);
                           if (wp_verify_nonce( $nonce, 'newsletter-nonce' ) and $is_agreed==true ) {

                                  global $wpdb;
                                   $email=sanitize_email($_POST['email']);
                                   $name=sanitize_text_field($_POST['name']);
                                   $name=esc_html($name);
                                   
                                  if(is_email($email)){
                                      
                                 
                                        $subscribed_on=date('Y-m-d h:i:s');
                                         if(function_exists('date_i18n')){

                                             $subscribed_on=date_i18n('Y-m-d'.' '.get_option('time_format') ,false,false);
                                             if(get_option('time_format')=='H:i')
                                                 $subscribed_on=date('Y-m-d H:i:s',strtotime($subscribed_on));
                                             else   
                                                 $subscribed_on=date('Y-m-d h:i:s',strtotime($subscribed_on));

                                         }

                                        $query = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'nl_subscriptions where email = %s',array($email)); 
                                        $myrow  = $wpdb->get_row($query);

                                        if(is_object($myrow)){

                                           echo 'error|'.$wp_news_letter_settings['email_exist'];

                                        }else{
                                                 try{

                                                      $key = md5(uniqid(rand(), true));
                                                      
                                                      $wpdb->insert(
                                                            $wpdb->prefix."nl_subscriptions",
                                                            array( 'name' => $name, 'email' => $email,'subscribed_on'=> $subscribed_on,'is_subscribed'=>1,'unsubs_key'=>$key),
                                                            array( '%s', '%s','%s','%d','%s' )
                                                        );
                                                       echo 'success|'.$wp_news_letter_settings['success'];         
                                                 }
                                                 catch(Exception $e){

                                                     echo 'error|'.$e->getMessage();         

                                                 }   

                                        }
                                  
                               }
                               else{
                                   
                                    echo 'error|'.$wp_news_letter_settings['iinvalidemail'] ;
                                    
                               }
                           }
                           else{

                                echo 'error|'.$wp_news_letter_settings['invalid_request'] ;
                        }      
                 }
                 else{

                      echo 'error|'.$wp_news_letter_settings['invalid_request'];
                 }
                 
                 die;
                          
    }

?>