<?
$this->pageTitle=Yii::app()->name . ' - Пожертвования';
$this->layout='//layouts/header_default_without_add';
?>
<div class="lCol">
<?php $this->widget('application.widgets.news.newsWidget'); ?>
<?php $this->widget('application.widgets.social.socialWidget'); ?>
</div>

<div class="rCol">
<p><b>Деньги в поддержку РосЯмы можно перечислять сюда:</b></p>

<p><b>Яндекс.Деньги</b> <a href="http://yaudit.org/yaudit/41001550415485">кошелёк № 41001550415485</a></p>
<!-- Яндекс-форма по перечислению денег -->
<iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/donate.xml?uid=41001550415485&amp;default-sum=300&amp;targets=%d0%bd%d0%b0+%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%83+%d0%bf%d1%80%d0%be%d0%b5%d0%ba%d1%82%d0%b0&amp;project-name=%d0%a0%d0%be%d1%81%d0%af%d0%bc%d0%b0&amp;project-site=http%3a%2f%2frosyama.ru&amp;button-text=02&amp;hint=" width="450" height="106"></iframe>
<!-- Кнопка Я-Аудита -->
<p><button onclick="window.open('http://yaudit.org/41001550415485','newwindow','toolbar=0')">Посмотреть историю пожертвований</button></p>

<p><b>PayPal</b> fezeev@gmail.com</p>

<p><h4>Банковский перевод:</h4>
Банк получателя ОАО «Альфа- Банк», г. Москва<br>
БИК 044525593<br>
К/с 30101810200000000593<br>
Наименование получателя: Езеев Федор Андреевич<br>
№ счета получателя: 40817810606050014327<br>
Назначение платежа: Дарение.
</p>
</div>