<?php
	function fuckOff() {
		echo '[' . date("Y-M-d H:i:s") . '] PHP Parse error:  syntax error, unexpected \';\' in /var/www/html/framework/router.php on line 327';
		die();
	}

	$clientIp = false;

	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	    $clientIp = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	    $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
	    $clientIp = $_SERVER['REMOTE_ADDR'];
	}

	if ($clientIp === false) {
		fuckOff();
	}

	function testClientIpByFile($filename, $clientip) {
		$clientip = trim($clientip);
		$f = fopen($filename, 'r');

		while (!(feof($f))) {
			$gosip = trim(fgets($f));

			if (strpos($gosip, '-') !== false) {
				$pairAddr = explode('-', $gosip);

				if (count($pairAddr) != 2) {
					continue;
				}

				$firstAddr = explode('.', $pairAddr[0]);
				$secondAddr = explode('.', $pairAddr[1]);

				if (count($firstAddr) != 4 || count($secondAddr) != 4) {
					continue;
				}

				for ($i = 0; $i < 4; ++$i) {
					$firstAddr[$i] = intval($firstAddr[$i]);
					$secondAddr[$i] = intval($secondAddr[$i]);
				}

				$a = $firstAddr;
				$b = $secondAddr;

				while (!($a[0] == $b[0] && $a[1] == $b[1] && $a[2] == $b[2] && $a[3] == $b[3])) {
					$currAddr = $a[0] . '.' . $a[1] . '.' . $a[2] . '.' . $a[3];

					if ($currAddr == $clientip) {
						fclose($f);
						return false;
					}

					++$a[3];

					if ($a[3] == 256) {
						$a[3] = 0;
						$a[2] += 1;
					}

					if ($a[2] == 256) {
						$a[2] = 0;
						$a[1] += 1;
					}

					if ($a[1] == 256) {
						$a[1] = 0;
						$a[0] += 1;
					}

					if ($a[0] == 256) {
						break;
					}
				}
			} elseif (strpos($gosip, '/') !== false) {
				list ($subnet, $bits) = explode('/', $gosip);
			    if ($bits === null) {
			        $bits = 32;
			    }
			    $ip = ip2long($clientip);
			    $subnet = ip2long($subnet);
			    $mask = -1 << (32 - $bits);
			    $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
			    
			    if (($ip & $mask) == $subnet) {
			    	fclose($f);
					return false;
			    }
			} else {
				if ($gosip == $clientip) {
					fclose($f);
					return false;
				}
			}
		}

		fclose($f);
		return true;
	}

	if (!testClientIpByFile('gosip.txt', $clientIp)) {
		fuckOff();
		die();
	}

	if (!testClientIpByFile('badip.txt', $clientIp)) {
		fuckOff();
		die();
	}

	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		$userAgent = $_SERVER['HTTP_USER_AGENT'];

		if (strpos($userAgent, 'Wget') !== false ||
			strpos($userAgent, 'Poop') !== false ||
			strpos($userAgent, 'SemrushBot') !== false ||
			strpos($userAgent, 'AhrefsBot') !== false ||
			strpos(strtolower($userAgent), 'wget') !== false ||
			strpos(strtolower($userAgent), 'poop') !== false ||
			strpos(strtolower($userAgent), 'semrushbot') !== false ||
			strpos(strtolower($userAgent), 'ahrefsbot') !== false)
		{
			fuckOff();
		}
	}

	function logIp($clientIp) {
		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'russiancoders') !== false) {
			return;
		}

		if (!isset($_SERVER['HTTP_REFERER']) || !strlen(trim($_SERVER['HTTP_REFERER']))) {
			return;
		}

		if (strpos($_SERVER['REQUEST_URI'], 'ping.php') !== false) {
			return;
		}

		if (strpos($_SERVER['HTTP_REFERER'], 'burger-imperia.com') !== false) {
			return;
		}

		if (strpos($_SERVER['HTTP_REFERER'], 'pizza-imperia.com') !== false) {
			return;
		}

		if (strpos($_SERVER['HTTP_REFERER'], 'pizza-tycoon.com') !== false) {
			return;
		}

		if (strpos($_SERVER['HTTP_REFERER'], 'losangelesvapeshop.website') !== false) {
			return;
		}

		$file = fopen("ip.log", "a");
		fwrite($file, 'REQUEST:  ' . $_SERVER['REQUEST_URI'] . "\r\n");
		fwrite($file, 'FROM:  ' . $_SERVER['HTTP_REFERER'] . "\r\n");
		fwrite($file, 'IP:   ' . $clientIp . "\r\n");
		fwrite($file, 'BROWSER:  ' . $_SERVER['HTTP_USER_AGENT'] . "\r\n");
		fwrite($file, "   \r\n");
		fclose($file);
	}

	logIp($clientIp);
