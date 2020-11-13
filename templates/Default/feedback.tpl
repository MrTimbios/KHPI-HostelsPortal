<div class="block story shadow">
	<div class="wrp clrfix">
		<div class="head">
			<h1 class="title h2 ultrabold">Обратная связь</h1>
		</div>
		<div class="feedback clrfix">
			<div class="grid_1_4 right grid_last">
				<b>Адрес</b><br>
				660093 м. Харків, вул. Пушкінська 79/1<br>
				<br>
				<b>Телефоны</b><br>
				+38 (095) <b>228-14-88</b><br>
				<br>
				<b>Часы работы</b><br>
				ПН-ПТ: 10 до 18<br>
				СБ-ВС: Выходной</b>
			</div>
			<div class="grid_3_4">
				<ul class="ui-form">
					[not-logged]
					<li class="form-group combo">
						<div class="combo_field"><input placeholder="Ваше имя" type="text" maxlength="35" name="name" id="name" class="wide" required></div>
						<div class="combo_field"><input placeholder="Ваш E-mail" type="email" maxlength="35" name="email" id="email" class="wide" required></div>
					</li>
					[/not-logged]
					<li class="form-group">
						<label>Получатель</label>
						{recipient}
					</li>
					<li class="form-group">
						<input placeholder="Тема сообщения" type="text" maxlength="45" name="subject" id="subject" class="wide" required>
					</li>
					<li class="form-group">
						<textarea placeholder="Сообщение" name="message" id="message" rows="8"[not-logged] style="height: 140px;"[/not-logged] class="wide" required></textarea>
					</li>
					[attachments]
						<li class="form-group">
							<label for="question_answer">Прикрепить файлы:</label>
							<input name="attachments[]" type="file" multiple>
						</li>
					[/attachments]
					[recaptcha]
					<li class="form-group">{recaptcha}</li>
					[/recaptcha]
					[question]
					<li class="form-group">
						<label for="question_answer">Вопрос: {question}</label>
						<input placeholder="Ответ" type="text" name="question_answer" id="question_answer" class="wide" required>
					</li>
					[/question]
				</ul>
				<div class="form_submit">
					[sec_code]
						<div class="c-capcha">
							{code}
							<input placeholder="Повторите код" title="Введите код указанный на картинке" type="text" name="sec_code" id="sec_code" required>
						</div>
					[/sec_code]
					<button class="btn" type="submit" name="send_btn"><b class="ultrabold">Отправить</b></button>
				</div>
			</div>
		</div>
	</div>
</div>
{include file="modules/map.tpl"}