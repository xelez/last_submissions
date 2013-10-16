<?php
  /**
   * Last submitted solutions on Gate
   * Copyright (c) 2013 Alexander Ankudinov <xelez0@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  include '../globals.php';
  include '../inc/include.php';

  if (!user_access_root()) {
  	header('HTTP/1.0 403 Forbidden');
    die('You are not allowed to access this.');
  }
  
  /* where is tester located */
  $base_url = '/gate/tester/';
   
  setlocale(LC_ALL, "ru_RU.UTF-8");
  db_connect (config_get ('check-database'));

  /* Converting from Id's to readable names */
  $uid_cache = array();
  $pid_cache = array();
  $cid_cache = array();
  
  function get_username($uid) {
  	global $uid_cache;
  	if (!array_key_exists($uid, $uid_cache)) {
	  	$q = db_query("SELECT name FROM user WHERE `id`=".$uid);
	  	$row=db_row_array($q);
	  	$uid_cache[$uid] = $row['name'];
  	}
  	return $uid_cache[$uid];	 
  }

  function get_problem($id) {
  	global $pid_cache;
  	if (!array_key_exists($id, $pid_cache)) {
  		$q = db_query("SELECT name FROM tester_problems WHERE `id`=".$id);
  		$row=db_row_array($q);
  		$pid_cache[$id] = $row['name'];
  	}
  	return $pid_cache[$id];
  }
  
  function get_contest($id) {
  	global $cid_cache;
  	if (!array_key_exists($id, $cid_cache)) {
  		$q = db_query("SELECT name FROM tester_contests WHERE `id`=".$id);
  		$row=db_row_array($q);
  		$cid_cache[$id] = $row['name'];
  	}
  	return $cid_cache[$id];
  }
  
  /* Url stuff */
  function url($append) {
  	global $base_url;
  	return $base_url.$append;
  }
  
  function submission_url($id) {
  	return url('?page=solutions&action=view&id='.$id);
  }
  
  /* Formatting stuff */
  function format_link($url, $text) {
  	return '<a href="'.$url.'">'.$text.'</a>';
  }
  
  function format_submission($id) {
  	return format_link(submission_url($id), $id);
  }
  
  function format_status($row) {
  	if ($row['status'] == 0)
  		return "Тестируется...";
  	
  	if ($row['errors'] == 'OK')
  		$color = "DarkGreen";
  	else
  		$color = "DarkRed";

  	return "<b><font color=$color>".$row['points']."(".$row['errors'].")</font></b>";
  }
  
  /* Main stuff */
  
  function print_submission($row) {
  	print "<tr>";

  	print "<td>".format_submission($row['id'])."</td>";
  	print "<td>".strftime('%Y %B %e - %A - %T', $row['timestamp'])."</td>";
  	print "<td>".get_username($row['user_id'])."</td>";
  	print "<td>".get_contest($row['contest_id'])."</td>";
  	print "<td>".get_problem($row['problem_id'])."</td>";
  	print "<td>".format_status($row)."</td>";
  	 
  	print "</tr>";
  }
  
  function print_last_submissions() {
  	$q = db_query("SELECT id,timestamp,contest_id,problem_id,user_id,status,errors,points ".
  			"FROM tester_solutions ORDER BY timestamp desc LIMIT 0,30");
  	
  	while ($row = db_row_array($q)) {
  		print_submission($row);
  	}
  	
  }
  
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Last submissions to Gate</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
  </head>
  <body>
    <h1>Последние посылки</h1>
    <table class="table table-striped table-hover">
    	<?php
    		print_last_submissions();
    	?>
    </table>
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
