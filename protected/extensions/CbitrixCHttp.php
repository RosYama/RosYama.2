<?php 
class CbitrixCHttp
{
	var $url = '';
	var $status = 0;
	var $result = '';
	var $fp = null;
	var $headers = array();
	var $cookies = array();

	var $http_timeout = 120;

	var $user_agent;

	var $follow_redirect = false;
	var $errno;
	var $errstr;

	var $additional_headers = array();

	function CHTTP()
	{
		$this->user_agent = 'BitrixSM ' . __CLASS__ . ' class';
	}

	function URN2URI($urn, $server_name = '')
	{
		global $APPLICATION;
		if(preg_match("/^[a-z]+:\\/\\//", $urn))
		{
			$uri = $urn;
		}
		else
		{
			if($APPLICATION->IsHTTPS())
				$proto = "https://";
			else
				$proto = "http://";

			if(strlen($server_name) > 0)
				$server_name = preg_replace("/:(443|80)/", "", $server_name);
			else
				$server_name = preg_replace("/:(443|80)/", "", $_SERVER["HTTP_HOST"]);

			$uri = $proto.$server_name.$urn;
		}
		return $uri;
	}

	function Download($url, $file)
	{
		CheckDirPath($file);
		$this->fp = fopen($file, "wb");
		if(is_resource($this->fp))
		{
			$res = $this->HTTPQuery('GET', $url);

			fclose($this->fp);
			unset($this->fp);

			return $res && ($this->status == 200);
		}
		return false;
	}

	function Get($url)
	{
		if ($this->HTTPQuery('GET', $url))
		{
			return $this->result;
		}
		return false;
	}

	function Post($url, $arPostData)
	{
		$postdata = '';
		if (is_array($arPostData))
		{
			foreach ($arPostData as $k => $v)
			{
				if (strlen($postdata) > 0)
				{
					$postdata .= '&';
				}
				$postdata .= urlencode($k) . '=' . urlencode($v);
			}
		}

		if($this->HTTPQuery('POST', $url, $postdata))
		{
			return $this->result;
		}
		return false;
	}
	
	function Head($url, $arPostData)
	{
		$postdata = '';
		if (is_array($arPostData))
		{
			foreach ($arPostData as $k => $v)
			{
				if (strlen($postdata) > 0)
				{
					$postdata .= '&';
				}
				$postdata .= urlencode($k) . '=' . urlencode($v);
			}
		}

		if($this->HTTPQuery('HEAD', $url, $postdata))
		{
			return $this->result;
		}
		return false;
	}	

	function HTTPQuery($method, $url, $postdata = '')
	{
		$arUrl = false;
		do {
			$this->url = $url;
			$arUrl = $this->ParseURL($url, $arUrl);
			if (!$this->Query($method, $arUrl['host'], $arUrl['port'], $arUrl['path_query'], $postdata, $arUrl['proto']))
			{
				return false;
			}
		} while ($this->follow_redirect && array_key_exists('Location', $this->headers) && strlen($url = $this->headers['Location']) > 0);

		return true;
	}

	function Query($method, $host, $port, $path, $postdata = false, $proto = '', $post_content_type = 'N')
	{
		$this->status = 0;
		$this->result = '';
		$this->headers = array();
		$this->cookies = array();
		$fp = fsockopen($proto.$host, $port, $this->errno, $this->errstr, $this->http_timeout);
		if ($fp)
		{
			$strRequest = "$method $path HTTP/1.0\r\n";
			$strRequest .= "User-Agent: {$this->user_agent}\r\n";
			$strRequest .= "Accept: */*\r\n";
			$strRequest .= "Host: $host\r\n";
			$strRequest .= "Accept-Language: en\r\n";

			if (count($this->additional_headers) > 0)
			{
				foreach ($this->additional_headers as $key => $value)
					$strRequest .= $key.": ".$value."\r\n";
			}

			if ($method == 'POST' || $method == 'PUT' || $method == 'HEAD')
			{
				if ('N' !== $post_content_type)
					$strRequest .= $post_content_type == '' ? '' : "Content-type: ".$post_content_type."\r\n";
				else
					$strRequest.= "Content-type: application/x-www-form-urlencoded\r\n";

				$strRequest.= "Content-length: " .
					(function_exists('mb_strlen')? mb_strlen($postdata, 'latin1'): strlen($postdata)) . "\r\n";
			}
			$strRequest .= "\r\n";
			if ($method == 'POST' || $method == 'PUT' || $method == 'HEAD')
			{
				$strRequest.= $postdata;
				$strRequest.= "\r\n";
			}

			fputs($fp, $strRequest);

			$headers = "";
			while(!feof($fp))
			{
				$line = fgets($fp, 4096);
				if($line == "\r\n")
				{
					//$line = fgets($fp, 4096);
					break;
				}
				$headers .= $line;
			}
			if ($method == 'HEAD') {
				$this->result = $this->ParseHeaders($headers);
				return true;
				}

			if(is_resource($this->fp))
			{
				while(!feof($fp))
					fwrite($this->fp, fread($fp, 4096));
			}
			else
			{
				$this->result = "";
				while(!feof($fp))
					$this->result .= fread($fp, 4096);
			}

			fclose($fp);

			return true;
		}
		
		return false;
	}

	function SetAuthBasic($user, $pass)
	{
		$this->additional_headers['Authorization'] = "Basic ".base64_encode($user.":".$pass);
	}

	public function ParseURL($url, $arUrlOld = false)
	{
		$arUrl = parse_url($url);

		if (is_array($arUrlOld))
		{
			if (!array_key_exists('scheme', $arUrl))
			{
				$arUrl['scheme'] = $arUrlOld['scheme'];
			}

			if (!array_key_exists('host', $arUrl))
			{
				$arUrl['host'] = $arUrlOld['host'];
			}

			if (!array_key_exists('port', $arUrl))
			{
				$arUrl['port'] = $arUrlOld['port'];
			}
		}

		$arUrl['proto'] = '';
		if (array_key_exists('scheme', $arUrl))
		{
			$arUrl['scheme'] = strtolower($arUrl['scheme']);
		}
		else
		{
			$arUrl['scheme'] = 'http';
		}

		if (!array_key_exists('port', $arUrl))
		{
			if ($arUrl['scheme'] == 'https')
			{
				$arUrl['port'] = 443;
			}
			else
			{
				$arUrl['port'] = 80;
			}
		}

		if ($arUrl['scheme'] == 'https')
		{
			$arUrl['proto'] = 'ssl://';
		}

		$arUrl['path_query'] = array_key_exists('path', $arUrl) ? $arUrl['path'] : '/';
		if (array_key_exists('query', $arUrl) && strlen($arUrl['query']) > 0)
		{
			$arUrl['path_query'] .= '?' . $arUrl['query'];
		}

		return $arUrl;
	}

	public function ParseHeaders($strHeaders)
	{
		$arHeaders = explode("\n", $strHeaders);
		foreach ($arHeaders as $k => $header)
		{
			if ($k == 0)
			{
				if (preg_match(',HTTP\S+ (\d+),', $header, $arFind))
				{
					$this->status = intval($arFind[1]);
				}
			}
			elseif(strpos($header, ':') !== false)
			{
				$arHeader = explode(':', $header, 2);
				if ($arHeader[0] == 'Set-Cookie')
				{
					if (($pos = strpos($arHeader[1], ';')) !== false && $pos > 0)
					{
						$cookie = trim(substr($arHeader[1], 0, $pos));
					}
					else
					{
						$cookie = trim($arHeader[1]);
					}
					$arCookie = explode('=', $cookie, 2);
					$this->cookies[$arCookie[0]] = rawurldecode($arCookie[1]);
				}
				else
				{
					$this->headers[$arHeader[0]] = trim($arHeader[1]);
				}
			}
		}
	}

	public function setFollowRedirect($follow)
	{
		$this->follow_redirect = $follow;
	}

	public static function sGet($url, $follow_redirect = false) //static get
	{
		$ob = new CbitrixCHttp();
		$ob->setFollowRedirect($follow_redirect);
		return $ob->Get($url);
	}

	public static function sPost($url, $arPostData, $follow_redirect = false) //static post
	{
		$ob = new CbitrixCHttp();
		$ob->setFollowRedirect($follow_redirect);
		return $ob->Post($url, $arPostData);
	}

	public static function sHead($url, $arPostData, $follow_redirect = false) //static post
	{
		$ob = new CbitrixCHttp();
		$ob->setFollowRedirect($follow_redirect);
		return $ob->Head($url, $arPostData);
	}	

	public static function SetStatus($status)
	{
		$bCgi = (stristr(php_sapi_name(), "cgi") !== false);
		$bFastCgi = ($bCgi && (array_key_exists('FCGI_ROLE', $_SERVER) || array_key_exists('FCGI_ROLE', $_ENV)));
		if($bCgi && !$bFastCgi)
			header("Status: ".$status);
		else
			header($_SERVER["SERVER_PROTOCOL"]." ".$status);
	}

	public static function SetAuthHeader($bDigestEnabled=true)
	{
		self::SetStatus('401 Unauthorized');

		if(defined('BX_HTTP_AUTH_REALM'))
			$realm = BX_HTTP_AUTH_REALM;
		else
			$realm = "Bitrix Site Manager";

		header('WWW-Authenticate: Basic realm="'.$realm.'"');

		if($bDigestEnabled !== false && COption::GetOptionString("main", "use_digest_auth", "N") == "Y")
		{
			// On first try we found that we don't know user digest hash. Let ask only Basic auth first.
			if($_SESSION["BX_HTTP_DIGEST_ABSENT"] !== true)
				header('WWW-Authenticate: Digest realm="'.$realm.'", nonce="'.uniqid().'"');
		}
	}

	public static function ParseAuthRequest()
	{
		$sDigest = '';

		if(isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] <> '')
		{
			// Basic Authorization PHP module
			return array("basic"=>array(
				"username"=>$_SERVER['PHP_AUTH_USER'],
				"password"=>$_SERVER['PHP_AUTH_PW'],
			));
		}
		elseif(isset($_SERVER['PHP_AUTH_DIGEST']) && $_SERVER['PHP_AUTH_DIGEST'] <> '')
		{
			// Digest Authorization PHP module
			$sDigest = $_SERVER['PHP_AUTH_DIGEST'];
		}
		else
		{
			if(isset($_SERVER['REDIRECT_REMOTE_USER']) || isset($_SERVER['REMOTE_USER']))
			{
				$res = (isset($_SERVER['REDIRECT_REMOTE_USER'])? $_SERVER['REDIRECT_REMOTE_USER'] : $_SERVER['REMOTE_USER']);
				if($res <> '')
				{
					if(preg_match('/(?<=(basic\s))(.*)$/is', $res, $matches))
					{
						// Basic Authorization PHP FastCGI (CGI)
						$res = trim($matches[0]);
					    list($user, $pass) = explode(':', base64_decode($res));
			            if(strpos($user, $_SERVER['HTTP_HOST']."\\") === 0)
			                $user = str_replace($_SERVER['HTTP_HOST']."\\", "", $user);
			            elseif(strpos($user, $_SERVER['SERVER_NAME']."\\") === 0)
			                $user = str_replace($_SERVER['SERVER_NAME']."\\", "", $user);

						return array("basic"=>array(
							"username"=>$user,
							"password"=>$pass,
						));
					}
					elseif(preg_match('/(?<=(digest\s))(.*)$/is', $res, $matches))
					{
						// Digest Authorization PHP FastCGI (CGI)
						$sDigest = trim($matches[0]);
					}
				}
			}
		}

		if($sDigest <> '' && ($data = self::ParseDigest($sDigest)))
			return array("digest"=>$data);

		return false;
	}

	public static function ParseDigest($sDigest)
	{
		$data = array();
		$needed_parts = array('nonce'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
		$keys = implode('|', array_keys($needed_parts));

		//from php help
		preg_match_all('@('.$keys.')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $sDigest, $matches, PREG_SET_ORDER);

		foreach ($matches as $m)
		{
			$data[$m[1]] = ($m[3]? $m[3] : $m[4]);
			unset($needed_parts[$m[1]]);
		}

		return ($needed_parts? false : $data);
	}

	public static function urlAddParams($url, $add_params, $options = array())
	{
		if(count($add_params))
		{
			$params = array();
			foreach($add_params as $name => $value)
			{
				if($options["skip_empty"] && !strlen($value))
					continue;
				if($options["encode"])
					$params[] = urlencode($name).'='.urlencode($value);
				else
					$params[] = $name.'='.$value;
			}

			if(count($params))
			{
				$p1 = strpos($url, "?");
				if($p1 === false)
					$ch = "?";
				else
					$ch = "&";

				$p2 = strpos($url, "#", $p1);
				if($p2===false)
				{
					$url = $url.$ch.implode("&", $params);
				}
				else
				{
					$url = substr($url, 0, $p2).$ch.implode("&", $params).substr($url, $p2);
				}
			}
		}
		return $url;
	}

	public static function urlDeleteParams($url, $delete_params, $options = array())
	{
		if(count($delete_params))
		{
			$url_parts = explode("?", $url, 2);
			if(count($url_parts) == 2 && strlen($url_parts[1]) > 0)
			{
				if($options["delete_system_params"])
					$delete_params = array_merge($delete_params, array(
						"login",
						"logout",
						"register",
						"forgot_password",
						"change_password",
						"confirm_registration",
						"confirm_code",
						"confirm_user_id",
						"bitrix_include_areas",
						"clear_cache",
						"show_page_exec_time",
						"show_include_exec_time",
						"show_sql_stat",
						"show_link_stat",
					));

				$params_pairs = explode("&", $url_parts[1]);
				foreach($params_pairs as $i => $param_pair)
				{
					$name_value_pair = explode("=", $param_pair, 2);
					if(count($name_value_pair) == 2 && in_array($name_value_pair[0], $delete_params))
						unset($params_pairs[$i]);
				}

				if(empty($params_pairs))
					return $url_parts[0];
				else
					return $url_parts[0]."?".implode("&", $params_pairs);
			}
		}

		return $url;
	}
}


function GetMenuTypes($site=false, $default_value=false)
{
	if($default_value === false)
		$default_value = "left=".GetMessage("main_tools_menu_left").",top=".GetMessage("main_tools_menu_top");

	$mt = COption::GetOptionString("fileman", "menutypes", $default_value, $site);
	if (!$mt)
		return Array();

	$armt_ = unserialize(stripslashes($mt));
	$armt = Array();
	if (is_array($armt_))
	{
		foreach($armt_ as $key => $title)
		{
			$key = trim($key);
			if (strlen($key) == 0)
				continue;
			$armt[$key] = trim($title);
		}
		return $armt;
	}

	$armt_ = explode(",", $mt);
	for ($i = 0, $c = count($armt_); $i < $c; $i++)
	{
		$pos = strpos($armt_[$i], '=');
		if ($pos === false)
			continue;
		$key = trim(substr($armt_[$i], 0, $pos));
		if (strlen($key) == 0)
			continue;
		$armt[$key] = trim(substr($armt_[$i], $pos + 1));
	}
	return $armt;
}

function SetMenuTypes($armt, $site = '', $description = false)
{
	COption::SetOptionString('fileman', "menutypes", addslashes(serialize($armt)), $description, $site);
}

function ParseFileContent($filesrc)
{
	/////////////////////////////////////
	// Parse prolog, epilog, title
	/////////////////////////////////////
	$filesrc = trim($filesrc);

	$php_doubleq = false;
	$php_singleq = false;
	$php_comment = false;
	$php_star_comment = false;
	$php_line_comment = false;

	$php_st = "<"."?";
	$php_ed = "?".">";

	if(substr($filesrc, 0, 2)==$php_st)
	{
		$fl = strlen($filesrc);
		$p = 2;
		while($p < $fl)
		{
			$ch2 = substr($filesrc, $p, 2);
			$ch1 = substr($ch2, 0, 1);

			if($ch2==$php_ed && !$php_doubleq && !$php_singleq && !$php_star_comment)
			{
				$p+=2;
				break;
			}
			elseif(!$php_comment && $ch2=="//" && !$php_doubleq && !$php_singleq)
			{
				$php_comment = $php_line_comment = true;
				$p++;
			}
			elseif($php_line_comment && ($ch1=="\n" || $ch1=="\r" || $ch2=="?>"))
			{
				$php_comment = $php_line_comment = false;
			}
			elseif(!$php_comment && $ch2=="/*" && !$php_doubleq && !$php_singleq)
			{
				$php_comment = $php_star_comment = true;
				$p++;
			}
			elseif($php_star_comment && $ch2=="*/")
			{
				$php_comment = $php_star_comment = false;
				$p++;
			}
			elseif(!$php_comment)
			{
				if(($php_doubleq || $php_singleq) && $ch2=="\\\\")
				{
					$p++;
				}
				elseif(!$php_doubleq && $ch1=='"')
				{
					$php_doubleq=true;
				}
				elseif($php_doubleq && $ch1=='"' && substr($filesrc, $p-1, 1)!='\\')
				{
					$php_doubleq=false;
				}
				elseif(!$php_doubleq)
				{
					if(!$php_singleq && $ch1=="'")
					{
						$php_singleq=true;
					}
					elseif($php_singleq && $ch1=="'" && substr($filesrc, $p-1, 1)!='\\')
					{
						$php_singleq=false;
					}
				}
			}

			$p++;
		}

		$prolog = substr($filesrc, 0, $p);
		$filesrc = substr($filesrc, $p);
	}
	elseif(preg_match("'(.*?<title>.*?</title>)(.*)$'is", $filesrc, $reg))
	{
		$prolog = $reg[1];
		$filesrc= $reg[2];
	}

	$title = false;
	if(strlen($prolog))
	{
		if(preg_match("/\\\$APPLICATION->SetTitle\\s*\\(\\s*\"(.*?)(?<!\\\\)\"\\s*\\);/is", $prolog, $regs))
			$title = UnEscapePHPString($regs[1]);
		elseif(preg_match("/\\\$APPLICATION->SetTitle\\s*\\(\\s*'(.*?)(?<!\\\\)'\\s*\\);/is", $prolog, $regs))
			$title = UnEscapePHPString($regs[1]);
		elseif(preg_match("'<title[^>]*>([^>]+)</title[^>]*>'i", $prolog, $regs))
			$title = $regs[1];
	}

	if(!$title && preg_match("'<title[^>]*>([^>]+)</title[^>]*>'i", $filesrc, $regs))
		$title = $regs[1];

	$arPageProps = array();
	if (strlen($prolog)>0)
	{
		preg_match_all("'\\\$APPLICATION->SetPageProperty\(\"(.*?)(?<!\\\\)\" *, *\"(.*?)(?<!\\\\)\"\);'i", $prolog, $out);
		if (count($out[0])>0)
		{
			for ($i1 = 0; $i1 < count($out[0]); $i1++)
			{
				$arPageProps[UnEscapePHPString($out[1][$i1])] = UnEscapePHPString($out[2][$i1]);
			}
		}
	}

	if(substr($filesrc, -2)=="?".">")
	{
		$p = strlen($filesrc) - 2;
		$php_start = "<"."?";
		while(($p > 0) && (substr($filesrc, $p, 2) != $php_start))
			$p--;
		$epilog = substr($filesrc, $p);
		$filesrc = substr($filesrc, 0, $p);
	}

	return Array(
			"PROLOG"=>$prolog,
			"TITLE"=>$title,
			"PROPERTIES"=>$arPageProps,
			"CONTENT"=>$filesrc,
			"EPILOG"=>$epilog
			);
}

function EscapePHPString($str)
{
	$str = str_replace("\\", "\\\\", $str);
	$str = str_replace("\$", "\\\$", $str);
	$str = str_replace("\"", "\\"."\"", $str);
	return $str;
}

function UnEscapePHPString($str)
{
	$str = str_replace("\\\\", "\\", $str);
	$str = str_replace("\\\$", "\$", $str);
	$str = str_replace("\\\"", "\"", $str);
	return $str;
}

function CheckSerializedData($str, $max_depth = 20)
{
	if (preg_match('/O\\:\\d/', $str)) return false; // serialized objects

	// check max depth
	$str1 = preg_replace('/[^{}]+/'.BX_UTF_PCRE_MODIFIER, '', $str);
	$cnt = 0;
	for ($i=0,$len=strlen($str1);$i<$len;$i++)
	{
		// we've just cleared all possible utf-symbols, so we can use [] syntax
		if ($str1[$i]=='}')
			$cnt--;
		else
		{
			$cnt++;
			if ($cnt > $max_depth)
				break;
		}
	}

	return $cnt <= $max_depth;
}

function bxmail($to, $subject, $message, $additional_headers="", $additional_parameters="")
{
	if(function_exists("custom_mail"))
		return custom_mail($to, $subject, $message, $additional_headers, $additional_parameters);

	if($additional_parameters!="")
		return @mail($to, $subject, $message, $additional_headers, $additional_parameters);

	return @mail($to, $subject, $message, $additional_headers);
}

function bx_accelerator_reset()
{
	if(defined("BX_NO_ACCELERATOR_RESET"))
		return;
	if(function_exists("accelerator_reset"))
		accelerator_reset();
	elseif(function_exists("wincache_refresh_if_changed"))
		wincache_refresh_if_changed();
}

class UpdateTools
{
	function CheckUpdates()
	{
		if(LICENSE_KEY == "DEMO")
			return;

		$days_check = intval(COption::GetOptionString('main', 'update_autocheck'));
		if($days_check > 0)
		{
			CUtil::SetPopupOptions('update_tooltip', array('display'=>'on'));

			$update_res = unserialize(COption::GetOptionString('main', '~update_autocheck_result'));
			if(!is_array($update_res))
				$update_res = array("check_date"=>0, "result"=>false);

			if(time() > $update_res["check_date"]+$days_check*86400)
			{
				if($GLOBALS["USER"]->CanDoOperation('install_updates'))
				{
					require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client.php");

					$result = CUpdateClient::IsUpdateAvailable($arModules, $strError);

					$modules = array();
					foreach($arModules as $module)
						$modules[] = $module["@"]["ID"];

					if($strError <> '' && COption::GetOptionString('main', 'update_stop_autocheck', 'N') == 'Y')
						COption::SetOptionString('main', 'update_autocheck', '');

					COption::SetOptionString('main', '~update_autocheck_result', serialize(array(
						"check_date"=>time(),
						"result"=>$result,
						"error"=>$strError,
						"modules"=>$modules,
					)));
				}
			}
		}
	}

	function SetUpdateResult()
	{
		COption::SetOptionString('main', '~update_autocheck_result', serialize(array(
			"check_date"=>time(),
			"result"=>false,
			"error"=>"",
			"modules"=>array(),
		)));
	}

	function SetUpdateError($strError)
	{
		$update_res = unserialize(COption::GetOptionString('main', '~update_autocheck_result'));
		if(!is_array($update_res))
			$update_res = array("check_date"=>0, "result"=>false);

		if($strError <> '')
			$update_res["result"] = false;
		$update_res["error"] = $strError;

		COption::SetOptionString('main', '~update_autocheck_result', serialize($update_res));
	}

	function GetUpdateResult()
	{
		$update_res = false;
		if(intval(COption::GetOptionString('main', 'update_autocheck')) > 0)
			$update_res = unserialize(COption::GetOptionString('main', '~update_autocheck_result'));
		if(!is_array($update_res))
			$update_res = array("result"=>false, "error"=>"", "modules"=>array());

		$update_res['tooltip'] = '';
		if($update_res["result"] == true || $update_res["error"] <> '')
		{
			$updOptions = CUtil::GetPopupOptions('update_tooltip');
			if($updOptions['display'] <> 'off')
			{
				if($update_res["result"] == true)
					$update_res['tooltip'] = GetMessage("top_panel_updates").(($n = count($update_res["modules"])) > 0? GetMessage("top_panel_updates_modules", array("#MODULE_COUNT#"=>$n)) : '').'<br><a href="/bitrix/admin/update_system.php?lang='.LANGUAGE_ID.'">'.GetMessage("top_panel_updates_settings1").'</a>';
				elseif($update_res["error"] <> '')
					$update_res['tooltip'] = GetMessage("top_panel_updates_err").' '.$update_res["error"].'<br><a href="/bitrix/admin/settings.php?lang='.LANGUAGE_ID.'&amp;mid=main&amp;tabControl_active_tab=edit5">'.GetMessage("top_panel_updates_settings").'</a>';
			}
		}

		return $update_res;
	}
}
?>