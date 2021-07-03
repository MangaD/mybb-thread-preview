# [MyBB Plugin] Thread Preview

This plugin is an improvement of the original plugin by Mark Janssen (dragonexpert) that can be found [here](https://mods.mybb.com/view/thread-preview). It displays a preview box of the thread content when hovering over the subject in the forum display.

## Installation:

1. Upload `upload/inc/plugins/threadpreview.php` to `inc/plugins` folder.

2. Go to `forumdisplay` template. After `{$footer}` paste the following code:
```js
<!-- Thread Preview - Start -->
<script>
	function make_visible(tid)
	{
		document.getElementById("preview" + tid).style.display="block";
	}
	function make_hidden(tid)
	{
		document.getElementById("preview" + tid).style.display="none";
	}
</script>
<!-- Thread Preview - End -->
```

3. Go to `forumdisplay_thread` template.  Locate the line that has the thread subject.  Add the following to the element (`span` or `a` as you wish):
```html
onmouseover="make_visible({$thread['tid']})" onmouseout="make_hidden({$thread['tid']})"
```

You may add an html comment for documentation if you like.

4. In that same template look for:

```html
<div class="author smalltext">{$thread['profilelink']}</div>
```

Right after that add this:
```html
<!-- Thread Preview - Start -->
<div style="display:none"  id="preview{$thread['tid']}" class="thread_preview">
    {$thread['preview']}
</div>
<!-- Thread Preview - End -->
```

5. Add following CSS (change colours and other things to your liking):

```css
/* Thread preview - START */
.thread_preview {
	position: absolute;
	margin-top: 10px;
	border: 1px solid #ccc;
	background: #fff;
	max-width: 300px;
	z-index:2;
	word-wrap: break-word;
	padding: 5px;
	text-align: justify;
}
.thread_preview::after, .thread_preview::before {
	bottom: 100%;
	left: 20%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
}
.thread_preview::before {
	border-bottom-color: #ccc;
	border-width: 11px;
	margin-left: -11px;
}
/* Thread preview - END */
```

6. Activate the plugin

## Customization

You can change how many characters to display in the preview at the ACP settings.

## Todo

- ignore certain types of mycode (select types in ACP options)

