<article class="story shortstory lefticons shadow">
	<div class="wrp">
		<div class="head grid_3_4">
			<h2 class="title ultrabold"><a href="{full-link}">{title}</a></h2>
		</div>
		<!-- Информация о новости -->
		<div class="story_info grid_1_4">
			<div class="storyinfo_link collapsed" aria-expanded="false" data-target="#storyinfo_{news-id}" data-toggle="collapse">
				<i class="arrow"></i>
				<svg class="icon icon-meta_date"><use xlink:href="#icon-meta_date"></use></svg>
				<b>{date}</b>
			</div>
			<div id="storyinfo_{news-id}" class="storyinfo collapse">
				<div class="storyinfo_box">
					[rating][rating-type-1]<div class="rate_stars">{rating}</div>[/rating-type-1][/rating]
					<ul class="meta">
						<li class="meta_date">
							<svg class="icon icon-meta_date"><use xlink:href="#icon-meta_date"></use></svg><time class="date" datetime="{date=Y-m-d}">[day-news]<b>{date}</b>[/day-news]</time>
						</li>
						<li class="meta_cat grey">
							<svg class="icon icon-meta_cat"><use xlink:href="#icon-meta_cat"></use></svg>{link-category}
						</li>
						<li class="meta_user grey" title="Автор: {login}">
							<svg class="icon icon-meta_user"><use xlink:href="#icon-meta_user"></use></svg>{author}
						</li>
						<li class="meta_views grey" title="Просмотров: {views}">
							<svg class="icon icon-meta_views"><use xlink:href="#icon-meta_views"></use></svg>{views}
						</li>
						<li class="meta_coms grey" title="Комментариев: {comments-num}">
							<svg class="icon icon-meta_coms"><use xlink:href="#icon-meta_coms"></use></svg>[com-link]{comments-num}[/com-link]
						</li>
					</ul>
					[tags]
					<div class="story_tags">
						<svg class="icon icon-tags"><use xlink:href="#icon-tags"></use></svg>
						<div class="tag_list grey">{tags}</div>
					</div>
					[/tags]
				</div>
			</div>
		</div>
		<!-- / Информация о новости -->
		<div class="story_cont grid_3_4">
			<div class="story_left_icons">
				<div class="story_icons">
					[not-group=5]
					<div class="fav_btn">
						[add-favorites]<span title="Добавить в закладки"><svg class="icon icon-fav"><use xlink:href="#icon-fav"></use></svg></span>[/add-favorites]
						[del-favorites]<span title="Убрать из закладок"><svg class="icon icon-fav"><use xlink:href="#icon-fav"></use></svg></span>[/del-favorites]
					</div>
					<div class="edit_btn" title="Редактировать">
						[edit]<i title="Редактировать"></i>[/edit]
					</div>
					[/not-group]
					[rating]
					<div class="rate">
						[rating-type-2]
							<div class="rate_like" title="Мне нравится">
							[rating-plus]
								<span class="rate_like_icon"><svg class="icon icon-like"><use xlink:href="#icon-like"></use></svg></span>
								<span class="grey">{rating}</span>
							[/rating-plus]
							</div>
						[/rating-type-2]
						[rating-type-3]
							<div class="rate_like-dislike">
								<div class="rate_like-dislike_in">
									[rating-plus]<span class="plus_icon" title="Нравится"><span>+</span></span>[/rating-plus]
									[rating-minus]<span class="plus_icon minus" title="Не нравится"><span>-</span></span>[/rating-minus]
								</div>
								<span class="grey">{rating}</span>
							</div>
						[/rating-type-3]
						[rating-type-4]
							<div class="rate_like-dislike">
								<span class="grey"><span class="ratingtypeplusminus ignore-select ratingplus">{likes}</span></span>
								<div class="rate_like-dislike_in">
									[rating-plus]<span class="plus_icon" title="Нравится"><span>+</span></span>[/rating-plus]
									[rating-minus]<span class="plus_icon minus" title="Не нравится"><span>-</span></span>[/rating-minus]
								</div>
								<span class="grey"><span class="ratingtypeplusminus ratingminus ignore-select">{dislikes}</span></span>
							</div>
						[/rating-type-4]
					</div>
					[/rating]
				</div>
			</div>
			<div class="text">
				{short-story}
				[edit-date]<p class="editdate grey">Новость отредактировал: <b>{editor}</b> - {edit-date}<br>
				[edit-reason]Причина: {edit-reason}[/edit-reason]</p>[/edit-date]
				<div class="more"><a href="{full-link}" title="Читать подробнее: {title}" class="btn"><b class="ultrabold">Подробнее</b></a></div>
			</div>
		</div>
	</div>
	[fixed]<span class="fixed_label" title="Это важно!">Это важно!</span>[/fixed]
</article>