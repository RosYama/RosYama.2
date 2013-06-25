<div class="services">
  <ul class="auth-services clear">
  <?php
	foreach ($services as $name => $service) {
		$isUsrServ=false;
		foreach ($userServices as $userService){
			if ($name==$userService->name) $isUsrServ=true;
		}
		echo '<li class="auth-service '.$service->id.'">';
		if ($isUsrServ)
			echo CHtml::link('x', Array('/profile/delservice', 'type'=>$name), array(
				'class' => 'del-service-btn',
				'title' =>"удалить аккаунт",
			));
		$html = '<span class="auth-icon '.$service->id.($inProfile && !$isUsrServ ? ' gray' : '').'"><i></i></span>';
		$html .= '<span class="auth-title">'.$service->title.'</span>';		
		if (isset($service->jsArguments['autologin']) && !$service->jsArguments['autologin'])
			$html = CHtml::link($html, '#', array(
				'class' => 'auth-link '.$service->id,
				'onclick'=>'$("#bx_auth_serv_'.$name.'").show().siblings().hide(); return false;'
			));			
		else		
			$html = CHtml::link($html, array($action, 'service' => $name), array(
				'class' => 'auth-link '.$service->id,
			));
			
		echo $html;
		echo '</li>';		
	}
  ?>
  </ul>
  <div class="bx-auth-line"></div>
	<div class="bx-auth-service-form" id="bx_auth_serv">
	<form method="GET" action="/userGroups/">
	<input type="hidden" name="service" id="service_name" value="" size="20" />
	
	<div id="bx_auth_serv_livejournal" style="display:none">
	<span class="bx-ss-icon openid"></span>
	<input type="text" name="openid_identity_livejournal" value="" size="20" />
	<span>.livejournal.com</span>
	<input type="submit" class="button" name="" value="Войти" onclick="$('#service_name').val('livejournal');" />
	</div>	
	
	<div id="bx_auth_serv_mailru" style="display:none">
	<span class="bx-ss-icon openid"></span>
	<input type="text" name="openid_identity_mailru" value="" size="20" />
	<span>@mail.ru</span>
	<input type="submit" class="button" name="" value="Войти" onclick="$('#service_name').val('mailru'); $(this).parents('form').submit();" />
	</div>
	
	</form>
  </div>
</div>