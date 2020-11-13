[searchposts]
	[fullresult]
		{include file="shortstory.tpl"}
	[/fullresult]
	[shortresult]
	<div class="search_head_line block story lefticons shadow">
		<div class="wrp">
			[not-group=5]
			<div class="story_left_icons">
				<div class="story_icons">
					<div class="edit_btn" title="Редактировать">
						[edit]<i title="Редактировать"></i>[/edit]
					</div>
				</div>
			</div>
			[/not-group]
			<div class="head"><h5 class="title ultrabold">[full-link]{title}[/full-link]</h5></div>
			<div class="text">
				{short-story limit="120"}...
			</div>
		</div>
	</div>
	[/shortresult]
[/searchposts]
[searchcomments]
	[fullresult]
	<div class="searchcoms block shadow">
		<div class="wrp">
			{include file="comments.tpl"}
		</div>
	</div>
	[/fullresult]
	[shortresult]
	<div class="search_head_line block story shadow">
		<div class="wrp">
			<div class="head"><h5 class="title ultrabold">{news_title}</h5></div>
			<div class="text">
				{comment limit="200"}
			</div>
		</div>
	</div>
	[/shortresult]
[/searchcomments]