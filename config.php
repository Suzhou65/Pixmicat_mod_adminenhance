<?php

// 這個檔案是位在 pixmicat/config.php 的設定檔案之指定行列
// 完整檔案請參閱 https://github.com/pixmicat/pixmicat/blob/develop/config.php#L93 
// 請用這份檔案取代該部分，下列的註解可刪除

// 封鎖設定
define("BAN_CHECK", 1);
$BANPATTERN = array();
$DNSBLservers = array(8, 
'tor.dan.me.uk', // 用於過濾所有 Tor 節點
'exitnodes.tor.dnsbl.sectoor.de', // 用於過濾 Tor 出口節點
'http.dnsbl.sorbs.net',  // 用於過濾 HTTP 協議的公開代理伺服器
'socks.dnsbl.sorbs.net',  // 用於過濾 Socks 協議的代理伺服器
'misc.dnsbl.sorbs.net',  // 用於過濾第三類代理伺服器
'zombie.dnsbl.sorbs.net',  // 用於過濾受到感染的電腦，可能被用於惡意 Tor 出口節點
'xbl.spamhaus.org',  // 用於過濾公開代理伺服器、垃圾郵件發信來源與惡意軟體發布來源
'proxy.bl.gweep.ca' // 用於過濾公開代理伺服器
); 