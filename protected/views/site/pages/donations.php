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
<!-- Кнопка Я-Аудита -->
<p><button onclick="window.open('http://yaudit.org/41001550415485','newwindow','toolbar=0')">Посмотреть историю пожертвований</button></p>

<p><b>PayPal и пластиковые карточки</b> 
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="SNF55HDZWZJ76">
<input type="image" src="https://www.paypalobjects.com/ru_RU/RU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — более безопасный и легкий способ оплаты через Интернет!">
<img alt="" border="0" src="https://www.paypalobjects.com/ru_RU/i/scr/pixel.gif" width="1" height="1">
</form>
</p>

<p><b>QIWI</b> - 9161775694</p>

<p><b>WebMoney</b>
<ul>
<li>R356781320482 - рубли
<li>Z227171524847 - доллары
<li>E326031675260 - евро
</ul>
</p>

<p><h4>Банковский перевод:</h4>
Банк получателя ОАО «Альфа- Банк», г. Москва<br>
БИК 044525593<br>
К/с 30101810200000000593<br>
Наименование получателя: НО "Фонд борьбы с коррупцией"<br>
ИНН:7709471429<br>
КПП:770901001<br>
№ счета получателя: 40703810102710000001<br>
Назначение платежа: Пожертвование на реализацию проекта "РосЯма". НДС не облагается.
</p>

<hr />
<!-- Яндекс-форма по перечислению денег -->
<table>
<tr><td><b>Текущий баланс</b></td><td><b>Поддержать РосЯму</b></td></tr>
<tr><td>
<a href="https://money.yandex.ru/embed/?from=sbal" title="Виджеты Яндекс.Денег" style="width: 200px; height: 100px; display: block; margin-bottom: 0.6em; background: url('https://money.yandex.ru/share-balance.xml?id=209719&key=B8394464AE139BE7') 0 0 no-repeat; -background: none; -filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='https://money.yandex.ru/share-balance.xml?id=209719&key=B8394464AE139BE7', sizingMethod = 'crop');"></a>
</td><td>
<iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/donate.xml?uid=41001550415485&amp;default-sum=300&amp;targets=%d0%bd%d0%b0+%d1%80%d0%b0%d0%b1%d0%be%d1%82%d1%83+%d0%bf%d1%80%d0%be%d0%b5%d0%ba%d1%82%d0%b0&amp;project-name=%d0%a0%d0%be%d1%81%d0%af%d0%bc%d0%b0&amp;project-site=http%3a%2f%2frosyama.ru&amp;button-text=02&amp;hint=" width="450" height="106"></iframe>
</td></tr>
</table>

<hr />
<ul>
<li>Финансовый отчёт за 2011 год: <a href="http://fezeev.livejournal.com/50545.html">http://fezeev.livejournal.com/50545.html</a>
<li>Финансовый отчёт за январь-май 2012 года: <a href="http://fezeev.livejournal.com/61845.html">http://fezeev.livejournal.com/61845.html</a>
<li>Финансовый отчёт за июнь 2012 года: <a href="http://fezeev.livejournal.com/63802.html">http://fezeev.livejournal.com/63802.html</a>
<li>Финансовый отчёт за июль 2012 года: <a href="http://fezeev.livejournal.com/64709.html">http://fezeev.livejournal.com/64709.html</a>
<li>Финансовый отчёт за август 2012 года: <a href="http://fezeev.livejournal.com/65617.html">http://fezeev.livejournal.com/65617.html</a>
</ul>
</div>
