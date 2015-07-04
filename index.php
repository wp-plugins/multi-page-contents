<?php
/*  
   Plugin Name:  Multi-Page content (Chapters)
   Description: Make a single post(or etc..) and divide it in Multiple pages, like a chaptered book.  (P.S.  OTHER MUST-HAVE PLUGINS FOR EVERYONE: http://bitly.com/MWPLUGINS  )
   contributors: selnomeria
   Version: 1.0
   LICENCE: Free
*/
if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

define('TableName1__MPCC',		$GLOBALS['wpdb']->prefix .'multipage_contents_bakcup');
define('NextPageReg__MPCC',		'<!--nextpage-->');
define('Devider__MPCC',			'<mppc_exloder />');
define('Pregex1__MPCC',			NextPageReg__MPCC);
define('TitleStart__MPCC',		'<mpcc_titlee>'); 
define('TitleEnd__MPCC',		'</mpcc_titlee>');
define('TitleStartRegex__MPCC',	'\<mpcc_titlee\>');
define('TitleEndRegex__MPCC',	'\<\/mpcc_titlee\>');
define('ErrorMessage1__MPCC',	'There is a problem..Probably, in plugin functionality , or with Current Wordpress Version.please,contact plugin developer (link is in bottom), and provide your wordpress verion/details to understand and solve the problem.');
define('PluginUrSlug__MPCC',	'my-mpcc-slug');
define('ContactMeUrl__MPCC',	'http://j.mp/contactmewordpresspluginstt');


//REDIRECT SETTINGS PAGE (after activation)
add_action( 'activated_plugin', 'activat_redirect__MPCC' ); function activat_redirect__MPCC( $plugin ) { if( $plugin == plugin_basename( __FILE__ ) ) { 
	exit( wp_redirect( admin_url( 'admin.php?page='.PluginUrSlug__MPCC ) ) ); 
} }
add_action('admin_menu','myf452__MPCC');function myf452__MPCC(){ add_submenu_page('options-general.php','Multi-Page content','Multi-Page content', 'manage_options' , PluginUrSlug__MPCC, 'mpcc__callback' );} function mpcc__callback(){
	?> 
	<style>span.codee{background-color:#D2CFCF; padding:1px 3px; border:1px solid; font-family: Consolas;} </style>
	<div class="eachLine" style="margin: 40px 0 0 0;"><br/>
		* You can see the metaboxes under the PAGE/POST editor.   (  Note, the plugin uses &lt;!--nextpage--&gt; tag.  If you dont know what I am talking about, then forget it. ) 
		<br/>* To style/design the blocks&links&output of plugin, use default CSS hooks.  
		<br/>* To change the default phrase <b>"TABLE OF CONTENTS"</b>, use php filter, example:  
		<br/><span class="codee">add_filter('TOCtitle__MPCC','your_funct'); function your_funct($content){  return "Here is my Pagess:";  }</span>
		<br/><br/><br/><br/><br/>* In case of problems, please, <a href="<?php echo ContactMeUrl__MPCC;?>" target="_blank"> contact me </a>.
		
	</div>
	<?php 
}


//ACTIVATION HOOK
register_activation_hook( __FILE__, 'activation__MPCC' );function activation__MPCC() { 	global $wpdb;
	$InitialArray = array( 
		//'MPCC__smth'				=> '1',
		);
	foreach($InitialArray as $name=>$value){	if (!get_option($name)){update_option($name,$value);}	}
	
	//create table
			$bla55555 = $wpdb->get_results("SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE = 'InnoDB'");
	$InnoDB_or_MyISAM = ($bla55555[0]->SUPPORT) ? 'InnoDB' : 'MyISAM' ;
	$x= $wpdb->query("CREATE TABLE IF NOT EXISTS `".TableName1__MPCC."` (
		`IDD` int(30) NOT NULL AUTO_INCREMENT,
		`lang` varchar(170) CHARACTER SET utf8 NOT NULL,
		`postID` int(50) NOT NULL,
		`part` int(50) NOT NULL,
		`content` longtext CHARACTER SET utf8 NOT NULL,
		`partTITLE` text CHARACTER SET utf8 NOT NULL,
		`PartsAmount` int(100) NOT NULL,
		`Extr1` varchar(100) NOT NULL,
		PRIMARY KEY (`IDD`),
		UNIQUE KEY `IDD` (`IDD`)
		) ENGINE=".$InnoDB_or_MyISAM." DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;"
	);
}

add_action('admin_init','startt_func__MPCC');
function startt_func__MPCC(){ 
 if (is_admin()){	require_once(ABSPATH . 'wp-includes/pluggable.php'); $usID= get_current_user_id();  global $pagenow;
	if 	(	(in_array( $pagenow, array('post.php'))  && 'edit' ==$_GET['action'])	//if Edit page 
			||	(in_array( $pagenow, array('post-new.php'))) 						//if NEW page
	){
		add_filter( 'the_editor_content', 'MultipageFilter__MPCC',7,2);
		function MultipageFilter__MPCC($content,$part=1){
			$exploded=explode(Pregex1__MPCC,$content);
			if (count($exploded) > 1){ 
				foreach ($exploded as $name=>$value){ $GLOBALS['mpcc_contents'][$name] = $value; }
				return $exploded[$part-1]; //minus 1 is because, array starts from 0
			}
			return $content;
		}
		add_action( 'add_meta_boxes', 'mtbx_1__MPCC' );	function mtbx_1__MPCC() { 	foreach (get_post_types() as $each) {add_meta_box('Id_44__MPCC', 'Multi-Page contents' ,'aBox1__MPCC', $each );	}	}	
		function aBox1__MPCC( $post ){	
			global $wpdb; 
			$postIdd=(!empty($_GET['post'])) ?  $_GET['post']:$post->ID;
			$indxArray= SanitizedIndxArray__MPCC($postIdd); 
			$chaptingEnabled=   "m_enabled" == $indxArray['EnbDsb'] ;
			
			$ThisPostCh= GetPostChapt__MPCC($post->ID,1);
			?>
		<div class="MPCC_AREA">
			<style>
			xxbody #post-body.columns-2 #postbox-container-1 {position:fixed;margin-right:0px; right:10px;top:20px;}
			a.savetxtbut{background-color: green;border: 2px solid currentColor;border-radius: 5px;color: white;left: 300px; padding: 10px; position: relative;top: -40px;}	a.savetxtbut:hover{font-size:1.1em;}
			#loaderrr{display: block;min-height: 100px;min-width: 350px;padding: 15px;margin: 0px 10% 10% 10%;position: fixed;top: 5%;overflow:hidden;z-index: 35;}
			a.addChapterClass{background-color:red; border:2px solid; border-radius:5px;color:white; padding:5px; position:relative; margin:0 10px;}
			input.titlcls{width:50%;}
			.mpBOOKtitle{text-align:center; margin:20px 0 5px 0;}
			#Id_44__MPCC{background-color: #2CEA2C;}
			.chNUMB{font-size:2em;line-height: 1em;}
			.EACHb_MPCC{background-color:#FFF6CE; margin:20px 0;padding:3px 10px;}
			.Opacity__mpcc{opacity:0.3;}
			.Opacity_FULL__mpcc{opacity:1;}
			#title1__MPCC{width:100%;}
			#AfterPgtitl{background-color:#2CEA2C; padding:5px; margin:10px 0;}
			.Enable_Chaps{background-color:rgb(255, 255, 255); padding:3px;  display:inline-block; margin:0 0 10px 0;}
			</style>
			
			<div id="AfterPgtitl">
			  <div class="Enable_Chaps">
			     Enable Chapters:  <input type="checkbox" id="chp_enabler"  autocomplete="off" onchange="Opacity_changerr();" value="ok"  <?php echo ( $chaptingEnabled ? 'checked="checked"': '' );?> />
			  </div>
			  <script type="text/javascript">
				function Opacity_changerr(){ 
					MyElms=document.getElementsByClassName("Opacity__mpcc");
					if (document.getElementById("chp_enabler").checked)
						 { for (var i=0; i < MyElms.length; i++)  {MyElms[i].className += "  Opacity_FULL__mpcc"; } }
					else { for (var i=0; i < MyElms.length; i++)  {MyElms[i].className = MyElms[i].className.replace("Opacity_FULL__mpcc","").trim(); }		}
				}
			  </script>
			
			
			  <div class="Opacity__mpcc <?php echo ($chaptingEnabled? "Opacity_FULL__mpcc":"");?> ">
			    Title for Base Content (<a href="javascript:alert('In case you will use chapters, then in this field, insert the CHAPTER-title for the main content (which is below, in main text editor)');">Read this popup!</a>):  
			    <input type="text" name="title1__MPCC" id="title1__MPCC" value="<?php echo $ThisPostCh[0]->partTITLE;?>"  placeholder="Title for Base content "  />
			  </div>
			 <!-- <div style="clear:both;width:100%;height:20px;background:black;margin:0 0 40px 0;"></div> -->
			</div>
					<script type="text/javascript">
					//insert this block below title
					var the_div = document.getElementById("AfterPgtitl"); var target_div= document.getElementById("postdivrich"); 	target_div.insertBefore(the_div, target_div.childNodes[0]);
					</script>
			
			
			<div class="Opacity__mpcc <?php echo ($chaptingEnabled? "Opacity_FULL__mpcc":"");?> ">
				<input type="hidden" name="chps_amount__MPCC" id="chps_amount__MPCC" value="<?php echo $indxArray['ChapAmount'];?>"  autocomplete="off"  />						
				<input name="mpBOOK_UPDATION" value="ok" type="hidden" />
				<div id="BookPContainer">
					<?php for ($i=2; $i<=$indxArray['ChapAmount']; $i++ ) { echo '<div id="MPCC_b_'.$i.'" class="EACHb_MPCC">'; output_MPbook_editor__MPCC($postIdd,$i); echo '</div>';  } ;?>
				</div>
				<div class="CH_buttons">
				  <a href="javascript:void(0);" onclick="RemoveLastChap__mpcc();"  class="addChapterClass" style="font-style:italic;">(Remove Last Chapter)</a>
				  <a href="javascript:void(0);" onclick="AddNewChap__mpcc();"  class="addChapterClass" style="background-color:#6900FF">Add CHapter</a>
				</div>
				<div style="clear:both;"></div>
				<a class="contact_author" style="padding:5px 10px; background-color:#CBA70D;position:absolute;bottom:2px; right:10px;" href="<?php echo ContactMeUrl__MPCC;?>" target="_blank">Contact Plugin Author.</a>
			</div>
			<script type="text/javascript">
			var postid__MPCC=document.getElementById("post_ID").value;
			var postlang__MPCC=postlang__MPCC || "";
			var ErrorMessage= "<?php echo ErrorMessage1__MPCC;?>";
			ALLOWED__MPCC=false; Array__mpcc =[];
			
			
				function check_if_plugin_enabled()	{  if (!document.getElementById("chp_enabler").checked) {return false;}  return true; }
				function check_if_ERROR_MSG()		{  if (!check_if_plugin_enabled()) {alert("At first, check the initial checkbox to enable chapters"); return false;}  return true; }
						
			function AddNewChap__mpcc(){			if (!check_if_ERROR_MSG()) return false;
			  var LastChapDiv=document.getElementById('chps_amount__MPCC');				
			  IncreasedChap__MPCC=parseInt(parseInt(LastChapDiv.value) + 1);			LastChapDiv.value = IncreasedChap__MPCC;
			  myyAjaxRequest("&pid=" + postid__MPCC+ "&lChap=" + IncreasedChap__MPCC+"&action=addChapterr&wpeditor__MPCC=1", "./index.php?","POST", "var tempD= document.createElement('div'); tempD.innerHTML= responseee; tempD.id='MPCC_b_'+IncreasedChap__MPCC; tempD.className='EACHb_MPCC';  document.getElementById('BookPContainer').appendChild(tempD);    tinymce.init({ selector: 'mp_TXTid_'+IncreasedChap__MPCC, theme:'modern',  skin:'lightgray',  language:'en'}); tinyMCE.execCommand('mceAddEditor', false, 'mp_TXTid_'+IncreasedChap__MPCC);   quicktags({id : 'mp_TXTid_'+IncreasedChap__MPCC}); window.setTimeout(function(){var x=document.getElementById('chNUMB_'+IncreasedChap__MPCC); x.style.fontSize='3em';x.style.color='red';}, 1000); ", true);
			}
			function RemoveLastChap__mpcc(){		if (!check_if_ERROR_MSG()) return false;
			  var LastChapDiv=document.getElementById('chps_amount__MPCC'); 
			  var ChapElem=(document.getElementById("MPCC_b_"+LastChapDiv.value) ?  document.getElementById("MPCC_b_"+LastChapDiv.value) : false);  if(ChapElem){ChapElem.parentNode.removeChild(ChapElem);}
			  DecreasedChap__MPCC=parseInt(parseInt(LastChapDiv.value) - 1);    LastChapDiv.value = (DecreasedChap__MPCC > 1 ) ? DecreasedChap__MPCC : 1;
			  alert("removed");
			}
			
			
			//Click handler - you might have to bind this click event another way
			//jQuery('input#publish, input#save-post').click(function(e){  SubmCLICKED(e);	});
			var SubmitButton = document.getElementById("save-post") || false;
			var PublishButton = document.getElementById("publish")  || false; 
			if (SubmitButton)	{SubmitButton.addEventListener("click", SubmCLICKED, false);}
			if (PublishButton)	{PublishButton.addEventListener("click", SubmCLICKED, false);}
			function SubmCLICKED(e){ Array__mpcc =[];
			  var Enabled_Disabled= check_if_plugin_enabled();
			  var LastChap=document.getElementById('chps_amount__MPCC').value;	 	  
			  if (LastChap > 1) { 
				if (!ALLOWED__MPCC) {
					Array__mpcc.push({ name: 'ChaptingEnabled',	value:(Enabled_Disabled? "m_enabled":"m_disabled") });	//Chapting is Enabled
					Array__mpcc.push({ name: 'ChaptersAmount',	value:LastChap}); 										//Chapter Amount
					Array__mpcc.push({ name: 'PostId', 			value:postid__MPCC});									//MainPost ID	
					Array__mpcc.push({ name: 'PostTitl',		value:document.getElementById("title").value });		//MainPost title
					Array__mpcc.push({ name: 'PostHtitle', 		value:document.getElementById("title1__MPCC").value});	//MainPost H-title
					Array__mpcc.push({ name: 'PostCont',		value:getContentt("content") });						//MainPost Content
					Array__mpcc.push({ name: 'PostLang', 		value:postlang__MPCC});									//MainPost Lang
					
					for (var i=2; i<= LastChap; i++){
						Array__mpcc.push(	{name: 'titlee_'+i,		value: document.getElementById("chtitle_"+i).value}, 
											{name: "contentt_"+i,	value: getContentt("mp_TXTid_"+i)}		);
					}
					jQuery.post('./index.php?mp_action=SaveMPbook',     Array__mpcc,     function(response,status){
						if(status == "success") { if(response!="success_MPCC"){alert("\r\n\r\nER_MSG:"+response); return false;} ALLOWED__MPCC=true;  if (SubmitButton)	{SubmitButton.click();} else if (PublishButton)	{PublishButton.click();} }     else {alert("error245_"+ErrorMessage);}
					});
					//myyAjaxRequest('param1=abc&param2=abc', './index.php?mp_action=SaveMPbook',  "POST", 'if(responseee!="success_MPCC"){alert("\r\n\r\nER_MSG:"+responseee); return false;} ALLOWED__MPCC=true;  jQuery("input#publish, input#save-post").click(); ', true);
					e.preventDefault(); return false; 
				}
			  }
			}
					function getContentt(el_id){
						//Detect Type of Textarea
						var txt_Container= document.getElementById("wp-"+el_id+"-wrap");
						if (txt_Container.className.indexOf("tmce-active") > -1)		{ var areaType='tinymcee';	var cnt=tinyMCE.get(el_id).getContent();}
						else if (txt_Container.className.indexOf("html-active") > -1)	{ var areaType='htmll';		var cnt=document.getElementById(el_id).value;}
						else { var areaType='unknownn'; var cnt='content_not_found__err#522'; alert("err232__ Cant get chapterID:"+el_id+";  \r\n\r\n" + ErrorMessage); }
						Array__mpcc.push({ name: "AreaTypee_"+el_id, 		value:areaType});
						return cnt;
					}
					
			</script>
			<script type="text/javascript">
			//############### "PLEASE WAIT" popup   ################ https://github.com/tazotodua/useful-javascript/ ################
			function myyAjaxRequest(parameters, url, method, YOUR_PASSED_CODES, ShowBlackground, YourWaitMessage){ pp_ShowBlackground= ShowBlackground || false;
			  var method= (method || "get").toLowerCase(); var url = (url =='') ? window.location.href : url;  if (method=="get") { url= url + ( (url.indexOf('?') <= -1) ? '?' : '&') +parameters+'&CacheAvoider=' + Math.random();}  
			  if(pp_ShowBlackground) {  var z = document.createElement("div"); z.id = "my_waiting_box_888";  z.innerHTML=  '<div style="background-color:black; color:white; opacity:0.9;height:8000px; left:0px;  position:fixed; top:0px; width:100%; z-index:1007;" id="ppa_shadow"> <div style="position:absolute; top:200px;left:49%; z-index: 1008;" id="ppa_load">'+ ( YourWaitMessage || '<span id="ppa_phrase" style="color:grey;font-size:24px;">LOADING...</span>')+'<br/></div></div>'; document.body.appendChild(z); }
			  try{try{var xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");} catch( e ){var xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");}} catch(e){var xmlhttp = new XMLHttpRequest();}  xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4){    if(pp_ShowBlackground) {var x=document.getElementById("my_waiting_box_888"); x.parentNode.removeChild(x);}    responseee = xmlhttp.responseText; eval(YOUR_PASSED_CODES);    }  //xmlhttp.readyState + xmlhttp.status//
			  }; xmlhttp.open(method,url, true);  if (method  == "get"){xmlhttp.send(null);} else if (method  == "post"){xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");xmlhttp.send(parameters);}
			}
			//########################################################################################################################
			</script>
		</div>
		<?php 
			
		}// ### FOR $i
	}// ###METABOX OUTPUT
 }
}

//================= generic functions ================//
function GetPostChapt__MPCC($postid,$part=false){ global $wpdb;
	return $GLOBALS['wpdb']->get_results($wpdb->prepare("SELECT * FROM ".TableName1__MPCC." WHERE postID = '%d'".   ($part ? " AND part = '%d'" : "" ),$postid, $part  ));
}
function SanitizedIndxArray__MPCC($post_id){ 
	$a= GetPostChapt__MPCC($post_id,0);
	if (!empty($a[0]))	{ $new['ChapAmount']=$a[0]->PartsAmount;	$new['EnbDsb']=$a[0]->Extr1;  }
	else				{ $new['ChapAmount']=1;  					$new['EnbDsb']="m_disabled";  }
	return $new;
}


function get_chapters__MPCC($txt){	$exploded=explode(Pregex1__MPCC,$txt); return array('chapters'=>$exploded, 'chap_amount'=> count($exploded) ); }
function RemoveTitlePart__MPCC($contents){	return preg_replace('/'.TitleStartRegex__MPCC.'(.*?)'.TitleEndRegex__MPCC.'/si','',$contents); 	} 
function UPDATEE_OR_INSERTTT__MPCC($tablename, $NewArray, $WhereArray){	global $wpdb; $arrayNames= array_keys($WhereArray);
	//convert array to STRING
	$o=''; $i=1; foreach ($WhereArray as $key=>$value){ $value= is_numeric($value) ? $value : "'".addslashes($value)."'"; $o .= $key . " = $value"; if ($i != count($WhereArray)) { $o .=' AND '; $i++;}  }
	//check if already exist
	$CheckIfExists = $wpdb->get_var($wpdb->prepare("SELECT $arrayNames[0] FROM $tablename WHERE $o",1) );
	if (!empty($CheckIfExists))	{	$wpdb->update($tablename,	$NewArray,	$WhereArray	);}
	else						{	$wpdb->insert($tablename, 	array_merge($NewArray, $WhereArray)	);	}
}


//================= specific functions ================//
add_action('init','Ajax_wpeditor__MPCC',1);  function Ajax_wpeditor__MPCC(){	if (isset($_POST['wpeditor__MPCC'])){
	output_MPbook_editor__MPCC($_POST['pid'], $_POST['lChap']) ; exit;
  }
}
// ======================================= SHOW TEXT EDITOR ===================================== //
function output_MPbook_editor__MPCC($postid, $numb){ global $wpdb;
	$currentPart = GetPostChapt__MPCC($postid, $numb);
	if ($currentPart)	{ 
		$cTITLE= $currentPart[0]->partTITLE;
		$cCONTENT= $currentPart[0]->content;
	}
	else	{$post=get_post($postid);$exploded=get_chapters__MPCC($post->post_content);
		$cTITLE = '';
		$cCONTENT=$exploded['chapters'][$numb-1] ? $exploded['chapters'][$numb-1] : 'default text';
	}
	$cCONTENT=RemoveTitlePart__MPCC($cCONTENT);  	echo '
	<div id="EachMP_block_'.$numb.'" class="MPCC_Ec ">
		<div class="mpBOOKtitle"><div class="chNUMB" id="chNUMB_'.$numb.'">(:'. ((int)($numb-1)).') </div> <input type="text" id="chtitle_'.$numb.'" value="'.$cTITLE.'" placeholder="Title for This Chapter" class="titlcls" /> </div>	<div style="clear:both;"></div>
		<div class="each_mpBOOK_textareaDIV"><div>';
			// NAME parameter dont needs to be set for TEXTAREA, because if we set it, on PUBLISH/UPDATE time, it is shtrown to $_POST session, and when the BOOK CONTENT is BIG, then $_POST cant handle such BIG DATA AMOUNT, and the page becomes problematic... so, we do it with AJAX request ... and REMOVED TEXTAREA
			//'<textarea class="each_mpBOOK_TXTAREA" id="boook_TE_'.$numb.'_1" name="mpBook_CONTENT__'.$numb.'" >'.$each_cont.'</textarea>';
			wp_editor( $cCONTENT , 'mp_TXTid_'.$numb, $settings = array(
			'editor_class'=>'each_mpBOOK_TXTAREA',    	/* 'textarea_name'=>'mpBook_CONTENT__'. $numb, */
			'tinymce'=>true ,'wpautop' =>false,	'media_buttons' => true ,	'teeny' => true, 'quicktags'=>true, ));
			echo '
		</div></div>	<div style="clear:both;"></div>
	</div>';
}


// ======================================= SAVE action===================================== //
add_action('init', 'save_book__MPCC',1); function save_book__MPCC(){ global $wpdb;
  if (isset($_GET['mp_action']) && $_GET['mp_action']=='SaveMPbook' ){
	$c_EnblDisb		=$_POST['ChaptingEnabled'];  	if(!in_array($c_EnblDisb, array("m_enabled","m_disabled")))		die('incorrect  "ChaptingEnabled"..'.$_POST['ChaptingEnabled']);
	$ChaptersAmount	=$_POST['ChaptersAmount'];   	if(!is_numeric($ChaptersAmount)) die('incorrect  "ChaptersAmount"..'.$_POST['ChaptersAmount']);
	$PostId			=$_POST['PostId'];				if(!is_numeric($PostId)) die('incorrect  "PostId"..'.$_POST['PostId']);
	$PostLang		=sanitize_file_name($_POST['PostLang']);
	if (CurrentUserCanEditThis__MPCC($PostId)){
	  //update INDEX record for post id
		UPDATEE_OR_INSERTTT__MPCC(TableName1__MPCC, array('PartsAmount'=>$ChaptersAmount, 'Extr1'=> $c_EnblDisb ),	array('postID'=> $PostId, 'part'=> 0 ) );
	  //update the first (main content)
		$contnt=  ($_POST['AreaTypee_' . 'content']=='htmll'  ?  wpautop( $_POST['PostCont'], true)   :   stripslashes($_POST['PostCont'])  ) ;
		UPDATEE_OR_INSERTTT__MPCC(TableName1__MPCC, array('content'=>$contnt, 'partTITLE'=> sanitize_title($_POST['PostHtitle'])),  array('postID'=> $PostId, 'part'=> 1) );
	  //update contents
	  for($i=2; $i <= $ChaptersAmount; $i++){
		$title= stripslashes($_POST['titlee_'.$i]);
		$got_contn = ($_POST['AreaTypee_'. 'mp_TXTid_'.$i]=='tinymcee'  ?  wpautop($_POST['contentt_'.$i], true) : stripslashes($_POST['contentt_'.$i])  ) ; 
		$contn = TitleStart__MPCC.$title.TitleEnd__MPCC   .   str_ireplace(Pregex1__MPCC,'',$got_contn);
		UPDATEE_OR_INSERTTT__MPCC( TableName1__MPCC, 	array('content'=>$contn, 'partTITLE'=> $title),	array('postID'=> $PostId, 'part'=> $i) );
	  }
	  //delete previous revisions & drafts
	  	$del= $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_type = 'inherit' AND post_parent = '%d'",$PostId) ); 
	
	  exit("success_MPCC");
	}
	else{ 
	  exit("error3543__".ErrorMessage1__MPCC);
	}
  }
}		
		//Re-used functions
		function CurrentUserCanEditThis__MPCC($postid){	require_once(ABSPATH . 'wp-includes/pluggable.php'); global $wpdb,$current_user;
			$authorID = $wpdb->get_var($wpdb->prepare("SELECT post_author FROM ".$wpdb->prefix."posts WHERE ID ='%d'",  $postid) );
			if (!$authorID && current_user_can('edit_posts')) {return true;}	//If opening new post
			if ($authorID) { if ($authorID == $current_user->ID || current_user_can('delete_others_posts')) { return true; } }
			return false;
		}


add_action( 'save_post', 'savpst_62__MPCC',95);	
function savpst_62__MPCC( $post_id ){ if ($post_id==$_POST['post_ID']) {  global $wpdb;		$additional_content=''; 
		$PArrayy	= GetPostChapt__MPCC($post_id, $numb);
		$indxArray	= SanitizedIndxArray__MPCC($post_id);
		//if chapting is enabled, then add CHAPTERS into to post_content (because the content should be searchable in SEARCH query...)
		if ($indxArray['EnbDsb'] == "m_enabled"){
			for ($i=2; $i <= $indxArray['ChapAmount'] ; $i++){
				foreach($PArrayy as $key=>$name){   if($name->part == $i){ $additional_content .= Pregex1__MPCC. $name->content; }   } 
			}
			$pst=get_post($post_id); 
			UPDATEE_OR_INSERTTT__MPCC($GLOBALS['wpdb']->posts,   array('post_content' => $pst->post_content.$additional_content), array('ID'=> $post_id)   );
		}
} }







// ================== CONTENT OUTPUT =================== //
	//	add_filter( 'wp_link_pages_link', 'filter_links__MPCC', 11,2 ); function pagelinks_filter__MPCC($link, $prev ){  ....  }
	//	https://core.trac.wordpress.org/browser/tags/4.2.2/src/wp-includes/post-template.php#L842

	//	add_filter(	'wp_link_pages_args','pagelinks_filter__MPCC',11,1);function pagelinks_filter__MPCC($parameters){
	//		$defaults = array( 'echo'=> 1,
	//			'before'	 => '<div class="pages__MPCC">' . __( '' ),		'after'		=> '</p>',
	//			'link_before'=> '<span class="PageNum__MPCC">',				'link_after'=> '</span>',
	//			'next_or_number'=> 'number',
	//			'separator'		=> ' ',
	//			'nextpagelink'	=> __( 'Next page' ),  	'previouspagelink'=> __( 'Previous page' ),
	//			'pagelink'		=> '%',
	//		);
	//		return $defaults;
	//	}
//disable it... we will do manuall OUTPUT	
add_filter(	'wp_link_pages_args','pagelinks_filter__MPCC',11,1);function pagelinks_filter__MPCC($parameters){   return  array( 'echo'=> 0 );  }
	//add_shortcode( 'index_mpcc', 'chapters_outp_func' );function chapters_outp_func($atts) { return get_table_of_contents();}
add_filter('the_content','output_toc__MPCC',88);	function output_toc__MPCC($content){ return get_table_of_contents($content); }
function get_table_of_contents($content){	global $wpdb,$post;  
  $currentPost = GetPostChapt__MPCC($post->ID); 
  if (!empty($currentPost[0])){
	foreach ($currentPost as $eachPart) {if (0==$eachPart->part){$chapAmnt=$eachPart->PartsAmount; $c_EnblDisb=   "m_enabled"==$eachPart->Extr1; }   if (1==$eachPart->part){$postHtitle=$eachPart->partTITLE;}  }
	if (isset($chapAmnt) && $chapAmnt>1 && $c_EnblDisb){
		$InitQueryPg= get_query_var('page'); $QueriedPage=(empty($InitQueryPg)) ? 1 : $InitQueryPg;
		if ($QueriedPage==1) {  $content = TitleStart__MPCC.$postHtitle.TitleEnd__MPCC.$content; }
																							$TOCtitle = apply_filters('TOCtitle__MPCC', "");
		$content .= '<div class="TOC_list__MPCC">  <div class="toctitle__MPCC">'. ( !empty($TOCtitle)  ? $TOCtitle : "Table of Contents").'</div>';
		$pLink = get_permalink($post->ID); 	$HomeUrl = home_url();  $base_paged_link = _wp_link_page__modified_to_get_base($post,9777797777);
		foreach ($currentPost as $eachPart){ $partN= $eachPart->part; if ($partN >= 1) {
			$content .= '<div class="row__MPCC mpccr_'.$partN.'" ><span class="urlE__MPCC">'.( $QueriedPage == $partN ? '<span class="currentActv__MPCC">'.$eachPart->partTITLE .'</span>': '<a class="pageA__MPCC" id="part'.$partN.'" href="'. str_ireplace(9777797777, $partN ,$base_paged_link) .'">' .$eachPart->partTITLE.'</a>') .'</span></div>';			
		  }
		}
		$content .= '</div>';	
	}
  } return $content;
}

//Style title
add_filter('the_content','style_title__MPCC',89);function style_title__MPCC($content){
	$content = preg_replace('/'.TitleStartRegex__MPCC.'(.*?)'.TitleEndRegex__MPCC.'/si','<h1 class="ptitle__MPCC">$1</h1>',$content); 	return $content;
}
add_action('wp_head','stylesheet_for__MPCC');function stylesheet_for__MPCC(){	echo '
  <style type="text/css">.______BY_____MPCC___PLUGIN______{}
  h1.ptitle__MPCC{text-align:center;font-size:1.3em; font-weight:bold;margin: 10px 0;}
  .TOC_list__MPCC{clear:both; text-align:left; margin: 0 0 0 10%; background-color:#F2E7BA; padding:4px;}  .toctitle__MPCC{font-weight: bold; text-align: center;}
  .row__MPCC{margin:10px 10px;}    .row__MPCC::before{content: "\25CF"; margin: 0 10px;}
  span.urlE__MPCC{ display: inline-block; width: 80%;}  span.currentActv__MPCC{color:grey;margin:0 0 0 3px; font-size: 1.4em;}  a.active__mpcc{color:black;cursor:default;}
  a.pageA__MPCC{font-size: 1.4em;background-color: #E7E7E7; padding: 2px 5px;  color:blue; border-radius: 3px;  width: 100%; display: inline-block; }
  </style>';
}

		//sourced from  http://wpseek.com/function/_wp_link_page/ and modified to recude Database calls by 90%
		function _wp_link_page__modified_to_get_base($post, $i ) {
			global $wp_rewrite;
			if ( 1 == $i ) { $url = get_permalink($post);  } 
			else {
				if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )    $url = add_query_arg( 'page', $i, get_permalink() );
				elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )        $url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
				else    $url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
			}
			return esc_url( $url );
		}
?>