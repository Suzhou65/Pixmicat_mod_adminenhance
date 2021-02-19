<?php
//在此特別明謝 ssk7833(North)協助開發此模組，為了蔚藍而潔淨的網路世界出了一份力

class mod_pushpost extends ModuleHelper {
		private $PUSHPOST_SEPARATOR = '[MOD_PUSHPOST_USE]';
		private $PUSHPOST_DEF = 15;
		private $PUSH_EMOTIONS = array("","(ﾟ∀ﾟ)","(ﾟ3ﾟ)","(*ﾟ∀ﾟ*)","(ﾟ∀。)","∀ﾟ)ノ","Σ(ﾟдﾟ)","(;ﾟдﾟ)","(ﾟдﾟ)","(д)ﾟﾟ","(╬ﾟдﾟ)","(σﾟдﾟ)σ","(´◓Д◔`)Ԡ","(´ﾟдﾟ`)","(;´д`)","(つд⊂)","(ﾉд`ﾟ)","(*´д`)","(-д-)","(TдT)","(ノﾟ∀ﾟ)ノ","(ゝ∀･)","(´∀`)","(*´∀`)","(〃∀〃)","σ`∀´)","(・∀・)","ﾟ∀ﾟ)σ","|∀ﾟ)","⊂彡☆))∀`)","(ฅ´ω`ฅ)","(｀･ω･)","(´・ω)","(・ω・)","(*´ω`*)","(oﾟωﾟo)","(´・ω・`)","(`・ω・´)","(｀・ω)","(^ω^)","(`_っ´)","(´,_ゝ`)","(´_ゝ`)","(・_ゝ・)","(・ー・)","(・_っ・)","(´ρ`)","(｡◕∀◕｡)","(･ิ㉨･ิ)","ヾ(´ε`ヾ)","(ノ・ω・)ノ","ヽ(●´∀`●)ﾉ","ﾟÅﾟ)","(ﾟдﾟ≡ﾟдﾟ)","(σﾟ∀ﾟ)σ","|ー`)","|д`)","|дﾟ)","(´ー`)","(*ﾟーﾟ)",
);
		 private $GESTURES = array("","＜","｡o０","ﾉｼ","彡","o彡ﾟ","ゝ","ㄏ","ノ","ノ゛","ﾉ","ﾉミ","ツ","っ","っ゛","つ","つ゛","ｏ","凸","／","y","y━･~","y-～","≡3","～♪","♬","❤","~♥","σ","ﾉ彡┴─┴","⌒＊","⌒☆","⌒★","≡☆"
);

        public function __construct($PMS) {
                parent::__construct($PMS);
                $this->loadLanguage();
        }
        public function getModuleName() {
                return $this->moduleNameBuilder('Emotion Pushpost Module / AAE');
        }
        public function getModuleVersionInfo() {
                return '7th, Fenrisulfr Customize Modified, 20161025';
        }
        private function getID() {
                return substr(crypt(md5(getREMOTE_ADDR().IDSEED.gmdate('Ymd', time() + TIME_ZONE * 3600)), 'id'), -8);
        }
        public function autoHookHead(&$txt, $isReply) {
                $txt .= '<style type="text/css">.pushpost { background-color: #fff; font-size: 0.8rem; padding: 10px; }</style>
<script type="text/javascript">
// <![CDATA[
var lastpushpost=0;
function mod_pushpostShow(pid){
        $g("mod_pushpostID").value = pid;
        $g("mod_pushpostName").value = getCookie("namec");
        $("div#mod_pushpostBOX").insertBefore($("div#r"+pid+" .quote"));

        if(lastpushpost!=pid) {
                $("div#mod_pushpostBOX").show();
        } else
                $("div#mod_pushpostBOX").toggle();
        lastpushpost = pid;
        return false;
}
function mod_pushpostKeyPress(e){if(e.which==13){e.preventDefault();mod_pushpostSend();}}
function mod_pushpostSend(){
        var o0 = $g("mod_pushpostID"), o1 = $g("mod_pushpostName"), o2 = $g("mod_pushpostComm"), o3 = $g("mod_pushpostSmb"), pp = $("div#r"+o0.value+" .quote"), o4 = $("#push_emotion option:selected").val(), o5 = $("#gesture option:selected").val();
        if(o2.value===""){ alert("'.$this->_T('nocomment').'"); return false; }
        o1.disabled = o2.disabled = o3.disabled = true;
        $.ajax({
                url: "'.str_replace('&amp;', '&', $this->getModulePageURL()).'&no="+o0.value,
                type: "POST",
                data: {ajaxmode: true, name: o1.value, push_emotion: o4, gesture: o5, comm: o2.value},
                success: function(rv){
                        if(rv.substr(0, 4)!=="+OK "){ alert(rv); o3.disabled = false; return false; }
                        rv = rv.substr(4);
                        (pp.find(".pushpost").length===0)
                                ? pp.append("<div class=\'pushpost\'>"+rv+"</div>")
                                : pp.children(".pushpost").append("<br />"+rv);
                        o0.value = o1.value = o2.value = o4 = o5 = ""; o1.disabled = o2.disabled = o3.disabled = false;
                        $("div#mod_pushpostBOX").hide();
                },
                error: function(){ alert("Network error."); o1.disabled = o2.disabled = o3.disabled = false; }
        });
}
// ]]>
</script>';
        }

        public function autoHookFoot(&$foot) {
                $ecnt=count($this->PUSH_EMOTIONS);
                $gcnt=count($this->GESTURES);
                for($i=0;$i<$ecnt;$i++) {
                        $emot.="<option>".$this->PUSH_EMOTIONS[$i]."</option>\n";
                }
                for($i=0;$i<$gcnt;$i++) {
                        $gest.="<option>".$this->GESTURES[$i]."</option>\n";
                }
                $foot .= '
<div id="mod_pushpostBOX" style="display:none">
<input type="hidden" id="mod_pushpostID" />'.$this->_T('pushpost').' <ul>
<li>'._T('form_name').' <input type="text" id="mod_pushpostName" maxlength="20" onkeypress="mod_pushpostKeyPress(event)" /></li>
<li>'._T('form_comment').
'<select id="push_emotion" style="text-align:right;line-height:1.7em" class="push_area">'.$emot.'</select>'.
'<select id="gesture" class="push_area" style="line-height:1.7em">'.$gest.'</select>'.
'<input type="text" id="mod_pushpostComm" size="50" maxlength="50" onkeypress="mod_pushpostKeyPress(event)" /><input type="button" id="mod_pushpostSmb" value="'._T('form_submit_btn').'" onclick="mod_pushpostSend()" /></li></ul>
</div>
';
        }

        public function autoHookThreadPost(&$arrLabels, $post, $isReply) {
                $PIO = PMCLibrary::getPIOInstance();
                $pushcount = '';
                if ($post['status'] != '') {
                        $f = $PIO->getPostStatus($post['status']);
                        $pushcount = $f->value('mppCnt'); // 被推次數
                }
                $arrLabels['{$QUOTEBTN}'] .= '&nbsp;<a href="'.
				        $this->getModulePageURL(array('no'=> $post['no'])).
						'" onclick="return mod_pushpostShow('.$post['no'].')">'.
						$pushcount.$this->_T('pushbutton').'</a>';
               if (strpos($arrLabels['{$COM}'], $this->PUSHPOST_SEPARATOR.'<br />') !== false) {
			            // 回應模式
                        if ($isReply || $pushcount <= $this->PUSHPOST_DEF) {
						         $arrLabels['{$COM}'] = str_replace($this->PUSHPOST_SEPARATOR.
                                 '<br />', '<div class="pushpost">', $arrLabels['{$COM}']).
								 '</div>';
                        } else {
						// 頁面瀏覽
						         // 定位符號位置
                                $delimiter = strpos($arrLabels['{$COM}'], $this->PUSHPOST_SEPARATOR.'<br />');
                                if ($this->PUSHPOST_DEF > 0) {
                                        $push_array = explode('<br />', substr($arrLabels['{$COM}'], $delimiter + strlen($this->PUSHPOST_SEPARATOR.'<br />')));
                                        $pushs = '<div class="pushpost">……<br />'.implode('<br />', array_slice($push_array, 0 - $this->PUSHPOST_DEF)).'</div>';
                                } else {
                                        $pushs = '';
                                }
                                $arrLabels['{$COM}'] = substr($arrLabels['{$COM}'], 0, $delimiter).$pushs;
                                $arrLabels['{$WARN_BEKILL}'] .= '<span class="warn_txt2">'.$this->_T('omitted').'<br /></span>'."\n";
                        }
                }
        }
        public function autoHookThreadReply(&$arrLabels, $post, $isReply) {
                $this->autoHookThreadPost($arrLabels, $post, $isReply);
        }

        public function autoHookRegistBegin(&$name, &$email, &$sub, &$com, $upfileInfo, $accessInfo, $isReply) {
				if (adminAuthenticate('check')) return;
                if (strpos($com, $this->PUSHPOST_SEPARATOR."\r\n") !== false) {
                        $com = str_replace($this->PUSHPOST_SEPARATOR."\r\n", "\r\n", $com);
                }
        }
        public function autoHookAdminList(&$modFunc, $post, $isres) {
                $modFunc .= '[<a href="'.$this->getModulePageURL(
				                        array(
							                  'action' => 'del',
										      'no' => $post[no]
										)
						  ).'">刪推</a>]';
        }
        public function ModulePage() {
					global $BAD_STRING, $BAD_FILEMD5, $BAD_IPADDR, $LIMIT_SENSOR, $THUMB_SETTING, $BANNED_TIME;
					$PIO = PMCLibrary::getPIOInstance();
					$FileIO = PMCLibrary::getFileIOInstance();
					$PMS = PMCLibrary::getPMSInstance();

					$PMS->useModuleMethods('RegistBegin', array(&$name, &$email, &$sub, &$com, array('file'=>&$upfile, 'path'=>&$upfile_path, 'name'=>&$upfile_name, 'status'=>&$upfile_status), array('ip'=>$ip, 'host'=>$host), $resto)); // "RegistBegin" Hook Point
					      if (!isset($_GET['no'])) die('[Error] not enough parameter.');
                if (isset($_GET['action'])) {
                        if (adminAuthenticate('check')) {
                                $pushcount = ''; $puststart=0;
                                $post = $PIO->fetchPosts($_GET['no']);
                                if (!count($post)) die('[Error] Post does not exist.');
                                extract($post[0]);
                                if ($status != ''){
                                        $f = $PIO->getPostStatus($status);
                                        $pushcount = $f->value('mppCnt');
                                }
                                if (($puststart=strpos($com, $this->PUSHPOST_SEPARATOR.'<br />'))===false) die('[Error] No pushpost.');
                                $ocom = substr($com,0,$puststart);
                                $pushpost = explode('<br />',substr($com,$puststart+strlen($this->PUSHPOST_SEPARATOR.'<br />')));
                                $com = $ocom;
                                if ($_GET['action'] == 'del') { // list
                                        $p_count = 1;
                                        $com .= '<div class="pushpost">';
                                        foreach($pushpost as $p) {
                                                $com .= '<input type="checkbox" name="'.($p_count++).'" value="delete" />'.$p.'<br />';
										}
                                        $com .= '</div>';
                                        $dat = '';
                                        head($dat);
                                        $dat .= '<div class="bar_reply">'.$this->_T('deletepush').'</div>';
                                        $dat .= '<form action="'.$this->getModulePageURL(
                                                array(
												       'action'=>'delpush',
													   'no' => $_GET['no']
													  )
                                                 ).'" method="post">';
                                        $dat .= PMCLibrary::getPTEInstance()->ParseBlock('SEARCHRESULT',
                                                array(
                                                        '{$NO}'=>$no, '{$SUB}'=>$sub, '{$NAME}'=>$name,
                                                        '{$NOW}'=>$now, '{$COM}'=>$com, '{$CATEGORY}'=>$category,
                                                        '{$NAME_TEXT}'=>_T('post_name'), '{$CATEGORY_TEXT}'=>_T('post_category')
                                                )
                                        );
                                        echo $dat, '<input type="submit" value="'._T('del_btn').'" /></form></body></html>';
                                        return;
                                } else if($_GET['action'] == 'delpush') { // delete
                                        $delno = array();
                                        reset($_POST);
                                        while ($item = each($_POST)) {
										        if ($item[1]=='delete' && $item[0] != 'func')
												        array_push($delno, $item[0]);
										}
                                        if (count($delno)) {
                                                foreach($delno as $d) {
                                                        if(isset($pushpost[$d-1])) unset($pushpost[$d-1]);
                                                }
                                        }
                                        $pushcount = count($pushpost);
                                        if ($pushcount) {
                                                $f->update('mppCnt', $pushcount);
                                                $com = $ocom.$this->PUSHPOST_SEPARATOR.'<br />'.implode('<br />', $pushpost);
                                        } else {
                                                $f->remove('mppCnt');
                                                $com = $ocom;
                                        }
                                        $PIO->updatePost($_GET['no'], array('com' => $com, 'status' => $f->toString()));
                                        $PIO->dbCommit();
                                        header('HTTP/1.1 302 Moved Temporarily');
										header('Location: '.fullURL().PHP_SELF.'?page_num=0');
                                        return;
                                } else die('[Error] unknown action.');
                        } else die('[Error] unauthenticated action.');
                }
				if (!isset($_POST['comm'])) {
				        echo $this->printStaticForm(intval($_GET['no']));
				} else {
						 if ($_SERVER['REQUEST_METHOD'] != 'POST') {
						         die(_T('regist_notpost'));
						 }
                        $baninfo = '';
                        $ip = getREMOTE_ADDR();
						$host = gethostbyaddr($ip);
						if (BanIPHostDNSBLCheck($ip, $host, $baninfo)) {
                                die(_T('regist_ipfiltered', $baninfo));
						}
                        $name = CleanStr($_POST['name']);
						$comm = CleanStr($_POST['comm']);
						$push_emotion = CleanStr($_POST['push_emotion']);
						$gesture = CleanStr($_POST['gesture']);
						// 若使用者有被記錄封鎖 cookie，判斷是否已經過了封鎖時間
						// if($_COOKIE['time'] > time()) {
						// 	die($this->_T('regist_cookiebanned'));
						// }

						// 判斷是否有禁字在推文或名稱內
						foreach($BAD_STRING as $value){
							if(strpos($comm, $value)!==false || strpos($name, $value)!==false) {
								// 設定 cookie 記錄時間，讓使用者在這段時間內無法發文
								// setcookie('time', time()+3600*$BANNED_TIME);
								die($this->_T('regist_wordfiltered'));
							}
						}
						if (strlen($name) > 30) die($this->_T('maxlength'));
						if (strlen($comm) > 160) die($this->_T('maxlength'));
						if (strlen($comm) == 0) die($this->_T('nocomment'));
						$name = str_replace(
						        array(_T('trip_pre'), _T('admin'), _T('deletor')),
								array(_T('trip_pre_fake'), '"'._T('admin').'"', '"'._T('deletor').'"'),
								$name
						);
                        $pushID = $this->getID();
                        $pushtime = gmdate('y/m/d H:i', time() + intval(TIME_ZONE) * 3600);
                        if (preg_match('/(.*?)[#＃](.*)/u', $name, $regs)) {
                                $cap = strtr($regs[2], array('&amp;'=>'&'));
                                $salt = strtr(preg_replace('/[^\.-z]/', '.', substr($cap.'H.', 1, 2)), ':;<=>?@[\\]^_`', 'ABCDEFGabcdef');
                                $name = $regs[1]._T('trip_pre').substr(crypt($cap, $salt), -10);
                        }
                        if (!$name || preg_match("/^[ |　|]*$/", $name)) {
                                if (ALLOW_NONAME) $name = DEFAULT_NONAME;
                                else die(_T('regist_withoutname')); // 不接受匿名
                        }
                        if (ALLOW_NONAME == 2) { // 強制砍名
                                $name = preg_match('/(\\'._T('trip_pre').'.{10})/', $name, $matches) ? $matches[1].':' : DEFAULT_NONAME.':';
                        } else {
                                $name .= ':';
                        }
                        $pushpost = "{$name} {$push_emotion}{$gesture}{$comm} ({$pushID} {$pushtime})";
                        $post = $PIO->fetchPosts($_GET['no']);
                        if (!count($post)) die('[Error] Post does not exist.');
                        $parentNo = $post[0]['resto'] ? $post[0]['resto'] : $post[0]['no'];
                        $threads = array_flip($PIO->fetchThreadList());
                        $threadPage = floor($threads[$parentNo] / PAGE_DEF);
                        $p = ($parentNo==$post[0]['no']) ? $post : $PIO->fetchPosts($parentNo);
                        $flgh = $PIO->getPostStatus($p[0]['status']);
                        if ($flgh->exists('TS')) die('[Error] '._T('regist_threadlocked'));
                        $post[0]['com'] .= ((strpos($post[0]['com'], $this->PUSHPOST_SEPARATOR.'<br />')===false) ? '<br />'.$this->PUSHPOST_SEPARATOR : '').'<br /> '.$pushpost;
                        $flgh2 = $PIO->getPostStatus($post[0]['status']);
                        $flgh2->plus('mppCnt');
                        $PIO->updatePost($_GET['no'], array('com'=>$post[0]['com'], 'status'=>$flgh2->toString()));
                        $PIO->dbCommit();
					   $this->callCHP('mod_audit_logcat',
                                array(sprintf('[%s] No.%d %s (%s)',
                                        __CLASS__,
                                        $_GET['no'],
                                        $comm,
                                        $pushID)
                                )
                        );
                        if (STATIC_HTML_UNTIL == -1 || $threadPage <= STATIC_HTML_UNTIL) {
								updatelog(0, $threadPage, true);
						}
                        deleteCache(array($parentNo));
                        if (isset($_POST['ajaxmode'])) {
                                echo '+OK ', $pushpost;
                        } else {
                                header('HTTP/1.1 302 Moved Temporarily');
                                header('Location: '.fullURL().PHP_SELF2.'?'.time());
                        }
                }
        }
        private function printStaticForm($targetPost) {
		        $PIO = PMCLibrary::getPIOInstance();
				$PTE = PMCLibrary::getPTEInstance();
                $post = $PIO->fetchPosts($targetPost);
                if (!count($post)) die('[Error] Post does not exist.');
                $dat = $PTE->ParseBlock('HEADER', array('{$TITLE}'=>TITLE, '{$RESTO}'=>''));
				$dat .= '</head><body id="main">';
				$dat .= '<form action="'.$this->getModulePageURL(array('no' => $targetPost)).'" method="post">
'.$this->_T('pushpost').' <ul><li>'._T('form_name').' <input type="text" name="name" maxlength="20" /></li><li>'._T('form_comment').' <input type="text" name="comm"
size="50" maxlength="50" /><input type="submit" value="'._T('form_submit_btn').'" /></li></ul>
</form>';
                $dat .= '</body></html>';
				return $dat;
		}
        private function loadLanguage() {
                $lang = array(
                        'zh_TW' => array(
                                'nocomment' => '請輸入內文',
                                'pushpost' => '推文',
                                'pushbutton' => '推',
                                'maxlength' => '話太多了',
                                'omitted' => '部分推文被省略。閱讀全部推文請按下回應。',
                                'deletepush' => '刪推',
								'regist_wordfiltered' => '文章送出失敗',
								'regist_cookiebanned' => '文章送出失敗'
                        ),
                        'ja_JP' => array(
                                'nocomment' => '何か書いて下さい',
                                'pushpost' => '[推文]',
                                'pushbutton' => '推',
                                'maxlength' => 'コメントが長すぎます',
                                'omitted' => '推文省略。全て読むには返信ボタンを押してください。',
								'deletepush' => '削除推文モード'
						),
						'en_US' => array(
						        'nocomment' => 'Please type your comment.',
								'pushpost' => '[Push this post]',
								'pushbutton' => 'PUSH',
								'maxlength' => 'You typed too many words',
								'omitted' => 'Some pushs omitted. Click Reply to view.',
								'deletepush' => 'Delete Push Post Mode'
							)
                );
                $this->attachLanguage($lang, 'en_US');
		}
}
