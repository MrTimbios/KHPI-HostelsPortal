<!DOCTYPE html>
<html lang="ru">
	<head>
		{headers}
		<meta name="HandheldFriendly" content="true">
		<meta name="format-detection" content="telephone=no">
		<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="default">
		<link rel="shortcut icon" href="{THEME}/images/favicon/favicon.ico">
		<link rel="apple-touch-icon" href="{THEME}/images/touch-icon-iphone.png">
		<link rel="apple-touch-icon" sizes="76x76" href="{THEME}/images/touch-icon-ipad.png">
		<link rel="apple-touch-icon" sizes="120x120" href="{THEME}/images/touch-icon-iphone-retina.png">
		<link rel="apple-touch-icon" sizes="152x152" href="{THEME}/images/touch-icon-ipad-retina.png">
		<link href="{THEME}/css/engine.css" type="text/css" rel="stylesheet">
		<link href="{THEME}/css/styles.css" type="text/css" rel="stylesheet">
		<link href="{THEME}/css/icomoon.css" type="text/css" rel="stylesheet">
		<link href="{THEME}/css/owl.carousel.css" type="text/css" rel="stylesheet">
		<link href="{THEME}/css/owl.theme.default.css" type="text/css" rel="stylesheet">
		<!-- <link href="{THEME}/css/main.css" type="text/css" rel="stylesheet"> -->
	</head>
	<body>
		<div class="page">
			<header class="header">
				<div class="container">
					{include file="modules/logotype.tpl"}
					{include file="modules/menu.tpl"}
				</div>
			</header>
			[available=main]
			<!-- Блок "О нас" -->
			{include file="modules/hero.tpl"}
			<!-- Блок "Про гуртожитки" -->
			{include file="modules/about.tpl"}
			<!-- Блок с каруселью общежитий -->
			{include file="modules/carousel.tpl"}
			<!-- Блок рекламы поселения -->
			{include file="modules/advert.tpl"}
			[/available]
			<!-- / Шапка -->
			<!-- Сортировка, Теги, Хлебные крошки -->
			[not-available=main|static]{include file="modules/tools.tpl"}[/not-available]
			[available=static]{include file="modules/hostels__list.tpl"}[/available]
			<!-- / Сортировка, Теги, Хлебные крошки -->
			<!-- Контент -->
			<div id="content">
				{info}
				[available=lastcomments]
				<div class="block story lastcomments">
					<div class="wrp">
						<div class="head">
							<h1 class="title h2 ultrabold">Последние комментарии</h1>
						</div>
						[/available]
						[available=cat|favorites|newposts|lastnews|main]<section class="story_list">[/available]
							{content}
						[available=cat|favorites|newposts|lastnews|main]</section>[/available]
					[available=lastcomments]</div></div>[/available]
				</div>
				<!-- / Контент -->
				[available=main]
				<!-- Популярные новости -->
				<div class="block col_news">
					<div class="wrp">
						<div class="block_title"><h4 class="ultrabold">Популярные новости</h4></div>
						<div class="grid_list">
							{custom template="story_top" limit="4" days="80" order="reads" cache="yes"}
						</div>
					</div>
				</div>
				<!-- / Популярные новости -->
				[/available]
				[not-available=showfull|main|static]
				{vote}
				[/not-available]
				<!-- Нижняя часть шаблона -->
				<footer id="footer">
					<div class="wrp">
						<!-- {include file="modules/footmenu.tpl"} -->
						{include file="modules/copyright.tpl"}
					</div>
				</footer>
				<!-- / Нижняя часть шаблона -->
			</div>
			{AJAX}
			<script src="{THEME}/js/lib.js"></script>
			<script src="{THEME}/js/svgxuse.min.js"></script>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
			<script src="https://kit.fontawesome.com/21a9c655a1.js" crossorigin="anonymous"></script>
			<script src="{THEME}/js/owl.carousel.min.js"></script>
			<script src="{THEME}/js/jquery.mousewheel.min.js"></script>
			<script src="{THEME}/js/main.js"></script>
			<script>
				jQuery(function($){
					$.get("{THEME}/images/sprite.svg", function(data) {
					var div = document.createElement("div");
					div.innerHTML = new XMLSerializer().serializeToString(data.documentElement);
					document.body.insertBefore(div, document.body.childNodes[0]);
					});
				});
			</script>
			<!-- Закрой DevTools, тебе оно не надо -->
			<!-- Hа копирайты внимание не обращай, это самопис, базарю -->
		</body>
	</html>