<?php

$RBLlist = array(6, 'tor.dan.me.uk', 'exitnodes.tor.dnsbl.sectoor.de', 'http.dnsbl.sorbs.net', 'socks.dnsbl.sorbs.net', 'misc.dnsbl.sorbs.net', 'zombie.dnsbl.sorbs.net' );

    function BanIPHostDNSBLCheck($IP, $HOST, &$baninfo){
	if(!BAN_CHECK) return false; // Disabled
	global $BANPATTERN, $RBLlist, $DNSBLWHlist;

	// IP/Hostname Check
	$HOST = strtolower($HOST);
	$checkTwice = ($IP != $HOST); // 是否需檢查第二次
	$IsBanned = false;
	foreach($BANPATTERN as $pattern){
		$slash = substr_count($pattern, '/');
		if($slash==2){ // RegExp
			$pattern .= 'i';
		}elseif($slash==1){ // CIDR Notation
			if(matchCIDR($IP, $pattern)){ $IsBanned = true; break; }
			continue;
		}elseif(strpos($pattern, '*')!==false || strpos($pattern, '?')!==false){ // Wildcard
			$pattern = '/^'.str_replace(array('.', '*', '?'), array('\.', '.*', '.?'), $pattern).'$/i';
		}else{ // Full-text
			if($IP==$pattern || ($checkTwice && $HOST==strtolower($pattern))){ $IsBanned = true; break; }
			continue;
		}
		if(preg_match($pattern, $HOST) || ($checkTwice && preg_match($pattern, $IP))){ $IsBanned = true; break; }
	}
	if($IsBanned){ $baninfo = _T('ip_banned'); return true; }

	// DNS-based Blackhole List(DNSBL) 黑名單
	if(!$RBLlist[0]) return false; // Skip check
	if(array_search($IP, $DNSBLWHlist)!==false) return false; // IP位置在白名單內
	$rev = implode('.', array_reverse(explode('.', $IP)));
	$lastPoint = count($RBLlist) - 1; if($RBLlist[0] < $lastPoint) $lastPoint = $RBLlist[0];
	$isListed = false;
	for($i = 1; $i <= $lastPoint; $i++){
		$query = $rev.'.'.$RBLlist[$i].'.'; // FQDN
		$result = gethostbyname($query);
		if($result && ($result != $query)){ $isListed = $RBLlist[$i]; break; }
	}
	if($isListed){ $baninfo = _T('ip_dnsbl_banned',$isListed); return true; }
	return false;
}