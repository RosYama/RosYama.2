<?php
/*
[2] => <input id="f_token" type="hidden" name="f_token" />
            [3] => <input id="f_gai_regkod" type="hidden" name="f_gai_regkod" class="txt" value="00"/>
            [4] => <input id="f_gai" type="text" name="f_gai" class="txt" readonly="readonly" title="Кликните для изменения" value="Уточните регион, в который адресуете обращение" readonly="readonly" />
            [5] => <input id="f_subj_kod" type="hidden" name="f_subj_kod" value="609" />
            [6] => <input id="f_subj" type="text" name="f_subj" class="txt" readonly="readonly" title="Кликните для изменения" value="Прочее..." readonly="readonly" />
            [7] => <input id="f_fam" type="text" name="f_fam" class="txt" maxlength="40" />
            [8] => <input id="f_name" type="text" name="f_name" class="txt" maxlength="40" />
            [9] => <input id="f_coname" type="text" name="f_coname" class="txt" maxlength="40" />
            [10] => <input type="radio" id="f_answer_method" name="f_answer_method" value="616" />
            [11] => <input type="radio" name="f_answer_method" value="615" />
            [12] => <input id="f_ind" type="text" name="f_ind" class="txt" maxlength="8" />
            [13] => <input id="f_reg" type="text" name="f_reg" class="txt" maxlength="50" />
            [14] => <input id="f_npunkt" type="text" name="f_npunkt" class="txt" maxlength="50" />
            [15] => <input id="f_addr" type="text" name="f_addr" class="txt" maxlength="100" />
            [16] => <input id="f_email" type="text" name="f_email" class="txt" maxlength="100"/>
            [17] => <input id="f_phone" type="text" name="f_phone" class="txt" maxlength="50" />
            [18] => <input class="captcha_word" type="text" size="15" name="captcha_word" autocomplete="off" />
            [19] => <input class="captcha_code" type="hidden" name="captcha_code">
            [20] => <input type="file" class="attach" name="attach[]" onchange="$(this).next(\'span.fakefile\').text(this.value);" />
*/

class GibddRuForm extends CFormModel
{
	public $f_token;
	public $f_gai_regkod;
	public $f_gai='Уточните регион, в который адресуете обращение';
	public $f_subj_kod=609; //Заявление
	public $f_subj='Прочее...';
	public $f_fam;
	public $f_name;
	public $f_coname;
	public $f_answer_method=615; //616-в письменной, 615 - в электронной
	public $f_ind;
	public $f_reg;
	public $f_npunkt;
	public $f_addr;
	public $f_email;
	public $f_phone;
	public $f_msg;
	public $captcha_word;
	public $captcha_code;
	public $attach;
	public $web_form_submit="Направить обращение";
	public $save='Y';
	

	public $sessid;
	
	public $holes;
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('f_gai_regkod, f_fam, f_name, f_answer_method, f_msg, holes, captcha_word', 'required'),
			array('f_fam, f_name, f_coname', 'length', 'max'=>40),
			array('f_reg, f_npunkt, f_phone', 'length', 'max'=>50),
			array('f_addr, f_email', 'length', 'max'=>100),
			array('captcha_word', 'length', 'max'=>30),
			array('f_ind', 'length', 'max'=>6),
			array('f_msg', 'length'),		
			array('f_ind', 'numerical', 'integerOnly'=>true),
			array('f_email', 'email'),
			array('attach, captcha_code, sessid', 'safe'),
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
			"f_subj_kod"=>"Цель обращения",
			"f_fam"=>"Фамилия",
			"f_name"=>"Имя",
			"f_coname"=>"Отчество",
			"f_answer_method"=>"Получить ответ",
			"f_ind"=>"Почтовый индекс",
			"f_reg"=>"Регион",
			"f_npunkt"=>"Населенный пункт",
			"f_addr"=>"Адрес",
			"f_email"=>"Электронная почта",
			"f_phone"=>"Контактные телефоны",
			"f_msg"=>"Текст обращения",
			"captcha_word"=>"Введите символы с картинки",
			"captcha_code"=>"",
			"attach"=>"Приложение"
			
		
		);
	}
}