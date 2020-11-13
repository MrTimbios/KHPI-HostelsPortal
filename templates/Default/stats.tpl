<article class="block story shadow">
	<div class="wrp">
		<div class="head"><h1 class="title h2 ultrabold">Статистика сайта</h1></div>
		<div class="stats_head">
			<ul>
				<li class="stats_d"><b>За сутки</b> <span>{news_day} новостей и {comm_day} комментариев, зарегистрировано {user_day} пользователей</span></li>
				<li class="stats_w"><b>За неделю</b> <span>{news_week} новостей и {comm_week} комментариев, зарегистрировано {user_week} пользователей</span></li>
				<li class="stats_m"><b>За месяц</b> <span>{news_month} новостей и {comm_month} комментариев, зарегистрировано {user_month} пользователей</span></li>
			</ul>
		</div>
	</div>
</article>
<div class="block">
	<div class="wrp">
		<div class="statistics">
			<div class="stat_group">
				<h5>Новости</h5>
				<ul>
					<li>Общее кол-во новостей <b class="right">{news_num}</b></li>
					<li>Из них опубликовано <b class="right">{news_allow}</b></li>
					<li>Опубликовано на главной <b class="right">{news_main}</b></li>
					<li>Ожидает модерации <b class="right">{news_moder}</b></li>
				</ul>
			</div>
			<div class="stat_group">
				<h5>Пользователи</h5>
				<ul>
					<li>Общее кол-во пользователей <b class="right">{user_num}</b></li>
					<li>Из них забанено <b class="right">{user_banned}</b></li>
				</ul>
			</div>
			<div class="stat_group">
				<h5>Комментарии</h5>
				<ul>
					<li>Кол-во комментариев <b class="right">{comm_num}</b></li>
					<li><a href="/?do=lastcomments">Посмотреть последние</a></li>
				</ul>
			</div>
			<p class="grey">Общий размер базы данных: {datenbank}</p>
		</div>
	</div>
</div>
<div class="block block_table_top_users">
	<div class="wrp">
		<h4 class="block_title ultrabold">Лучшие пользователи</h4>
		<div class="table_top_users">
			<table class="userstop">{topusers}</table>
		</div>
	</div>
</div>