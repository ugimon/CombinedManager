<?php /* Template_ 2.2.3 2016/03/07 10:27:14 C:\inetpub\combined_manager\vhost.manager\_template\layout\layout.html */?>
<?php $this->print_("header",$TPL_SCP,1);?>

	<script>
	window.onload = function()
	{
		refreshContent();
		beginTimer();
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