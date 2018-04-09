<script type="text/javascript">
	(function ($) {
        $(document).ready(function () {
            $('.nsl-container')
                .addClass('nsl-container-login-layout-below')
                .appendTo('#loginform,#registerform,#front-login-form,#setupform')
                .css('display', 'block');
        });
    }(jQuery));
</script>
<style type="text/css">
    .nsl-container {
        display: none;
    }

    .nsl-container-login-layout-below {
        clear: both;
        padding: 20px 0 0;
    }

    .login form {
        padding-bottom: 20px;
    }
</style>
<noscript>
    <style>
        .nsl-container {
            display: block;
        }
    </style>
</noscript>