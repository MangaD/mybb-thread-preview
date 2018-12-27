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
		"version"		=> "1.0",
		"guid" 			=> "",
		"compatibility" => "18*"
	);
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
			$firstpostcache[$data['tid']] = substr($data['message'], 0, 200);
			$threadcache[$data['tid']]['preview'] = substr($data['message'], 0 , 200);
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
		"allow_html" => (int)$mybb->settings[$prefix.'profile_box_post_allowhtml'],
		"allow_mycode" => (int)$mybb->settings[$prefix.'profile_box_post_allowmycode'],
		"allow_smilies" => (int)$mybb->settings[$prefix.'profile_box_post_allowsmilies'],
		"allow_imgcode" => (int)$mybb->settings[$prefix.'profile_box_post_allowimgcode'],
		"allow_videocode" => (int)$mybb->settings[$prefix.'profile_box_post_allowvideocode'],
		"nofollow_on" => 1,
		"filter_badwords" => 1
	);

	$thread['preview'] = $parser->parse_message($firstpostcache[$thread['tid']], $parser_options);
}
?>