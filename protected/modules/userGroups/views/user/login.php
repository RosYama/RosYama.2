<div class="bx-auth">
	<div class="bx-auth-title">Войти на сайт</div>
	<div class="bx-auth-note">Пожалуйста, авторизуйтесь:</div>

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableAjaxValidation'=>false,
		'focus'=>array($model, 'username'),
	)); ?>
	
	<table class="bx-auth-table">
			<tr>
				<td class="bx-auth-label"><?php echo $form->labelEx($model,'username'); ?>:</td>
				<td><?php echo $form->textField($model,'username'); ?>
					<?php echo $form->error($model,'username'); ?>
				</td>
			</tr>
			<tr>
				<td class="bx-auth-label"><?php echo $form->labelEx($model,'password'); ?>:</td>
				<td><?php echo $form->passwordField($model,'password'); ?>
					<?php echo $form->error($model,'password'); ?>
				</td>
			</tr>
						<tr>
				<td></td>
				<td><?php echo $form->checkBox($model,'rememberMe'); ?>
					<?php echo $form->label($model,'rememberMe'); ?>
					<?php echo $form->error($model,'rememberMe'); ?>
			</td>
			</tr>
			<tr>
				<td></td>
				<td class="authorize-submit-cell"><?php echo CHtml::submitButton('Войти'); ?></td>
			</tr>
		</table>
		
		<noindex>
			<p>
				<a href="/personal/holes.php?forgot_password=yes" rel="nofollow">Забыли свой пароль?</a>
			</p>
		</noindex>
		
		<?php if (UserGroupsConfiguration::findRule('registration')): ?>
		<noindex>
			<p>
				<?php echo CHtml::link('Зарегистрироваться', array('/userGroups/user/register'))?><br />
				Если вы впервые на сайте, заполните, пожалуйста, регистрационную форму. 
			</p>
		</noindex>
		<?php endif; ?>		
				
	
	<?php $this->endWidget(); ?>	
	
</div>

<script type="text/javascript">
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
</script>



<div class="bx-auth">
	<form method="post" name="bx_auth_services" target="_top" action="/personal/holes.php?login=yes">
		<div class="bx-auth-title">Войти как пользователь</div>
		<div class="bx-auth-note">Вы можете войти на сайт, если вы зарегистрированы на одном из этих сервисов:</div>
		<div class="bx-auth-services">
			<div><a href="javascript:void(0)" onclick="BxShowAuthService('Facebook', '')" id="bx_auth_href_Facebook"><i class="bx-ss-icon facebook"></i><b>Facebook</b></a></div>
			<div><a href="javascript:void(0)" onclick="BxShowAuthService('Twitter', '')" id="bx_auth_href_Twitter"><i class="bx-ss-icon twitter"></i><b>Twitter</b></a></div>
			<div><a href="javascript:void(0)" onclick="BxShowAuthService('Livejournal', '')" id="bx_auth_href_Livejournal"><i class="bx-ss-icon livejournal"></i><b>Livejournal</b></a></div>
			<div><a href="javascript:void(0)" onclick="BxShowAuthService('VKontakte', '')" id="bx_auth_href_VKontakte"><i class="bx-ss-icon vkontakte"></i><b>ВКонтакте</b></a></div>
			<div><a href="javascript:void(0)" onclick="BxShowAuthService('OpenID', '')" id="bx_auth_href_OpenID"><i class="bx-ss-icon openid"></i><b>OpenID</b></a></div>
			<div><a href="javascript:void(0)" onclick="BxShowAuthService('YandexOpenID', '')" id="bx_auth_href_YandexOpenID"><i class="bx-ss-icon yandex"></i><b>Яндекс</b></a></div>
			<div><a href="javascript:void(0)" onclick="BxShowAuthService('GoogleOAuth', '')" id="bx_auth_href_GoogleOAuth"><i class="bx-ss-icon google"></i><b>Google</b></a></div>
			<div><a href="javascript:void(0)" onclick="BxShowAuthService('MyMailRu', '')" id="bx_auth_href_MyMailRu"><i class="bx-ss-icon mymailru"></i><b>Мой Мир</b></a></div>
			<div><a href="javascript:void(0)" onclick="BxShowAuthService('MailRuOpenID', '')" id="bx_auth_href_MailRuOpenID"><i class="bx-ss-icon openid-mail-ru"></i><b>Mail.Ru</b></a></div>
			<div><a href="javascript:void(0)" onclick="BxShowAuthService('Rambler', '')" id="bx_auth_href_Rambler"><i class="bx-ss-icon rambler"></i><b>Rambler</b></a></div>
			<div><a href="javascript:void(0)" onclick="BxShowAuthService('Liveinternet', '')" id="bx_auth_href_Liveinternet"><i class="bx-ss-icon liveinternet"></i><b>Liveinternet</b></a></div>
			<div><a href="javascript:void(0)" onclick="BxShowAuthService('Blogger', '')" id="bx_auth_href_Blogger"><i class="bx-ss-icon blogger"></i><b>Blogger</b></a></div>
		</div>
		<div class="bx-auth-line"></div>
		<div class="bx-auth-service-form" id="bx_auth_serv" style="display:none">
			<div id="bx_auth_serv_Facebook" style="display:none"><a href="javascript:void(0)" onclick="BX.util.popup('https://www.facebook.com/dialog/oauth?client_id=173225386065828&amp;redirect_uri=http%3A%2F%2Frosyama.ru%2Fpersonal%2Fholes.php%3Fauth_service_id%3DFacebook&amp;scope=email&amp;display=popup', 580, 400)" class="bx-ss-button facebook-button"></a><span class="bx-spacer"></span><span>Используйте вашу учетную запись на Facebook.com для входа на сайт.</span></div>
			<div id="bx_auth_serv_Twitter" style="display:none"><a href="javascript:void(0)" onclick="BX.util.popup('/personal/holes.php?auth_service_id=Twitter', 800, 450)" class="bx-ss-button twitter-button"></a><span class="bx-spacer"></span><span>Используйте вашу учетную запись на Twitter.com для входа на сайт.</span></div>
			<div id="bx_auth_serv_Livejournal" style="display:none">
<span class="bx-ss-icon openid"></span>
<input type="text" name="OPENID_IDENTITY_LIVEJOURNAL" value="" size="20" />
<span>.livejournal.com</span>
<input type="submit" class="button" name="" value="Войти" />
</div>
			<div id="bx_auth_serv_VKontakte" style="display:none">
<a href="javascript:void(0)" onclick="VK.Auth.login(BxVKAuthInfo);" class="bx-ss-button vkontakte-button"></a><span class="bx-spacer"></span><span>Используйте вашу учетную запись VKontakte.ru для входа на сайт.</span></div>
			<div id="bx_auth_serv_OpenID" style="display:none">
<span class="bx-ss-icon openid"></span>
<span>OpenID:</span>
<input type="text" name="OPENID_IDENTITY_OPENID" value="ilya_123" size="40" />
<input type="submit" class="button" name="" value="Войти" />
</div>
			<div id="bx_auth_serv_YandexOpenID" style="display:none">
<span class="bx-ss-icon openid"></span>
<input type="text" name="OPENID_IDENTITY_YANDEX" value="" size="20" />
<span>@yandex.ru</span>
<input type="submit" class="button" name="" value="Войти" />
</div>
			<div id="bx_auth_serv_GoogleOAuth" style="display:none"><a href="javascript:void(0)" onclick="BX.util.popup('https://accounts.google.com/o/oauth2/auth?client_id=577493071709.apps.googleusercontent.com&amp;redirect_uri=http%3A%2F%2Frosyama.ru%2Fbitrix%2Ftools%2Foauth%2Fgoogle.php&amp;scope=https%3A%2F%2Fwww.google.com%2Fm8%2Ffeeds%2F&amp;response_type=code&amp;state=site_id%3Ds1%26backurl%3D%252Fpersonal%252Fholes.php', 580, 400)" class="bx-ss-button google-button"></a><span class="bx-spacer"></span><span>Используйте вашу учетную запись Google для входа на сайт.</span></div>
			<div id="bx_auth_serv_MyMailRu" style="display:none">
<a href="javascript:void(0)" onclick="mailru.connect.login();" class="bx-ss-button mymailru-button"></a><span class="bx-spacer"></span><span>Используйте вашу учетную запись Мой Мир@Mail.ru для входа на сайт.</span></div>
			<div id="bx_auth_serv_MailRuOpenID" style="display:none">
<span class="bx-ss-icon openid"></span>
<input type="text" name="OPENID_IDENTITY_MAILRU" value="" size="20" />
<span>@mail.ru</span>
<input type="submit" class="button" name="" value="Войти" />
</div>
			<div id="bx_auth_serv_Rambler" style="display:none">
<span class="bx-ss-icon openid"></span>
<input type="text" name="OPENID_IDENTITY_RAMBLER" value="" size="20" />
<span>@rambler.ru</span>
<input type="submit" class="button" name="" value="Войти" />
</div>
			<div id="bx_auth_serv_Liveinternet" style="display:none">
<span class="bx-ss-icon openid"></span>
<span>http://www.liveinternet.ru/users/</span>
<input type="text" name="OPENID_IDENTITY_LIVEINTERNET" value="" size="20" />
<span>/</span>
<input type="submit" class="button" name="" value="Войти" />
</div>
			<div id="bx_auth_serv_Blogger" style="display:none">
<span class="bx-ss-icon openid"></span>
<input type="text" name="OPENID_IDENTITY_BLOGGER" value="" size="20" />
<span>.blogspot.com</span>
<input type="submit" class="button" name="" value="Войти" />
</div>
		</div>
		<input type="hidden" name="auth_service_id" value="" />
	</form>
</div>


<div id="userGroups-container">
	<?php if(isset(Yii::app()->request->cookies['success'])): ?>
	<div class="info">
		<?php echo Yii::app()->request->cookies['success']->value; ?>
		<?php unset(Yii::app()->request->cookies['success']);?>
	</div>
	<?php endif; ?>
	<?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
	<?php endif; ?>
	<?php if(Yii::app()->user->hasFlash('mail')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('mail'); ?>
    </div>
	<?php endif; ?>
	<div class="form center">
	
	</div><!-- form -->
</div>