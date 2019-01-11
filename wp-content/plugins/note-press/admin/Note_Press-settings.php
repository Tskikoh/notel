<?php
if (!defined('WPINC'))
	{
	die;
	}
	
function Note_Press_showMessage($message, $errormsg = false)
	{
	if ($errormsg)
		{
		echo '<div id="message" class="error">';
		}
	else
		{
		echo '<div id="message" class="updated fade">';
		}
	echo "<p><strong>$message</strong></p></div>";
	}
if (isset($_POST['NPLevel']))
{
	switch ($_POST['NPLevel'])
	{
	case 0: update_option("Note_Press_Menu_Level",0);break;
	case 1: update_option("Note_Press_Menu_Level",1);break;
	case 2: update_option("Note_Press_Menu_Level",2);break;
	case 3: update_option("Note_Press_Menu_Level",3);break;
	default:update_option("Note_Press_Menu_Level",3);
	}
	Note_Press_showMessage(__('Settings updated.','Note_Press'));
}
echo '<div class="wrap">';
echo '<table width="100%" cellpadding="5">';
echo '<tr><td width="100px"><img src="' . plugin_dir_url(__FILE__) . 'images/NPLogo1.png" align="bottom" hspace="3" width="100" height="97" /></td>';
echo '<td><h1>' . __('Note Press', 'Note_Press') . '</h1>';
echo '<p>' . __('For more information and instructions please visit our website at: ', 'Note_Press') . '<a href="http://www.datainterlock.com" target="_blank">http://www.datainterlock.com</a>
</td>
<td>
<a href="https://www.patreon.com/user?u=6474471" target="_blank">
<img src="' . plugin_dir_url(__FILE__) . 'images/Patreon.png" hspace="3"/>
</a>
<br>';
echo __('Please consider supporting the development of Note Press by clicking the image above and becoming a Patreon.','Note_Press');
echo '
</td>
</tr></table><hr />';
	$menulevel = get_option("Note_Press_Menu_Level",3);
	$checked0='';
	$checked1='';
	$checked2='';
	$checked3='';
	switch ($menulevel)
		{
			case 0: $checked0 = 'checked="checked"';break;
			case 1: $checked1 = 'checked="checked"';break;
			case 2: $checked2 = 'checked="checked"';break;
			case 3: $checked3 = 'checked="checked"';break;
		}
	echo '<h3>'.__('Note Press Settings','Note_Press').'</h3>';
	echo '<hr>';
	echo '<form action="" method="post">';
	echo '<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">';
	//******************************************************************** User Level				
	echo '			<div id="userdiv" class="stuffbox">';
	echo '				<h3><label for="Title">' . __('Note Press User Level:', 'Note_Press') . '</label></h3>';
	echo '				<div class="inside">';
	echo '					
						 
						  <input type="radio" name="NPLevel" value="3" style="width:10px" '.$checked3.'> Admin and Super Admin<br>
						  <input type="radio" name="NPLevel" value="2" style="width:10px" '.$checked2.'> Editor and above<br>
						  <input type="radio" name="NPLevel" value="1" style="width:10px" '.$checked1.'> Author and above<br>
						  <input type="radio" name="NPLevel" value="0" style="width:10px" '.$checked0.'> Contributor and above
	';
	echo '				<p>' . __('Select the level required to create notes with Note Press.', 'Note_Press') . '</p>';
	echo '				</div>
					</div>';
	//******************************************************************** Check box				
	echo '		</div>
				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables ui-sortable">
						<div id="linksubmitdiv" class="postbox ">
						<h3 class="hndle ui-sortable-handle"><span>' . __('Save', 'Note_Press') . '</span></h3>
							<div class="inside">
								<div class="submitbox" id="submitlink">
									<div id="major-publishing-actions">					
										<div id="publishing-action">';
	echo '								<button  class="button-primary" type="submit" name="SaveSettings" value="">' . __('Save Settings', 'Note_Press') . '</button>';
	echo '								</div>
										<div class="clear">
										</div>
									</div>
									<div class="clear">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>';
	echo '	</div>';
	echo '</div>';
	echo '<div id="clear"></div>';
	echo '</form>';
		
?>