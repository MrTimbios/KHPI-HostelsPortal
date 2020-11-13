<div id="tools">
	<div class="tools">
		<div class="wrp">
			[available=showfull]
				<div id="breadcrumbs">
					<svg class="icon icon-sort"><use xlink:href="#icon-sort"></use></svg>
					{speedbar}
				</div>
			[/available]
			[not-available=showfull]
			<div class="grid_3_4">
				[sort]
					<div id="sort">
						<svg class="icon icon-sort"><use xlink:href="#icon-sort"></use></svg>
						<b class="sort_label grey">Сортировать по</b>
						{sort}
					</div>
				[/sort]
				[available=lastcomments|addnews|search|pm|feedback|lostpassword|static|register|userinfo|stats]
				<div id="breadcrumbs">
					<svg class="icon icon-sort"><use xlink:href="#icon-sort"></use></svg>
					{speedbar}
				</div>
				[/available]
			</div>
			<div class="grid_1_4 grid_last">
				<a class="tags_btn grey collapsed" aria-expanded="false" href="#toptags" data-toggle="collapse">
					<svg class="icon icon-tags"><use xlink:href="#icon-tags"></use></svg>Популярные теги<svg class="icon icon-arrow_down"><use xlink:href="#icon-arrow_down"></use></svg>
				</a>
			</div>
			[/not-available]
		</div>
	</div>
	[not-available=showfull]
	<!-- Популярные теги -->
	<div id="toptags" class="collapse">
		<div class="wrp">
			<div class="tag_list">
				{tags}
			</div>
		</div>
	</div>
	<!-- / Популярные теги -->
	[/not-available]
</div>