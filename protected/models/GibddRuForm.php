<?php
/*
<table class="form-table data-table">
	<thead>
		<tr>
			<th colspan="2">	<div style="word-wrap: break-word; display: inline-block;">
	<h2>
		<a href="#" class="popup_show" id="regselector" rel="select_ugibdd" onclick="return false;" >77</a>
	</h2>
	<div class="graytext">Вы можете выбрать другой орган для направления обращения</div>
	</div>
</th>
		</tr>
	</thead>
	<tbody>
	<input type="hidden"  class="inputtext"  name="form_text_31" value="" size="2" /><input type="hidden"  class="inputtext"  name="form_text_35" value="" size="10" />		<tr>
			<td>
								Фамилия<font color='red'><span class='form-required starrequired'>*</span></font>							</td>
			<td><input type="text"  class="inputtext"  name="form_text_11" value="" size="50" /></td>
		</tr>
			<tr>
			<td>
								Имя<font color='red'><span class='form-required starrequired'>*</span></font>							</td>
			<td><input type="text"  class="inputtext"  name="form_text_12" value="" size="50" /></td>
		</tr>
			<tr>
			<td>
								Отчество							</td>
			<td><input type="text"  class="inputtext"  name="form_text_13" value="" size="50" /></td>
		</tr>
			<tr>
			<td>
								Почтовый индекс							</td>
			<td><input type="text"  class="inputtext"  name="form_text_14" value="" size="6" /></td>
		</tr>
			<tr>
			<td>
								Регион							</td>
			<td><input type="text"  class="inputtext"  name="form_text_15" value="" size="50" /></td>
		</tr>
			<tr>
			<td>
								Населенный пункт							</td>
			<td><input type="text"  class="inputtext"  name="form_text_16" value="" size="50" /></td>
		</tr>
			<tr>
			<td>
								Адрес							</td>
			<td><input type="text"  class="inputtext"  name="form_text_17" value="" size="67" /></td>
		</tr>
			<tr>
			<td>
								Электронная почта							</td>
			<td><input type="text"  class="inputtext"  name="form_email_18" value="" size="30" /></td>
		</tr>
			<tr>
			<td>
								Контактные телефоны							</td>
			<td><input type="text"  class="inputtext"  name="form_text_19" value="" size="30" /></td>
		</tr>
			<tr>
			<td>
								Порядок рассмотрения<font color='red'><span class='form-required starrequired'>*</span></font>							</td>
			<td><input type="checkbox"  id="20" name="form_checkbox_AGREE[]" id="form_checkbox_AGREE[]" value="20"><label for="20">Я ознакомлен(а) с порядком рассмотрения обращений</label></td>
		</tr>
			<tr>
			<td>
								Цель обращения<font color='red'><span class='form-required starrequired'>*</span></font>							</td>
			<td><select  class="inputselect"  name="form_dropdown_SUBJECT" id="form_dropdown_SUBJECT"><option value="37">Укажите цель обращения</option><option value="23">Предложение</option><option value="21">Жалоба</option><option value="25">Заявление</option><option value="39">Запрос</option><option value="22">Выражение благодарности сотрудникам ГИБДД</option><option value="24">Получение разъяснения нормативно-правовых актов</option><option value="36">Уведомление об уплате административного штрафа</option><option value="38">Прочее...</option></select></td>
		</tr>
			<tr>
			<td>
								Текст обращения<font color='red'><span class='form-required starrequired'>*</span></font>							</td>
			<td><textarea name="form_textarea_26" cols="71" rows="15"  class="inputtextarea" ></textarea></td>
		</tr>
			<tr>
			<td>
								Приложение							</td>
			<td> <input name="form_file_27"  class="inputfile"   size="0" type="file" /><span class="bx-input-file-desc"></span></td>
		</tr>
	<input type="hidden"  name="form_hidden_40" value="" />		<tr>
			<th colspan="2"><b>Защита от автоматического заполнения</b></th>
		</tr>
		<tr>
			<td>Введите символы с картинки<font color='red'><span class='form-required starrequired'>*</span></font></td>
			<td>
				<input type="hidden" name="captcha_sid" value="01c8098011940c6868d97ce2d1cc7b3c" /><img src="/bitrix/tools/captcha.php?captcha_sid=01c8098011940c6868d97ce2d1cc7b3c" width="180" height="40" /><br />
				<input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext" />
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="2">
			<p>
				<font color='red'><span class='form-required starrequired'>*</span></font> - так помечены поля, обязательные для заполнения			</p>
				<input  type="submit" name="web_form_submit" value="Направить обращение" />
								&nbsp;<input type="reset" value="Очистить форму" />
			</th>
		</tr>
	</tfoot>
*/

class GibddRuForm extends CFormModel
{
	public $WEB_FORM_ID=4;
	public $form_text_31; //регион ГИБДД
	public $form_text_35;
	public $form_text_11;
	public $form_text_12;
	public $form_text_13;
	public $form_text_14;
	public $form_text_15;
	public $form_text_16;
	public $form_text_17;
	public $form_email_18;
	public $form_text_19;
	public $form_dropdown_SUBJECT;
	public $form_textarea_26;
	public $form_file_27;
	public $form_hidden_40;
	public $captcha_word;
	public $web_form_submit="Направить обращение";
	public $iread='on';
	public $captcha_sid;
	public $sessid;
	public $reg;
	public $tmp;
	public $sbj;	
	public $pst;
	public $holes;
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('form_text_11, form_text_12, form_dropdown_SUBJECT, form_textarea_26, captcha_word, captcha_sid, WEB_FORM_ID, iread, sessid, reg, holes', 'required'),
			array('form_text_11, form_text_12, form_text_13, form_text_15, form_text_16', 'length', 'max'=>50),
			array('form_text_17', 'length', 'max'=>67),
			array('form_text_19, captcha_word', 'length', 'max'=>30),
			array('form_text_14', 'length', 'max'=>6),
			array('form_textarea_26', 'length'),
			array('form_file_27', 'length'),
			array('form_text_14, form_dropdown_SUBJECT, form_text_31', 'numerical', 'integerOnly'=>true),
			array('form_email_18', 'email'),
		);
	}	
		
	public function beforeValidate(){
		parent::beforeValidate();		
		return true;		
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return Array(
			'form_text_31'=>'',
			'form_text_35'=>'',
			'form_text_11'=>'Фамилия',
			'form_text_12'=>'Имя',
			'form_text_13'=>'Отчество',
			'form_text_14'=>'Почтовый индекс',
			'form_text_15'=>'Регион',
			'form_text_16'=>'Населенный пункт',
			'form_text_17'=>'Адрес',
			'form_email_18'=>'Электронная почта',
			'form_text_19'=>'Контактные телефоны',
			'form_dropdown_SUBJECT'=>'Цель обращения',
			'form_textarea_26'=>'Текст обращения',
			'form_file_27'=>'Приложение',
			'form_hidden_40'=>'',
			'captcha_word'=>'Введите символы с картинки',
			'web_form_submit'=>'Направить обращение',
		
		);
	}
}