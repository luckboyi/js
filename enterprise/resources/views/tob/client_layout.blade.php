<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
  @yield("meta")
	<link rel="stylesheet" href="{{ static_res('/btobEdition/css/base.css') }}" />
	<link rel="stylesheet" href="{{ static_res('/btobEdition/css/btobEdition.css') }}" />
	<script src="{{ static_res('/common/js/jquery-1.12.3.min.js') }}"></script>
  <script src="{{ static_res('/btobEdition/js/tinyscrollbar.js') }}"></script>
  @yield("head")
</head>
<body>
   @yield('content')
   @yield("javascript")
</body>
</html>
