<?php

//在此特別明謝 ssk7833(North)協助開發此模組，為了蔚藍而潔淨的網路世界出了一份力
ini_set('memory_limit', '64M');
//將記憶體限制設定為 64MB，預防大型過濾清單輸出異常
class mod_adminenhance extends ModuleHelper {
	 private $mypage;
	 private $ipfile = '.ht_List_IP';
	 // IP 封鎖清單
	 private $imgfile = '.ht_List_IMG_MD5';
     // 圖片封鎖清單(使用 MD5 識別)
	 private $wordfile = '.ht_List_WORDFILTER';
	 // 關鍵字封鎖清單
	 private $cookiefile = '.ht_List_BANTIME';
     // 紀錄如果命中過濾內容，將利用 cookie 封鎖多久(小時)

	public function __construct($PMS) {
		parent::__construct($PMS);
		$this->mypage = $this->getModulePageURL();
	}
	public function getModuleName() {
		return $this->moduleNameBuilder('Advanced Aministration Enhance Module / AAE');
	}
	public function getModuleVersionInfo() {
		return '7th, Fenrisulfr Customize Modified, 20161025';
	}

	// 從資料檔抓出資料
	private function _parseBlackListFile($fname, $only1st = false) {
	    if (!is_file($fname))
		   return array();
		$l = file($fname);
		$r = array();
		$autodelno = array();
		$tmp = '';
		$now = time();
		for ($i = 0, $len = count($l); $i < $len; $i++) {
			$tmp = explode("\t", rtrim($l[$i]));
			if (isset($tmp[3]) && $tmp[3] != '0') {
			        if ($tmp[2] + intval($tmp[3]) * 86400 < $now) {
					$autodelno[] = $i;
					continue;
				}
			}
			$r[] = $only1st ? $tmp[0] : $tmp;
		}
		if (count($autodelno)) {
		$this->_arrangeRecord($this->ipfile, $autodelno, '');
		}
		return $r;
	}

	// 重新整理記錄檔內容 (同步進行刪除及新增動作)
	private function _arrangeRecord($fname, $arrDel, $newline) {
		$line = is_file($fname) ? file($fname) : array();
		if (is_array($arrDel)) {
		        foreach($arrDel as $delid)
						unset($line[$delid]);
			}
		$line = implode('', $line).$newline;
		$fp = fopen($fname, 'w');
		fwrite($fp, $line);
		fclose($fp);
	}
	public function _showHostString(&$arrLabels, $post, $isReply) {
		$arrLabels['{$NOW}'] .= " <u>{$post['host']}</u>";
	}
	public function _hookHeadCSS(&$style, $isReply) {
		$style .= '<style type="text/css">
.dos_list_short {
	height: 200px;
	width: 85%;
	overflow: auto;
	background: #DDDDDD;
	border: 1px solid #AAAAAA;
}
</style>
<script type="text/javascript">
// <![CDATA[
function add(form){
	var op = form.operate.value, ndata = form.newdata.value, nperiod = form.newperiod.value, ndesc = form.newdesc.value;
	$.post("'.str_replace('&amp;', '&', $this->getModulePageURL()).'", {operate: op, newdata: ndata, newperiod: nperiod, newdesc: ndesc, ajax: true}, function(d){
		var l, lastno = (l = $("input:checkbox:last", form).get(0)) ? parseInt(l.value) + 1 : 0;
		$("table", form).append(d.replace("#NO#", lastno));
		form.newdata.value = form.newdesc.value = "";
	});
	return false;
}
// ]]>
</script>
';
	}
	public function autoHookRegistBegin() {
		global $BANPATTERN, $BAD_FILEMD5, $BAD_STRING, $BANNED_TIME;
		if (is_file($this->ipfile))
		       $BANPATTERN = array_merge($BANPATTERN, array_map('rtrim',
   			           $this->_parseBlackListFile($this->ipfile, true)
			   ));
		if (is_file($this->imgfile))
		       $BAD_FILEMD5 = array_merge($BAD_FILEMD5, array_map('rtrim',
			           $this->_parseBlackListFile($this->imgfile, true)
			   ));
		if (is_file($this->wordfile))
   		       $BAD_STRING = array_merge($BAD_STRING, array_map('rtrim',
			           $this->_parseBlackListFile($this->wordfile, true)
			   ));
	 if (is_file($this->cookiefile))
 		       $BANNED_TIME = $this->_parseBlackListFile($this->cookiefile, true)[0];
	}
	public function autoHookAdminFunction($action, &$param, $funcLabel, &$message) {
		if ($action=='add'){
			$this->hookModuleMethod('ThreadPost', array(&$this, '_showHostString'));
			$this->hookModuleMethod('ThreadReply', array(&$this, '_showHostString'));
			$param[] = array('mod_adminenhance_thstop', 'Stop/Release Thread');
			$param[] = array('mod_adminenhance_thsage', 'SAGE/DisSAGE');
			$param[] = array('mod_adminenhance_banip', 'HOST');
			$param[] = array('mod_adminenhance_banimg', 'MD5');
			return;
		}
		$PIO = PMCLibrary::getPIOInstance();
		switch ($funcLabel) {
			case 'mod_adminenhance_thstop':
				$infectThreads = array();
				foreach ($PIO->fetchPosts($param) as $th) {
					if ($th['resto']) continue;
					$infectThreads[] = $th['no'];
					$flgh = $PIO->getPostStatus($th['status']);
					$flgh->toggle('TS');
					$PIO->setPostStatus($th['no'], $flgh->toString());
				}
				$PIO->dbCommit();
				$message .= 'Stop/Release Thread (No.'.implode(', ', $infectThreads).') DONE<br />';
				break;
			case 'mod_adminenhance_thsage':
				$infectThreads = array();
				foreach ($PIO->fetchPosts($param) as $th){
					if ($th['resto']) continue;
					$infectThreads[] = $th['no'];
					$flgh = $PIO->getPostStatus($th['status']);
					$flgh->toggle('asage');
					$PIO->setPostStatus($th['no'], $flgh->toString());
				}
				$PIO->dbCommit();
				$message .= 'SAGE/DisSAGE (No.'.implode(', ', $infectThreads).') DONE<br />';
				break;
			case 'mod_adminenhance_banip':
				$fp = fopen($this->ipfile, 'a');
				foreach ($PIO->fetchPosts($param) as $th) {
					if (($IPaddr = gethostbyname($th['host'])) != $th['host'])
					        $IPaddr .= '/24';
					fwrite($fp, $IPaddr."\t\t".time()."\t0\n");
				}
				fclose($fp);
				$message .= 'HOST<br />';
				break;
			case 'mod_adminenhance_banimg':
				$fp = fopen($this->imgfile, 'a');
				foreach ($PIO->fetchPosts($param) as $th) {
					if ($th['md5chksum'])
					       fwrite($fp, $th['md5chksum']."\n");
				}
				fclose($fp);
				$message .= 'MD5<br />';
				break;
			default:
		}
	}
	public function autoHookLinksAboveBar(&$link, $pageId, $addinfo = false) {
		if ($pageId == 'admin' && $addinfo == true)
			$link .= '[<a href="'.$this->getModulePageURL().'"> HOST | MD5 | WordFilter | BannedTime </a>]';
	}
	public function autoHookThreadPost(&$arrLabels, $post, $isReply){
		$fh = new FlagHelper($post['status']);
		if($fh->value('asage')) {
			if($arrLabels['{$COM}']) $arrLabels['{$WARN_ENDREPLY}'].='<span class="warn_txt"><br/></span>';
			else $arrLabels['{$WARN_ENDREPLY}'] = '<span class="warn_txt"><br/></span>';
		}
	}
	public function autoHookThreadReply(&$arrLabels, $post, $isReply){
		$this->autoHookThreadPost($arrLabels, $post, $isReply);
	}
	public function autoHookRegistBeforeCommit(&$name, &$email, &$sub, &$com, &$category, &$age, $dest, $isReply, $imgWH, &$status){
	    $PIO = PMCLibrary::getPIOInstance();
		$fh = new FlagHelper($status);

		if($isReply) {
			$rpost = $PIO->fetchPosts($isReply);
			$rfh = new FlagHelper($rpost[0]['status']);
			if($rfh->value('asage')) $age = false;
		}
	}
	public function ModulePage(){
		if(isset($_GET['action'])) {
			if($_GET['action'] == 'listwords') {
				$dat = '';
				$dat .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />WordFilter Access Denied';
                //隱藏關鍵字過濾清單給想查詢的一般使用者
				echo $dat;
				return;
			}
		}
		if(!adminAuthenticate('check'))
		        die('[Error] Access Denied.');
		if (isset($_POST['operate'])) {
			$op = $_POST['operate'];
			$ndata = isset($_POST['newdata']) ?
			(get_magic_quotes_gpc() ? stripslashes($_POST['newdata']) :
		            $_POST['newdata']) : '';
			$nperiod = isset($_POST['newperiod']) ? intval($_POST['newperiod']) : 0;
			$ndesc = isset($_POST['newdesc']) ? CleanStr($_POST['newdesc']) : '';
			$del = isset($_POST['del']) ? $_POST['del'] : null;
			$newline = '';
			$ismodified = ($ndata != '' || $del != null);
			if ($ismodified) {
				switch ($op) {
					case 'ip':
						$file = $this->ipfile;
						if($ndata != '')
						       $newline = $ndata."\t".$ndesc."\t".time()."\t".$nperiod."\n";
						break;
					case 'img':
						$file = $this->imgfile;
						if($ndata != '')
						       $newline = $ndata."\t".$ndesc."\n";
						break;
					case 'word':
						$file = $this->wordfile;
						if($ndata != '')
						       $newline = $ndata."\t".$ndesc."\n";
						break;
					case 'bannedTime':
						$file = $this->cookiefile;
						if($ndata != '') {
						       $newline = $ndata;
									 // 刪除原本在檔案中的內容
									 $del[0] = "0";
						}
						break;
				}
				$this->_arrangeRecord($file, $del, $newline);
			}
			if (isset($_POST['ajax'])) {
				$extend = ($op=='ip') ?
				        '<td>'.date('Y/m/d H:m:s', time())." ($nperiod)</td>" : '';
				echo '<tr><td>'.htmlspecialchars($ndata).'</td><td>'.$ndesc.
				        '</td>'.$extend.'<td><input type="checkbox" name="del[]" value="#NO#" /></td></tr>';
				return;
			}
		}
		$dat = '';
		$this->hookModuleMethod('Head', array(&$this, '_hookHeadCSS'));
		head($dat);
		$dat .= '<div class="bar_admin">Filter </div>
<div id="content">
<br/>
<form action="'.$this->getModulePageURL().'" method="post">
<div id="ipconfig"><input type="hidden" name="operate" value="ip"/>
HOST- <input type="text" name="newdata"/>
EXP- <input type="text" name="newperiod" value="0" />DAYS |
NOTE- <input type="text" name="newdesc"/>
<input type="submit" value="ADD" onclick="return add(this.form);" /><br/><br/>
<div class="dos_list_short">
<table border="0" width="100%">
<tr><td>HOST</td><td>NOTE</td><td>MFD | EXP</td><td>DEL</td></tr>';
		foreach ($this->_parseBlackListFile($this->ipfile) as $i => $l) {
			$dat .= '<tr><td>'.htmlspecialchars($l[0]).'</td><td>'.(isset($l[1]) ? $l[1] : '').'</td>'.
			'<td>'.(isset($l[2]) ? date('Y/m/d H:m:s', $l[2]) : '-').(isset($l[3]) ? ' ('.$l[3].')' : ' (0)').'</td>'.
			'<td><input type="checkbox" name="del[]" value="'.$i.'" /></td></tr>'."\n";
		}
		$dat .= '</table>
</div>
<input type="submit" value="DEL" /><br/>
</div>
</form>
<form action="'.$this->getModulePageURL().'" method="post">
<div id="imgconfig"><input type="hidden" name="operate" value="img"/>
<br />
MD5- <input type="text" name="newdata"/>
NOTE- <input type="text" name="newdesc" value="註解"/>
<input type="hidden" name="newperiod" value="0"/>
<input type="submit" value="ADD" onclick="return add(this.form);" /><br/><br/>
<div class="dos_list_short">
<table border="0" width="100%">
<tr><td>MD5</td><td>NOTE</td><td>DEL</td></tr>';
		foreach ($this->_parseBlackListFile($this->imgfile) as $i => $l) {
			    $dat .= '<tr><td>'.htmlspecialchars($l[0]).'</td><td>'.
				        (isset($l[1]) ? $l[1] : '').'</td><td><input type="checkbox" name="del[]" value="'.$i.'" /></td></tr>'."\n";
		}
		$dat .= '</table>
</div>
<input type="submit" value="DEL" /><br/>
</div>
</form>
<form action="'.$this->getModulePageURL().'" method="post">
<div id="wordconfig"><input type="hidden" name="operate" value="word"/>
<br />
WordFilter- <input type="text" name="newdata"/>
NOTE- <input type="text" name="newdesc" value="註解"/>
<input type="submit" value="ADD" onclick="return add(this.form);" /><br/><br/>
<div class="dos_list_short">
<table border="0" width="100%">
<tr><td>WordFilter</td><td>NOTE</td><td>DEL</td></tr>';
		foreach ($this->_parseBlackListFile($this->wordfile) as $i => $l){
			    $dat .= '<tr><td>'.htmlspecialchars($l[0]).'</td><td>'.
				        (isset($l[1]) ? $l[1] : '').'</td><td><input type="checkbox" name="del[]" value="'.$i.'" /></td></tr>'."\n";
		}
		$dat .= '</table>
</div>
<input type="submit" value="DEL" /><br/>
</div>
</form>
<form action="'.$this->getModulePageURL().'" method="post">
<div id="wordconfig"><input type="hidden" name="operate" value="bannedTime"/>
<br/>
封鎖時間（擇一輸入）：天- <input type="number" name="byDay"/>
小時- <input type="number" name="byHour"/>
<input type="hidden" name="newdata"/>
<input type="submit" value="UPDATE" onclick="return bannedTime(this.form);" /><br/><br/>

<div class="display">封鎖 <span style="color:red;">';
$dat .= $this->_parseBlackListFile($this->cookiefile)[0][0];
$dat .= '</span> 小時</div>
</div>
</form>
<script type="text/javascript">
// <![CDATA[
var ElembyDay = document.querySelector("input[name=byDay]");
var ElembyHour = document.querySelector("input[name=byHour]");
ElembyDay.addEventListener(\'focus\', function() { ElembyHour.value = \'\'; });
ElembyHour.addEventListener(\'focus\', function() { ElembyDay.value = \'\'; });

function bannedTime(form){
	var byDay = form.byDay.value;
	var byHour = form.byHour.value;
	var time = byHour;
	if(byDay!==\'\')
		time = byDay * 24;
	if(time===\'\')
		return false;
	form.newdata.value = time;
}
// ]]>
</script>
<hr/>
<div id="HELP"><front font-size=1.0rem;>
說明文件請參閱<a href="https://github.com/pixmicat/pixmicat_modules/blob/develop/mod_adminenhance/mod_adminenhance.php#L259">Pixmicat | Pixmicat_Modules | GitHub</a>
</front></div></div>';
		foot($dat);
		echo $dat;
	}
}
