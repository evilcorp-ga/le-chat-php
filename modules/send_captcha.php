<?php
function send_captcha(){
	global $I, $db, $memcached;
	$difficulty=(int) get_setting('captcha');
	if($difficulty===0 || !extension_loaded('gd')){
		return;
	}
	$captchachars=get_setting('captchachars');
	$length=strlen($captchachars)-1;
	$code='';
	for($i=0;$i<5;++$i){
		$code.=$captchachars[mt_rand(0, $length)];
	}
	$randid=mt_rand();
	$time=time();
	if(MEMCACHED){
		$memcached->set(DBNAME . '-' . PREFIX . "captcha-$randid", $code, get_setting('captchatime'));
	}else{
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'captcha (id, time, code) VALUES (?, ?, ?);');
		$stmt->execute([$randid, $time, $code]);
	}
	echo "<tr id=\"captcha\"><td>$I[copy]<br>";
	if($difficulty===1){
		$im=imagecreatetruecolor(55, 24);
		$bg=imagecolorallocate($im, 0, 0, 0);
		$fg=imagecolorallocate($im, 255, 255, 255);
		imagefill($im, 0, 0, $bg);
		imagestring($im, 5, 5, 5, $code, $fg);
		echo '<img width="55" height="24" src="data:image/gif;base64,';
	}elseif($difficulty===2){
		$im=imagecreatetruecolor(55, 24);
		$bg=imagecolorallocate($im, 0, 0, 0);
		$fg=imagecolorallocate($im, 255, 255, 255);
		imagefill($im, 0, 0, $bg);
		imagestring($im, 5, 5, 5, $code, $fg);
		$line=imagecolorallocate($im, 255, 255, 255);
		for($i=0;$i<2;++$i){
			imageline($im, 0, mt_rand(0, 24), 55, mt_rand(0, 24), $line);
		}
		$dots=imagecolorallocate($im, 255, 255, 255);
		for($i=0;$i<100;++$i){
			imagesetpixel($im, mt_rand(0, 55), mt_rand(0, 24), $dots);
		}
		echo '<img width="55" height="24" src="data:image/gif;base64,';
	}else{
		$im=imagecreatetruecolor(150, 200);
		$bg=imagecolorallocate($im, 0, 0, 0);
		$fg=imagecolorallocate($im, 255, 255, 255);
		imagefill($im, 0, 0, $bg);
		$chars=[];
		for($i=0;$i<10;++$i){
			$found=false;
			while(!$found){
				$x=mt_rand(10, 140);
				$y=mt_rand(10, 180);
				$found=true;
				foreach($chars as $char){
					if($char['x']>=$x && ($char['x']-$x)<25){
						$found=false;
					}elseif($char['x']<$x && ($x-$char['x'])<25){
						$found=false;
					}
					if(!$found){
						if($char['y']>=$y && ($char['y']-$y)<25){
							break;
						}elseif($char['y']<$y && ($y-$char['y'])<25){
							break;
						}else{
							$found=true;
						}
					}
				}
			}
			$chars[]=['x', 'y'];
			$chars[$i]['x']=$x;
			$chars[$i]['y']=$y;
			if($i<5){
				imagechar($im, 5, $chars[$i]['x'], $chars[$i]['y'], $captchachars[mt_rand(0, $length)], $fg);
			}else{
				imagechar($im, 5, $chars[$i]['x'], $chars[$i]['y'], $code[$i-5], $fg);
			}
		}
		$follow=imagecolorallocate($im, 200, 0, 0);
		imagearc($im, $chars[5]['x']+4, $chars[5]['y']+8, 16, 16, 0, 360, $follow);
		for($i=5;$i<9;++$i){
			imageline($im, $chars[$i]['x']+4, $chars[$i]['y']+8, $chars[$i+1]['x']+4, $chars[$i+1]['y']+8, $follow);
		}
		$line=imagecolorallocate($im, 255, 255, 255);
		for($i=0;$i<5;++$i){
			imageline($im, 0, mt_rand(0, 200), 150, mt_rand(0, 200), $line);
		}
		$dots=imagecolorallocate($im, 255, 255, 255);
		for($i=0;$i<1000;++$i){
			imagesetpixel($im, mt_rand(0, 150), mt_rand(0, 200), $dots);
		}
		echo '<img width="150" height="200" src="data:image/gif;base64,';
	}
	ob_start();
	imagegif($im);
	imagedestroy($im);
	echo base64_encode(ob_get_clean()).'">';
	echo '</td><td>'.hidden('challenge', $randid).'<input type="text" name="captcha" size="15" autocomplete="off"></td></tr>';
}
