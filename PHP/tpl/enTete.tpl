<!DOCTYPE html>
<html lang="fr">
<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	
	<title>{{titre}}</title>

	<!-- CSS utilisé à travers le site -->
	<link rel="stylesheet" href="styles/site.css">

	<!-- CSS utilisé pour la page -->
{% for style in css %}
	<link rel="stylesheet" href="styles/{{style}}.css">
{% endfor %}

</head>
<body>