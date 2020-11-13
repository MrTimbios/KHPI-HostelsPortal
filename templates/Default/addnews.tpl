<article class="block story">
	<div class="wrp">
		<div class="head">
			<h1 class="title h2 ultrabold">Добавить новость</h1>
		</div>
		<div class="addnews clrfix">
		<div class="grid_1_4 right grid_last">
			<h5 class="ultrabold">Справка</h5>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce semper velit quam, eget iaculis mauris auctor mollis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse lectus ex, sodales id auctor nec, imperdiet consectetur metus. Donec interdum mauris ut lacus blandit consectetur. Etiam tempus, purus sed <b>bibendum pellentesque</b>, libero nulla posuere felis, sed pellentesque nulla odio vel ligula. Aliquam lorem purus, ornare ut iaculis eget, semper quis sapien. Phasellus faucibus molestie urna id pharetra.</p>
			<p>In sit amet libero sem. Maecenas eget consequat enim. Vestibulum at varius felis. Nullam facilisis interdum ipsum, sit amet mattis dolor vestibulum ac. Nulla a <a href="#">sapien at diam</a> sodales fringilla. Cras sagittis vel nunc vitae dictum. Integer a odio eget est rutrum pulvinar. Aenean consequat facilisis nibh non feugiat. Suspendisse potenti. </p>
		</div>
		<div class="grid_3_4">
			<ul class="ui-form">
				<li class="form-group imp">
					<label for="title">Заголовок</label>
					<input type="text" name="title" id="title" value="{title}" class="wide" required>
				</li>
				[urltag]
				<li class="form-group imp">
					<label for="alt_name">URL новости</label>
					<input type="text" name="alt_name" id="alt_name" value="{alt-name}" class="wide">
				</li>
				[/urltag]
				<li class="form-group imp">
					<label for="category">Категория</label>
					{category}
				</li>
				<li class="form-group">
					<a class="btn btn_border" href="#" onclick="$('.addvote').toggle();return false;">Добавить опрос</a>
				</li>
				<li class="form-group addvote" style="display:none;" >
					<label for="vote_title">Заголовок опроса</label>
					<input type="text" name="vote_title" id="vote_title" value="{votetitle}" maxlength="150" class="wide">
				</li>
				<li class="form-group addvote" style="display:none;" >
					<label for="frage">Вопрос</label>
					<input type="text" name="frage" id="frage" value="{frage}" maxlength="150" class="wide">
				</li>
				<li class="form-group addvote" style="display:none;" >
					<label for="vote_body">Варианты ответов</label>
					<p class="grey">Каждая новая строка является новым вариантом ответа</p>
					<textarea name="vote_body" id="vote_body" rows="10" class="wide" >{votebody}</textarea>
					<div class="checkbox">
						<input type="checkbox" name="allow_m_vote" id="allow_m_vote" value="1" {allowmvote}>
						<label for="allow_m_vote">Разрешить выбор нескольких вариантов</label>
					</div>
				</li>
				<li class="form-group imp">
					<label for="short_story">Краткое описание</label>
					[not-wysywyg]
					<div class="bb-editor">
						{bbcode}
						<textarea name="short_story" id="short_story" onfocus="setFieldName(this.name)" rows="8" class="wide" required>{short-story}</textarea>
					</div>
					[/not-wysywyg]
					{shortarea}
				</li>
				<li class="form-group">
					<label for="full_story">Полное описание</label>
					[not-wysywyg]
					<div class="bb-editor">
						{bbcode}
						<textarea name="full_story" id="full_story" onfocus="setFieldName(this.name)" rows="18" class="wide" >{full-story}</textarea>
					</div>
					[/not-wysywyg]
					{fullarea}
				</li>
				<li class="form-group">
					<table class="xfields">
						{xfields}
					</table>
				</li>
				<li class="form-group">
					<label for="alt_name">Ключевые слова</label>
					<input placeholder="Вводите через запятую" type="text" name="tags" id="tags" value="{tags}" maxlength="150" autocomplete="off" class="wide">
				</li>
				<li class="form-group">
					<div class="admin_checkboxs">{admintag}</div>
				</li>
			[recaptcha]
				<li class="form-group">{recaptcha}</li>
			[/recaptcha]
			[question]
				<li class="form-group">
					<label for="question_answer">{question}</label>
					<input placeholder="Введите ответ" type="text" name="question_answer" id="question_answer" class="wide" required>
				</li>
			[/question]
			</ul>
			<p style="margin: 20px 0 0 0;" class="grey"><span style="color: #e85319">*</span> — поля отмеченные звездочкой обязательны для заполнения.</p>
			<div class="form_submit">
				[sec_code]
					<div class="c-capcha">
						{sec_code}
						<input placeholder="Повторите код" title="Введите код указанный на картинке" type="text" name="sec_code" id="sec_code" required>
					</div>
				[/sec_code]
				<button class="btn" type="submit" name="add"><b class="ultrabold">Отправить</b></button>
				<button id="add_news_preview" class="btn btn_border" onclick="preview()" type="submit" name="nview"><b class="ultrabold">Предпросмотр</b></button>
			</div>
		</div>
	</div>
</article>