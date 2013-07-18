<?php
/**
 * Plugin Name: 7L ads post
 * Description: Ads in post and riplais link by profile
 * Version: 0.1
 * Author: dika
 * Author URI: http://www.tujuhlevel.com
 */
 

add_action( 'show_user_profile', 'dik_show_extra_profile_fields_ads' );
add_action( 'edit_user_profile', 'dik_show_extra_profile_fields_ads' );

function dik_show_extra_profile_fields_ads( $user ) { ?>

  <h3>Extra profile information</h3>

	<table class="form-table">

		<tr>
			<th><label for="aff_link">Affiliate Link</label></th>

			<td>
				<input type="text" name="aff_link" id="aff_link" value="<?php echo esc_attr( get_the_author_meta( 'aff_link', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please enter your Affiliate Link.</span>
			</td>
		</tr>

	</table>
<?php }


add_action( 'personal_options_update', 'dik_save_extra_profile_fields_ads' );
add_action( 'edit_user_profile_update', 'dik_save_extra_profile_fields_ads' );

function dik_save_extra_profile_fields_ads( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	
	update_usermeta( $user_id, 'aff_link', $_POST['aff_link'] );
}


 
 
if ( is_admin() ) :

add_action('init', 'dik_load_ads_post_color');
function dik_load_ads_post_color() {
	wp_enqueue_style( 'farbtastic' );
	wp_enqueue_script( 'farbtastic' );
}


$dik_ads_post_setting = array(

	array(	"type" => "open"),
	
	array(	"name" => "Admin Url Ads default",
			"desc" => "Enter a Url For ads Text.",
			"id" => "dik_admin_url_ads_txt",
			"std" => "",
			"type" => "text"),
			
	array(	"name" => "Ads Post (1)",
			"desc" => "Ubah Link menjadi <b>{link}</b>, Contoh : &lt;a href=&quot;<b>{link}</b>&quot;&gt;Link title&lt;/a&gt; ",
			"id" => "dik_ads_satu_text",
			"std" => "<a href=\"{link}\">contoh Link</a>",
			"type" => "textarea"),
			
	array(	"name" => "Ads Post (2)",
			"desc" => "Ubah Link menjadi <b>{link}</b>, Contoh : &lt;a href=&quot;<b>{link}</b>&quot;&gt;Link title&lt;/a&gt;",
			"id" => "dik_ads_dua_text",
			"std" => "<a href=\"{link}\">contoh Link</a>",
			"type" => "textarea"),
			
			
	array(	"type" => "close")

);


add_action("admin_menu", "dik_ads_createmenus");


function dik_ads_createmenus() {
	global $dik_ads_post_setting;

	if (isset($_POST['dik_serv_post_setting'])) {
		dik_seve_post_ads($dik_ads_post_setting);
    }

    add_menu_page("Ads Post", "Ads Post Setting", "administrator", "ads-7l-post", "dik_fun_page_ads_post",plugins_url( 'img/ads-icon.png' , __FILE__ ));

}

function dik_fun_page_ads_post(){
global $dik_ads_post_setting;
 if ( $_REQUEST['dik_serv_post_setting'] ) echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
?>
	<div class="wrap">
	<div id="icon-themes" class="icon32"></div><h2>Setting</h2><br/>
	<form method="post" >
	<input name="acces_themplate_option" type="hidden"  value="<?php echo rand(5, 200);?>" >
	<?php
	dik_thempate_show_form($dik_ads_post_setting);
	?>
	<p class="submit">
	<input name="dik_serv_post_setting" type="submit" class="button button-primary" value="Save changes" />    
	</p>
	</form>
<?php
	
}

function dik_seve_post_ads($opt){

	foreach ($opt as $value) 
	{
		if ($value['type'] == 'image'){
				
			if($_FILES[$value['id']]['name'] != "") {
							
				$overrides = array( 'test_form' => false);
				$imagefile=wp_handle_upload($_FILES[$value['id']], $overrides);
				$imageuploadlocation = $imagefile['url'];
				update_option($value['id'],$imageuploadlocation); 
				//echo $value['id']." ".$imageuploadlocation;
			}
						
			if ($_REQUEST[$value['id']."_delete"])
			{
				delete_option( $value['id'] );
			}
				
		} else {	
            update_option( $value['id'], $_REQUEST[ $value['id'] ] ); 
					
		}
					 
	}
}


function dik_thempate_show_form($options_t){
foreach ($options_t as $value) { 
    
	switch ( $value['type'] ) {
	
		case "open":
		?>
        <table width="100%" border="0" class="widefat" style="padding:10px;">
		
        
        
		<?php break;
		
		case "close":
		?>
		
        </table><br />
        
        
		<?php break;
		
		case "title":
		?>
		<tr>
        	<td colspan="2"><h3 style="font-family:Georgia,'Times New Roman',Times,serif;"><?php echo $value['name']; ?></h3></td>
        </tr>
                
        
		<?php break;

		case 'text':
		?>
        
        <tr>
            <td width="20%" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
            <td width="80%">
				<input style="width:400px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" /><br />
				<small><?php echo $value['desc']; ?></small>
			</td>
        </tr>

      
		<?php 
		break;
		
		case 'textarea':
		?>
        
        <tr>
            <td width="20%" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
            <td width="80%">
				<textarea name="<?php echo $value['id']; ?>" style="width:400px; height:200px;" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'] )); } else { echo $value['std']; } ?></textarea>
				<br /><small><?php echo $value['desc']; ?></small>
			</td>          
        </tr>

		<?php 
		break;
		
		case 'select':
		?>
        <tr>
            <td width="20%" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
            <td width="80%">
				<select style="width:240px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php foreach ($value['options'] as $option) { ?><option<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?></select>
				<br /><small><?php echo $value['desc']; ?></small>
			</td>
       </tr>
        
		<?php
        break;
            
		case "checkbox":
		?>
            <tr>
				<td width="20%"  valign="middle"><strong><?php echo $value['name']; ?></strong></td>
                <td width="80%"><? if(get_settings($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = ""; } ?>
                        <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
						<br /><small><?php echo $value['desc']; ?></small>
                 </td>
            </tr>
                        
     
        <?php 		break;
		
		case "textareawp":
		?>
		<tr>
			<td width="20%" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
			<td width="80%">
				<?php
				if ( get_settings( $value['id'] ) != "") { $content =  stripslashes(get_settings( $value['id'] )); } else { $content = $value['std']; }
				wp_editor($content,$value['id'],array('textarea_name' => $value['id'] , 'media_buttons' => true));
				?>
				<br /><small><?php echo $value['desc']; ?></small>
			</td>
        </tr>  
		<?php
		break;
		case "image":
		?>
		<tr>
			<td width="20%" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
			<td width="80%">
				<?php if(get_settings( $value['id'] )) {?> <img src="<?php echo get_settings( $value['id'] ); ?>" alt="" width="150px" /><br /><input type="checkbox" name="<?php echo $value['id']; ?>_delete" id="<?php echo $value['id']; ?>_delete" value="true"  /> Delete Image <br /> <?php } ?> 
				<input type="file" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="" /><br />
				<br /><small><?php echo $value['desc']; ?></small>
			</td>
        </tr>  
		<?php
		break;
	
 
} 
}
}

endif;


add_action( 'wp_enqueue_scripts', 'dik_ads_post_stylesheet' );

function dik_ads_post_stylesheet() {
	if(is_single()):
        wp_register_style( 'ads-post-style', plugins_url('ads-post.css', __FILE__) );
        wp_enqueue_style( 'ads-post-style' );
    endif;
}


function dik_post_insert_ads_post($data){
	if ( is_single() ) :
		if(get_the_author_meta('aff_link'))
			$lin_df = get_the_author_meta('aff_link');
		else
			$lin_df = stripslashes(get_option('dik_admin_url_ads_txt',false));
		
		$data_form = "<div id=\"id-ads-satu\">\n";
		$data_form .= stripslashes(get_option('dik_ads_satu_text',false));
		$data_form .= "\n</div>\n";
		$data_jadi = str_replace("{link}",$lin_df,$data_form);
		
		$data_form2 = "<div id=\"id-ads-dua\">\n";
		$data_form2 .= stripslashes(get_option('dik_ads_dua_text',false));
		$data_form2 .= "\n</div>\n";
		$data_jadi2 = str_replace("{link}",$lin_df,$data_form2);
		
		$data = $data_jadi." ".$data;
		$data = $data." ".$data_jadi2;

		return $data;
	else:
		return $data;
	endif;
}

add_filter( 'the_content', 'dik_post_insert_ads_post' );


//add widget

class Dik_Ads_Post_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'dik_ads_widget', // Base ID
			'Ads Post', // Name
			array( 'description' => 'Ads Post Widget ! ', ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		if(get_the_author_meta('aff_link'))
			$lin_df = get_the_author_meta('aff_link');
		else
			$lin_df = stripslashes(get_option('dik_admin_url_ads_txt',false));
		
		$text = $instance['ads_post_widget'];
		$text = str_replace("{link}",$lin_df,$text);
		echo $args['before_widget'];
		//echo 'Hello, World!', 'text_domain';
		echo stripslashes($text);
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'ads_post_widget' ] ) ) {
			$ads_post_widget = $instance[ 'ads_post_widget' ];
		}
		else {
			$ads_post_widget = '<a href=\"{link}\">contoh Link</a>' ;
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_name( 'ads_post_widget' ); ?>"><?php _e( 'Ads Widget :' ); ?></label> 
		<textarea rows="15" class="widefat" id="<?php echo $this->get_field_id( 'ads_post_widget' ); ?>" name="<?php echo $this->get_field_name( 'ads_post_widget' ); ?>"><?php echo stripslashes(esc_attr( $ads_post_widget )); ?>
		</textarea>
		</p>
		<p>Ubah Link menjadi <b>{link}</b> <br /> Contoh :  &lt;a href=&quot;<b>{link}</b>&quot;&gt;Link title&lt;/a&gt; </p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['ads_post_widget'] =  $new_instance['ads_post_widget'] ;

		return $instance;
	}

}

// register Widget
function dik_register_widget_ads_post() {
    register_widget( 'Dik_Ads_Post_Widget' );
}
add_action( 'widgets_init', 'dik_register_widget_ads_post' );
