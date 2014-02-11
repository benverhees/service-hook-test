<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

$git = 'git';

if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
    $git = escapeshellarg('C:\Program Files (x86)\Git\bin\git');
}

$result = array();

if(isset($_POST["payload"])){
	$payload = json_decode($_POST["payload"]);
	$ref = isset($payload->ref) ? $payload->ref : "";
	$result['ref'] = $ref;
	$base_ref = isset($payload->base_ref) ? $payload->base_ref : "";
	$result['base_ref'] = $base_ref;
	$head_commit_id = (isset($payload->head_commit) && isset($payload->head_commit->id)) ? $payload->head_commit->id : "";
	$result['head_commit_id'] = $head_commit_id;

	if(!empty($ref)) {
		$result['fetch'] = shell_exec($git . ' fetch 2>&1');
		$result['show_ref'] = shell_exec(sprintf($git . ' show-ref --tags -s %s 2>&1', $ref));
		
		if($head_commit_id == $result['show_ref']) {
			$result['checkout'] = shell_exec(sprintf($git . ' checkout %s 2>&1', $ref));
		}
	}
}

header('Content-Type: application/json');
echo json_encode($result);
?>