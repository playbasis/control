<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <!-- TemplateBeginEditable name="doctitle" -->
    <title>ศLuckVer</title>
    <!-- TemplateEndEditable -->
    <!-- TemplateBeginEditable name="head" -->
    <!-- TemplateEndEditable -->
</head>

<body class="login">

<!-- Container -->
<div id="container">


    <!-- Header -->
    <div id="header">
        <div id="branding">

            <h1 id="site-name">Django administration</h1>

        </div>


    </div>
    <!-- END Header -->


    <!-- Content -->
    <div id="content" class="colM">


        <div id="content-main">
            <?php
            $attributes = array('action' => '/admin/', 'id' => 'login-form', 'method' => 'post');
            echo form_open('', $attributes);?>
                <div style="display:none"><input type="hidden" name="csrfmiddlewaretoken" value="CgWwBnA2R9sGx84JTuLt6BilLwCmVdaW"></div>
                <div class="form-row">

                    <label for="id_username" class="required">Username:</label> <input id="id_username" type="text" name="username" maxlength="30">
                </div>
                <div class="form-row">

                    <label for="id_password" class="required">Password:</label> <input type="password" name="password" id="id_password">
                    <input type="hidden" name="this_is_the_login_form" value="1">
                    <input type="hidden" name="next" value="/admin/">
                </div>


                <div class="submit-row">
                    <label>&nbsp;</label><input type="submit" value="Log in">
                </div>
            <?php echo form_close();?>

            <script type="text/javascript">
                document.getElementById('id_username').focus()
            </script>
        </div>


        <br class="clear">
    </div>
    <!-- END Content -->

    <div id="footer"></div>
</div>
<!-- END Container -->


</body>
</html>
