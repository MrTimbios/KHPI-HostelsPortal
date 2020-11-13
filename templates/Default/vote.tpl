<div class="vote_line">
	<div class="wrp">
		<h4 class="vote_line_title ultrabold">
			Опрос
			<span class="vote_icon">
				<i class="i1"></i><i class="i2"></i><i class="i3"></i><i class="i4"></i>
			</span>
		</h4>
		<p class="vtitle">{title}</p>
		<div class="vote_line_form">
			<div class="dropdown">
				<button data-toggle="dropdown" class="btn btn_white"><b class="ultrabold">Принять участие</b></button>
				<div class="dropdown-form">
					[votelist]
					<form method="post" name="vote">
					[/votelist]
						<div class="vote_list">
							{list}
						</div>
					[voteresult]
						<div class="vote_votes grey">Проголосовало: {votes}</div>
					[/voteresult]
					[votelist]
						<input type="hidden" name="vote_action" value="vote">
						<input type="hidden" name="vote_id" id="vote_id" value="{vote_id}">
						<button title="Голосовать" class="btn wide" type="submit" onclick="doVote('vote'); return false;" ><b class="ultrabold">Голосовать</b></button>
						<button title="Результаты" class="btn btn_border wide" type="button" onclick="doVote('results'); return false;" >
							<b class="ultrabold">Результаты</b>
						</button>
					</form>
					[/votelist]
				</div>
			</div>
			<a class="more_votes" href="#" onclick="ShowAllVotes(); return false;">Другие опросы...</a>
		</div>
	</div>
</div>