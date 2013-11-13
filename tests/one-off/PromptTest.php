<?php 

require dirname(dirname(dirname(__FILE__))) . '/bootstrap.php';

$m = new MessageLog();
if($m->prompt('Do this?')) {
	echo 'done';
} else {
	echo 'skip';
}
