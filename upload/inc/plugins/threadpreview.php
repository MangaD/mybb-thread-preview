<?php
if(!defined("IN_MYBB"))
{
	die("Direct access not allowed.");
}

$plugins->add_hook("forumdisplay_thread", "threadpreview_preview");

function threadpreview_info()
{
	return array(
		"name"			=> "Thread Preview",
		"description"	=> "Shows a part of the first post of a thread on forumdisplay when hovered over.",
		"website"		=> "https://github.com/MangaD/mybb-thread-preview",
		"author"		=> "MangaD",
		"authorsite"	=> "https://github.com/MangaD/mybb-thread-preview",
		"version"		=> "1.2",
		"guid" 			=> "",
		"compatibility" => "18*"
	);
}

function threadpreview_install()
{
	global $db;

	$result = $db->simple_select('settinggroups', 'MAX(disporder) AS max_disporder');
	$max_disporder = $db->fetch_field($result, 'max_disporder');
	$disporder = 1;

	//create settings
	$settings_group = array(
		'gid'			=> 'NULL',
		'name'			=> 'threadpreview',
		'title'			=> 'Thread Preview',
		'description'	=> "Here you can configure the Thread Preview plugin.",
		'disporder'		=> $max_disporder + 1,
		'isdefault'		=> '0'
	);
	$db->insert_query('settinggroups', $settings_group);
	$gid = (int) $db->insert_id();
	
	$setting = array(
		'sid'			=> 'NULL',
		'name'			=> 'threadpreview_maxlength',
		'title'			=> "Maximum characters",
		'description'	=> "How many characters should be displayed in the thread preview?",
		'optionscode'	=> 'numeric',
		'value'			=> '200',
		'disporder'		=> $disporder++,
		'gid'			=> $gid
	);
	$db->insert_query('settings', $setting);
	
	rebuild_settings();
}

function threadpreview_is_installed()
{
	global $mybb;

	// Are the settings present?
	return (isset($mybb->settings['threadpreview_maxlength']));
}

function threadpreview_uninstall()
{
	global $db;

	// Remove settings
	$result = $db->simple_select('settinggroups', 'gid', "name = 'threadpreview'");
	$gid = (int) $db->fetch_field($result, "gid");
	
	if ($gid > 0)
	{
		$db->delete_query('settings', "gid = '{$gid}'");
	}
	$db->delete_query('settinggroups', "gid = '{$gid}'");

	rebuild_settings();
}

function threadpreview_activate()
{
}

function threadpreview_deactivate()
{
}

function threadpreview_preview()
{
	global $mybb, $db, $tids, $threadcache, $thread, $firstpostcache;
	if(!$firstpostcache)
	{
		$query = $db->query("SELECT t.tid, t.firstpost, p.message
			FROM " . TABLE_PREFIX . "threads t
			LEFT JOIN " . TABLE_PREFIX . "posts p
			ON(t.firstpost=p.pid)
			WHERE t.tid IN(" . $tids . ")");
		while($data = $db->fetch_array($query))
		{
			// Change the third parameter of the substr function to however many characters you wish the preview to have.
			$firstpostcache[$data['tid']] = substr($data['message'], 0, (int)$mybb->settings['threadpreview_maxlength']);
			$threadcache[$data['tid']]['preview'] = substr($data['message'], 0 , (int)$mybb->settings['threadpreview_maxlength']);
		}
	}

	// MangaD - parse mycode, smilies...
	global $parser;
	if(!$parser)
	{
		require_once MYBB_ROOT."inc/class_parser.php";
		$parser = new postParser;
	}

	$parser_options = array(
		"allow_html" => (int)$mybb->settings['pmsallowhtml'],
		"allow_mycode" => (int)$mybb->settings['pmsallowmycode'],
		"allow_smilies" => (int)$mybb->settings['pmsallowsmilies'],
		"allow_imgcode" => (int)$mybb->settings['pmsallowimgcode'],
		"allow_videocode" => (int)$mybb->settings['pmsallowvideocode'],
		"nofollow_on" => 1,
		"filter_badwords" => 1
	);

	$thread['preview'] = $parser->parse_message($firstpostcache[$thread['tid']], $parser_options);
}
?>