<?php /* Template_ 2.2.3 2016/03/03 07:42:07 C:\inetpub\web\5. Armand De\www\vhost.live_daemon\_template\layout\layout.html */?>
<?php $this->print_("header",$TPL_SCP,1);?>

	<script>
	window.onload = function()
	{
		begin_live_listener();
	}
	</script>
</head>

	<body>
		<div id="wrap">
			<div id="header">
<?php $this->print_("top",$TPL_SCP,1);?>

			</div>
			<div id="body">
				
				<div id="wrap_main">
					<div id="main">
<?php $this->print_("content",$TPL_SCP,1);?>

					</div>
	
				</div>

				<div id="leftMenu">
<?php $this->print_("left",$TPL_SCP,1);?>

				</div>
			</div>

			<div id="footer">
<?php $this->print_("footer",$TPL_SCP,1);?>

			</div>
		</div>
	</body>
</html>