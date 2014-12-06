<?php

@ini_set('memory_limit','256M');
class LoyaltyUpdate extends Module
{


	/* @var boolean error */
	protected $error = false;
	
	public function __construct()
	{
	 	$this->name = 'loyaltyupdate';
		$this->module_key = 'c8bb36f51f8a60b5309309fbd1321cad';
	 if(_PS_VERSION_ > "1.4.0.0" && _PS_VERSION_ < "1.5.0.0"){
		$this->tab = 'pricing_promotion';
		$this->author = 'RSI';
		$this->need_instance = 0;
		}
		elseif(_PS_VERSION_ > "1.5.0.0"){
				$this->tab = 'pricing_promotion';
		$this->author = 'RSI';
			}
		
		else{
		$this->tab = 'Tools';
		}
	 	$this->version = '1.3';

	 	parent::__construct();

        $this->displayName = $this->l('Custom Loyalty Rewards');
        $this->description = $this->l('Edit loyalty points');
		$this->confirmUninstall = $this->l('Are you sure you want to delete all the data ?');
	}
	
	public function install()
	{
		
		if (parent::install() == false)
		return false;
	
	
		/*
		if (!Configuration::updateValue('AQUASLIDER_TOP', "0"))
		return false;
		if (!Configuration::updateValue('AQUASLIDER_FLOAT', "left"))
		return false;*/
	
		/*$query= 'INSERT INTO '._DB_PREFIX_.'loyaltyupdate  VALUES (1,"",1,1,1,"squareOutMoving","top",0,"left",0)';
		if (!Db::getInstance()->Execute($query))
		return false;
		$query= 'INSERT INTO '._DB_PREFIX_.'loyaltyupdate  VALUES (2,"",1,1,2,"squareOutMoving","top",0,"left",0)';
		if (!Db::getInstance()->Execute($query))
		return false;
		$query= 'INSERT INTO '._DB_PREFIX_.'loyaltyupdate_lang  VALUES (1,1,"","","")';
		if (!Db::getInstance()->Execute($query))
		return false;
		$query= 'INSERT INTO '._DB_PREFIX_.'loyaltyupdate_lang  VALUES (1,2,"","","")';
		if (!Db::getInstance()->Execute($query))
		return false;
			$query= 'INSERT INTO '._DB_PREFIX_.'loyaltyupdate_lang  VALUES (1,3,"","","")';
		if (!Db::getInstance()->Execute($query))
		return false;
			$query= 'INSERT INTO '._DB_PREFIX_.'loyaltyupdate_lang  VALUES (1,4,"","","")';
		if (!Db::getInstance()->Execute($query))
		return false;
			$query= 'INSERT INTO '._DB_PREFIX_.'loyaltyupdate_lang  VALUES (2,1,"","","")';
		if (!Db::getInstance()->Execute($query))
		return false;
			$query= 'INSERT INTO '._DB_PREFIX_.'loyaltyupdate_lang  VALUES (2,2,"","","")';
		if (!Db::getInstance()->Execute($query))
		return false;
			$query= 'INSERT INTO '._DB_PREFIX_.'loyaltyupdate_lang  VALUES (2,3,"","","")';
		if (!Db::getInstance()->Execute($query))
		return false;
			$query= 'INSERT INTO '._DB_PREFIX_.'loyaltyupdate_lang  VALUES (2,4,"","","")';
		if (!Db::getInstance()->Execute($query))
		return false;*/
		if(ini_get("allow_url_fopen") == "0"){
		ini_set("allow_url_fopen", "1");
		}

		
		return true;
	}
	
	
	public function uninstall()
	{
	
	

return true;
	  
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function getContent()
    {
		$this->_html = '<h2>'.$this->displayName.'</h2>
		<script type="text/javascript" src="'.$this->_path.'loyaltyupdate.js"></script>';
			
		/* Add a link */
		if (isset($_POST['send']))
		{
				mysql_connect(_DB_SERVER_, _DB_USER_, _DB_PASSWD_) or die(mysql_error());
mysql_query("SET NAMES UTF8");//this is needed for UTF 8 characters - multilanguage
mysql_select_db(_DB_NAME_) or die(mysql_error());
		
		$sql='UPDATE '._DB_PREFIX_.'loyalty SET `points`= \''.Tools::getValue('points').'\' WHERE `id_loyalty` = '.Tools::getValue('id_customer').';';		
					if (!Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'loyalty SET `points`= \''.Tools::getValue('points').'\' WHERE `id_customer` = '.Tools::getValue('id_customer').' AND `id_order` = '.Tools::getValue('id_order').';'))
			return false;
		$this->_html .= $this->displayConfirmation($this->l('points of user '.Tools::getValue('email').' updated'));

		}
		$this->_displayForm();
		
		
		return $this->_html;
    }
	
	private function _displayForm()
	{
		global $cookie;
		/* Language */
			if(_PS_VERSION_ < "1.5.0.0"){
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages();
			}
			else
			{
					$languages = Language::getLanguages(false);
			$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
				}
		$divLangName = 'text¤description¤url¤picture';
		/* Title */
		
		if (_PS_VERSION_ > "1.4.0.0")
	{
			$this->_html .= '	<script type="text/javascript" src="../modules/loyaltyupdate/accordion.ui.js"></script>
			<script type="text/javascript">
	
$(function() {
		
		$( "#accordion" ).accordion({ autoHeight: false });
	});

	</script>';
		}
		$this->_html .= '
		
	
		';
		 	if (_PS_VERSION_ < "1.5.0.0")
	{
			$this->_html .= '
		';
	}
			$this->_html .= '
	
		
	
		<style type="text/css">
		.intext {width: 390px; height: 24px; 
		font-family:arial, sans-serif; font-size:12px; padding:3px; xvisibility:hidden;
		}
		.layouts { padding: 50px; font-family: Georgia, serif; }
		.layout-slider { margin-bottom: 5px; width: 50%; }
		.layout-slider-settings { font-size: 12px; padding-bottom: 10px; }
		.layout-slider-settings pre { font-family: Courier; }
		.tile2 	{ 
		position:absolute; border:1px solid silver; background-color:white;
		filter:alpha(opacity=50); -moz-opacity:0.50; opacity:0.50;
		font-family:arial, sans-serif; font-size:12px; padding:3px;
		}
		.links  {position:absolute; left:0px; top:0px; width: 390px; height: 24px; 
		font-family:arial, sans-serif; font-size:12px; padding:3px; visibility:hidden;
		}
		.alts 	{position:absolute; left:0px; top:30px; width: 390px; height: 24px; 
		font-family:arial, sans-serif; font-size:12px; padding:3px; visibility:hidden;
		}
		.tools  {
		font-family:arial,sans-serif; font-size:12px; line-height:30px 
		}
		.toolbtn {width:150px;line-height:20px}
		
		
		#iconselect {
		background: url(../modules/loyaltyupdate/images/select-bg.gif) no-repeat;
		height: 25px;
		width: 250px;
		font: 13px Arial, Helvetica, sans-serif;
		padding-left: 15px;
		padding-top: 4px;
		}
		
		.selectitems {
		width:230px;
		height:auto;
		border-bottom: dashed 1px #ddd;
		padding-left:10px;
		padding-top:2px;
		}
		.selectitems span {
		margin-left: 5px;
		}
		#iconselectholder {
		width: 250px;
		overflow: auto;
		display:none;
		position:absolute;
		background-color:#FFF5EC;
		
		}
		.hoverclass{
		background-color:#FFFFFF;
		curson:hand;}
		.selectedclass{
		background-color:#FFFF99;
		}
		.box{
		background: #fff;
		margin:5px
		}
		.boxholder{
		clear: both;
		padding: 5px;
		background: #8DC70A;
		}
		.tabm{
		float: left;
		height: 32px;
		width: 102px;
		margin: 0 1px 0 0;
		text-align: center;
		background: #8DC70A url(../modules/loyaltyupdate/images/greentab.jpg) no-repeat;
		
		}
		.tabtxt{
		margin: 0;
		color: #fff;
		font-size: 12px;
		font-weight: bold;
		padding: 9px 0 0 0;
		}
		-->
		</style>
		<script type="text/javascript">
		id_language = Number('.$defaultLanguage.');
		</script>
			<fieldset style="width:700px">
		<div id="accordion">
	
	
				
		
		
		

		';
		
			
		global $currentIndex, $cookie, $adminObj;
		

		$this->_html .= '
		<h3 style="padding-left:40px">
		<img src="'.$this->_path.'search.png" alt="" title="" width="32" />'.$this->l('Search by email').'</h3>
			
			<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
			<input name="email" type="text" value="" />
		
			<input name="search" type="submit" value="Search" />
			<p>'.$this->l('leave empty to find all customers').'</p>
			</form>

		<h3 style="padding-left:40px">
		<img src="'.$this->_path.'logo.png" alt="" title="" />'.$this->l('List of points').'</h3>
							<div>';
							
		
							
							
		$this->html .='
		<table class="table">
		<tr>
		<th>'.$this->l('ID').'</th>
		<th>'.$this->l('First name').'</th>
		<th>'.$this->l('Last name').'</th>
		<th>'.$this->l('Email').'</th>
		<th>'.$this->l('Points').'</th>

	

		</tr>';
			mysql_connect(_DB_SERVER_, _DB_USER_, _DB_PASSWD_) or die(mysql_error());
mysql_query("SET NAMES UTF8");//this is needed for UTF 8 characters - multilanguage
mysql_select_db(_DB_NAME_) or die(mysql_error());
if(@$_POST['email'] == NULL){
		$sq = 'SELECT l.*, c.*
		FROM '._DB_PREFIX_.'loyalty l
		LEFT JOIN '._DB_PREFIX_.'customer c ON (l.id_customer = c.id_customer)
		'.((_PS_VERSION_ > "1.5.0.0") ? "
		WHERE id_shop = ".$this->context->shop->id : '').' ORDER BY `email` ASC';
}
else
{
		$sq = 'SELECT l.*, c.*
		FROM '._DB_PREFIX_.'loyalty l
		LEFT JOIN '._DB_PREFIX_.'customer c ON (l.id_customer = c.id_customer)
		'.((_PS_VERSION_ > "1.5.0.0") ? "
		WHERE id_shop = ".$this->context->shop->id : '').' AND `email` = \''.$_POST['email'].'\'';
	}
	//echo $sq;
		 $sqll= mysql_query($sq);
		 
	  $rpp         = 40; // results per page

        $adjacents   = 2;

        

        $page = intval(@$_GET["page"]);

        if ($page <= 0)

            $page = 1;

        

        $reload = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];

        // count total number of appropriate listings:

        @$tcount = mysql_num_rows($sqll);

        

        // count number of pages:

        $tpages = ($tcount) ? ceil($tcount / $rpp) : 1; // total pages, last page number

        

        $count = 0;

        $i     = ($page - 1) * $rpp;

     
		
		
		
	    while (($count < $rpp) && ($i < $tcount)) {

            mysql_data_seek($sqll, $i);

            $query = mysql_fetch_array($sqll);
		
	
		$this->_html .= '<div style="border:1px solid #ccc">
		<tr>
		<td><strong>ID Order: </strong>'.$query['id_order'].' - </td>
		<td><strong>ID Customer: </strong>'.$query['id_customer'].' - </td>
		<td><strong>Name: </strong>'.$query['firstname'].' - </td>
		<td><strong>Lastname: </strong>'.$query['lastname'].' - </td>
		<td><strong>Email: </strong>'.$query['email'].'</td>
		<td><form method="post" action="'.$_SERVER['REQUEST_URI'].'"><input name="points" type="text" value="'.$query['points'].'" /><input name="id_customer" type="hidden" id="id_customer" value="'.$query['id_customer']	.'" /><input name="email" type="hidden" id="email" value="'.$query['email'].'" /><input name="id_order" type="hidden" id="id_order" value="'.$query['id_order'].'" /><input name="send" type="submit" value="Update" /></form></td>
		</td>
		</tr></div>
		
	';
	        $i++;
	  $count++;
	}
		
		
		
		
		$this->_html .= '
		</table><br/>';
			$this->_html .= LoyaltyUpdate::paginate_one($reload, $page, $tpages, $adjacents);
		$this->_html .= '</div>';
		
		
		
		
		
		$this->_html .= '

		<h3 style="padding-left:40px">
		<img src="'.$this->_path.'module.png" alt="" title="" />'.$this->l('Contribute').'</h3>
		<div>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">	<p class="clear">'.$this->l('You can contribute with a donation if our free modules and themes are usefull for you. Clic on the link and support us!').'</p>
				<p class="clear">'.$this->l('For more modules & themes visit: www.catalogo-onlinersi.com.ar').'</p>
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="HMBZNQAHN9UMJ">
<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/scr/pixel.gif" width="1" height="1">

</form></div>
		</fieldset>
		';
		
		 
	}
	
		public function getLinks()
	{
		$result = array();
		/* Get id and url */
		if (!$links = Db::getInstance()->ExecuteS('SELECT l.*, c.*
		FROM '._DB_PREFIX_.'loyalty l
		LEFT JOIN '._DB_PREFIX_.'customer c ON (l.id_customer = c.id_customer)
		'.((_PS_VERSION_ > "1.5.0.0") ? "
		WHERE id_shop = ".$this->context->shop->id : '').' ORDER BY `email` ASC GROUP BY c.id_customer'))
		return false;
		$i = 0;
		foreach ($links AS $link)
		{
		$result[$i]['id'] = $link['id_customer'];
		
		$result[$i]['firstname'] = $link['firstname'];
		$result[$i]['lastname'] = $link['lastname'];
		$result[$i]['id_loyalty'] = $link['id_loyalty'];
		$result[$i]['id_loyalty'] = $link['id_loyalty'];
		$result[$i]['email'] = $link['email'];
			$result[$i]['id_order'] = $link['id_order'];
		$result[$i]['points'] = $link['points'];
		
		$i++;
		
		}
		
		return $result;
	}
	  function paginate_one($reload, $page, $tpages)

    {

        $firstlabel = $this->l('First');

        $prevlabel  = $this->l('Prev');

        $nextlabel  = $this->l('Next');

        $lastlabel  = $this->l('Last');

        

        $out     = "<div class=\"pagin\">\n";

        $reload2 = preg_replace("/\&page=[0-9]/", "", $reload);

        // first

        if ($page > 1) {

            $out .= "<a href=\"" . $reload2 . "\">" . $firstlabel . "</a>\n";

        } else {

            $out .= "<span>" . $firstlabel . "</span>\n";

        }

        

        // previous

        if ($page == 1) {

            $out .= "<span>" . $prevlabel . "</span>\n";

        } elseif ($page == 2) {

            $out .= "<a href=\"" . $reload2 . "\">" . $prevlabel . "</a>\n";

        } else {

            $out .= "<a href=\"" . $reload2 . "&amp;page=" . ($page - 1) . "\">" . $prevlabel . "</a>\n";

        }

        

        // current

        $out .= "<span class=\"current\">" . $this->l('Page') . " " . $page . " of " . $tpages . "</span>\n";

        

        // next

        if ($page < $tpages) {

            $out .= "<a href=\"" . $reload2 . "&amp;page=" . ($page + 1) . "\">" . $nextlabel . "</a>\n";

        } else {

            $out .= "<span>" . $nextlabel . "</span>\n";

        }

        

        // last

        if ($page < $tpages) {

            $out .= "<a href=\"" . $reload2 . "&amp;page=" . $tpages . "\">" . $lastlabel . "</a>\n";

        } else {

            $out .= "<span>" . $lastlabel . "</span>\n";

        }

        

        $out .= '</div>						<a href="../modules/loyaltyupdate/moduleinstall.pdf">README</a><br/>
				<a href="../modules/loyaltyupdate/termsandconditions.pdf">TERMS</a><br/>
				
		
		';

        

        return $out;

    }
}
