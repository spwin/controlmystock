<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
	<meta charset="utf-8"/>
	<title>Stock Control v1.0</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport"/>
	<meta content="" name="description"/>
	<meta content="" name="author"/>
	<link rel="stylesheet" href="{{ asset("assets/stylesheets/styles.css") }}" />
    <link rel="stylesheet" href="{{ asset("css/app.css") }}" />
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
    <script src="{{ asset("assets/scripts/frontend.js") }}" type="text/javascript"></script>
    <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
</head>
<body @yield('body-tag')>
	@yield('body')
    <script src="{{ asset("assets/scripts/footer.js") }}" type="text/javascript"></script>
    @stack('scripts')
</body>
</html>