<script src="http://widgets.twimg.com/j/2/widget.js"></script>
	<style type="text/css" media="screen">
		.twtr-hd {padding:0}
		#twtr-widget-1 div.twtr-doc {background:#fff !important}
		.twtr-timeline, .twtr-doc {border-radius:0}
		.twtr-widget {background: #fff; margin-bottom: 30px; padding: 10px 15px 0}
		.twtr-ft img {display:none}
		.twtr-ft span {float:none}
		.twtr-ft .twtr-join-conv {display:inline; color:#1985b5 !important; font-size: 11px;}
	</style>
	<script>
		new TWTR.Widget({
		version: 2,
		type: 'search',
		search: 'rosyama',
		interval: 6000,
		title: '',
		subject: '',
		width: 185,
		height: 300,
		theme: {
			shell: {
				background: '#ececec',
				color: '#ffffff'
			},
			tweets: {
				background: '#ffffff',
				color: '#444444',
				links: '#1985b5'
			}
		},
		features: {
			scrollbar: false,
			loop: true,
			live: true,
			hashtags: true,
			timestamp: true,
			avatars: true,
			toptweets: true,
			behavior: 'default'
		}
		}).render().start();
	</script>
	<div class="like">
		<!-- Facebook like -->
		<div id="fb_like">
			<iframe src="http://www.facebook.com/plugins/like.php?href=http://<?php echo $_SERVER['SERVER_NAME'] ?>/&amp;layout=button_count&amp;show_faces=false&amp;width=180&amp;action=recommend&amp;font&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:180px; height:21px;" allowTransparency="true"></iframe>
		</div>
		<!-- Vkontakte like -->
		<div id="vk_like"></div>
		<script type="text/javascript">VK.Widgets.Like("vk_like", {type: "button", verb: 1});</script>
	</div>
