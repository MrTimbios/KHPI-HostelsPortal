<div class="block story">
	<div class="wrp">
		<div class="grid_3_4 none">
			<div class="head">
				<h1 class="title h2 ultrabold">
					[registration]Регистрация[/registration]
					[validation]Продолжение регистрации[/validation]
				</h1>
			</div>
			<div class="text regtext">
			[registration]
				Регистрация на нашем сайте позволит Вам быть его полноценным участником.
				Вы сможете добавлять новости на сайт, оставлять свои комментарии, просматривать скрытый текст и многое другое.<br>
				<br>В случае возникновения проблем с регистрацией, обратитесь к <a href="/index.php?do=feedback">администратору</a> сайта.
			[/registration]
			[validation]
				Ваш аккаунт был зарегистрирован на нашем сайте,
				однако информация о Вас является неполной, поэтому ОБЯЗАТЕЛЬНО заполните дополнительные поля в Вашем профиле.<br>
			[/validation]
			</div>
			<ul class="ui-form">
				[registration]
					<li class="form-group imp">
						<label for="name">Логин</label>
						<div class="login_check">
							<input type="text" name="name" id="name" class="wide" required>
							<button class="btn" title="Проверить" onclick="CheckLogin(); return false;">Проверить</button>
						</div>
						<div id="result-registration"></div>
					</li>
					<li class="form-group imp">
						<label for="password1">Пароль</label>
						<input type="password" name="password1" id="password1" class="wide" required>
					</li>
					<li class="form-group imp">
						<label for="password2">Повторите пароль</label>
						<input type="password" name="password2" id="password2" class="wide" required>
					</li>
					<li class="form-group imp">
						<label for="email">E-mail</label>
						<input type="email" name="email" id="email" class="wide" required>
					</li>
				[question]
					<li class="form-group">
						<label for="question_answer">{question}</label>
						<input placeholder="Введите ответ" type="text" name="question_answer" id="question_answer" class="wide" required>
					</li>
				[/question]
				[recaptcha]
					<li>{recaptcha}</li>
				[/recaptcha]
				[/registration]
				[validation]
					<li class="form-group">
						<label for="fullname">Ваше имя</label>
						<input type="text" id="fullname" name="fullname" class="wide">
					</li>
					<li class="form-group">
						<label for="land">Место жительства</label>
						<input type="text" id="land" name="land" class="wide">
					</li>
					<li class="form-group">
						<label for="image">О себе</label>
						<textarea id="info" name="info" rows="5" class="wide"></textarea>
					</li>
					<li class="form-group">
						<label for="image">Аватар</label>
						<input type="file" id="image" name="image" class="wide">
					</li>
					<li class="form-group">
						<table class="xfields">
							{xfields}
						</table>
					</li>
				[/validation]
			</ul>
			<div class="form_submit">
				[registration]
				[sec_code]
					<div class="c-capcha">
						{reg_code}
						<input placeholder="Повторите код" title="Введите код указанный на картинке" type="text" name="sec_code" id="sec_code" required>
					</div>
				[/sec_code]
				[/registration]
				<button class="btn" name="submit" type="submit"><b class="ultrabold">Зарегистрироваться</b></button>
			</div>
		</div>
	</div>
</div>