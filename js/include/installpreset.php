<?php add_action( 'wp_ajax_cforms2_installpreset', 'cforms2_installpreset' );

function cforms2_installpreset() {
check_admin_referer( 'cforms2_installpreset' );

?>

	<p>
		<label for="cf_edit_label_select"><?php _e('Please select a predefined form:', 'cforms'); ?></label>
		<?php echo cforms2_get_form_presets(); ?>
	</p>
	<p class="ex installNote"><?php _e('By accepting and choosing OK, you will <strong>replace</strong> all your existing input fields with this new preset! If you\'re unsure about this, make a backup copy of the form first.', 'cforms'); ?></p>
<?php die();
}

### read all presets from the dir
function cforms2_get_form_presets(){
	$fullplugindir	= dirname(__FILE__);
	$presetsdir		= $fullplugindir.'/../../formpresets/';

	$list = $title	= '';
	$alldesc 		= '';
	$alldesc_i		= 0;
	$allfiles		= array();

	if ($handle = opendir($presetsdir)) {
	    while (false !== ($file = readdir($handle))) {
	        if ($file != "." && $file != ".." && filesize($presetsdir.$file) > 0)
				array_push($allfiles,$file);
	    }
	    closedir($handle);
	}
	sort($allfiles);

	$prefix =''; $prefix_i=1;
    $disp = 'block';
	foreach( $allfiles as $file ){
		if ( $fhandle = fopen($presetsdir.$file, "r") ) {
		    if (!feof($fhandle)){
		        preg_match( '/^[^"]+"(.*)"[^"]+$/', fgets($fhandle, 4096), $title );
		        preg_match( '/^[^"]+"(.*)"[^"]+$/', fgets($fhandle, 4096), $desc );
		        $alldesc .= '<span id="descInstall'.($alldesc_i++).'" style="display:'.$disp.';">'.__($desc[1],'cforms').'</span>';
		        $disp = 'none';
		    }
		    fclose($fhandle);
		}

		$newprefix = substr( __($title[1],'cforms'), 0, strpos(__($title[1],'cforms'),':') );

		if ( $newprefix <> $prefix ){
			switch( $prefix_i++ ){
				case '1': $optstyle = ' style="color:#b84141"'; break;
				case '2': $optstyle = ' style="color:#528d47"'; break;
				case '3': $optstyle = ' style="color:#435f7c"'; break;
				default: $optstyle =''; break;
			}
			$prefix = $newprefix;
		}

		$list .= '<option value="'.$file.'" '.$optstyle.'>' .__($title[1],'cforms'). '</option>';
	}
	$fullstring = '<select name="formpresets" id="formpresets">'.$list.'</select></p><p class="descPreset">'.$alldesc;
    return ($list=='')?'<select><li>'.__('Not available','cforms').'</select></li>':$fullstring;
}

?>