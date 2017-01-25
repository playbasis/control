<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" ng-app>
<head>
    <link rel="icon" type='image/x-icon' href="<?php echo base_url();?>image/favicon.ico">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css'>

    <title><?php echo $title; ?></title>
    <base href="<?php echo base_url(); ?>" />
    <?php if (isset($description)) { ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php } ?>
    <?php if (isset($keywords)) { ?>
    <meta name="keywords" content="<?php echo $keywords; ?>" />
    <?php } ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/beforelogin/styles.css" />

    <script type="text/javascript">
        var imageUrlPath = "<?php echo S3_IMAGE ?>";
        var baseUrlPath = "<?php echo base_url();?><?php echo (index_page() == '')? '' : index_page()."/" ?>";
        var SiteId = "<?php echo $site_id;?>";
        var ClientId = "<?php echo $client_id;?>";
    </script>

</head>
<body id="pb-registration" >

<?php
    $this->load->view($main);
?>

<footer class="pbr-footer" role="contentinfo">
	<div class="row">

		<a href="http://www.playbasis.com/">
			<svg title="Playbasis" class="pbr-footer-logo">
				<use xlink:href="<?php echo base_url();?>image/beforelogin/logo.svg#logo"></use>
			</svg>
		</a>

		<div class="pbr-footer-copyright">
			<?php echo $this->lang->line('text_footer'); ?><br>
                                    If you have any questions or you need help, please send us an email to  <a href="mailto:support@playbasis.com">support@playbasis.com</a>
		</div>

	</div>
</footer><!-- /footer -->
<script>
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrf_token_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
</script>
<script src="javascript/beforelogin/script.min.js"></script>
<script type="text/javascript" src="javascript/beforelogin/jquery.jcryption.3.1.0.js"></script>
<script>
    $(document).ready(function() {
        $("#login_form").jCryption();
    });
</script>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-39586205-1', 'pbapp.net');
    ga('send', 'pageview');
</script>



	</body>
</html>