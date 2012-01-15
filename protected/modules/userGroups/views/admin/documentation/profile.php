<p>Most of the times you'll need many more fields in your profile, but we didn't want you to start digging around the module's models and controllers to
accomplish the results you need.</p>

<p>Since version 1.1 userGroups comes with an easy way to extend your user profiles: <b>Profile Extensions</b></p>

<h3>First Step: create the database table and model</h3>
<p>Create a database table just like you where always used to do.<br/>
Just remember to insert a column named <b>ug_id</b>, make it a bigint with length of 20 and unique.</p>

<p>Right after that create your model, you can do it manually or using gii if you prefer.</p>

<h3>Adding the profileViews method to the class</h3>
<p>To work properly userGroups needs that you add a new method to your model: <b>profileViews</b></p>

<div class="code"><code>
	/**
	 * returns an array that contains the views name to be loaded
	 * @return array
	 */
	public function profileViews()
	{
		return array(
			UserGroupsUser::VIEW =&gt; 'index',
			UserGroupsUser::EDIT =&gt; 'update',
			UserGroupsUser::REGISTRATION =&gt; 'registration',
		);
	}

</code></div>

<p>profileViews will be called by the userGroups module every single time it will try to render a profile view.<br/>
Let's say that a user goes to his page profile.<br/>
userGroups will look in the list of models that extend the user profile, looking for those that are returning a value for the
<b>userGroupsUser::VIEW</b> key.<br/>
If the model returns a value for that key, userGroups will load the view named after that value.</p>

<p>To let userGroups find your view file you'll have to put it at this path:<br/>
<b>protected/views/TABLENAME/</b><br/>

So if you created a new model for the table <i>user_hobbies</i>, userGroups will look for the view file inside<br/>
<b>protected/views/user_hobbies/</b></p>

<h3>Creating UserGroupsUser::VIEW views</h3>
<p>View pages are loaded when someone loads a profile.<br/>
In your view file related to UserGroupsUser::VIEW you will have a <b>$model</b> variable that will contain your Profile Extension model.</p>


<h3>Creating UserGroupsUser::UPDATE views</h3>
<p>In the update views you'll still have your profile extension model inside the <b>$model</b> variable.<br/>
You will also have a variable named <b>$user_id</b> containing the user ID of the profile that is being edited.</p>
<p>In your update view you can use ajax validation and we encourage you to do so, but you have to pay attention to the submit button.<br/>
Here you can see an example of submit button:</p>

<div class="code"><code>
	echo CHtml::ajaxSubmitButton(
		Yii::t('userGroupsModule.general','Update External Profile'), // first line
		Yii::app()-&gt;baseUrl . '/userGroups/user/update/id/'.$user_id, // second line
		array('update' =&gt; '#userGroups-container'), // third line
		array('id' =&gt; 'submit-profile-'.$model-&gt;id.rand()) // fourth line
	);

</code></div>

<p>In the first line we are using Yii::t() method to define the name of the submit button. You don't have to do so if you don't
care about localization, so you can use a string here instead.<br/>
In the second line we are setting the path of the form. Make sure you right it down exactly as it is, unless you want to use
your own controller to deal with the saving action. In that case point the form to the correct path.<br/>
In the third line we set the javascript action to perform right after the form execution. Copy it exactly as it is.<br/>
In the fourth line we set a random id name for the button. That's an important thing to do if you don't want to have some
ajax overloading.</p>

<h3>Creating UserGroupsUser::REGISTRATION views</h3>
<p>In the registration view files you'll just have to insert your form inputs and nothing else.<br/>
Your inputs will be part of the registration form and userGroups will take care of validation and storing the data on the database.<br/>
In this views you'll have your Profile Extension model inside the <b>$model</b> variable.</p>

<h3>So i can use normal model validation and whatsoever?</h3>
<p>Of course you can, and you can use whatever other model features you are used to, like afterFind, beforeSave, and so on.<br/>
Right now userGroups supports profile extension just in profile view and editing actions, not on registration or recovery.<br/>
Anyway we encourage you to use rules based on scenarios, to avoid future problems.<br/>
The scenario used on the UserGroupsUser::UPDATE views is: <b>updateProfile</b>.<br/>
The scenario used on the UserGroupsUser::REGISTRATION view is <b>registration</b>.</p>


<h3>So, after I set all this stuff what do i have to do to make userGroups aware of my Profile Extension?</h3>
<p>That, as usual, is pretty easy: open your application config file and add the profile paramter to the userGroups module.<br/>
This parameter must contain an array where every value is the name of the model class that you are going to include.</p>

<div class="code"><code>
	'userGroups'=&gt;array(
		'accessCode'=&gt;'your access code, by now useless',
		<span class="highlight">'profile' =&gt; array('UserHobbies'),</span>
	)

</code></div>

<h3>Is Profile Extensions using the relations system of CActiveRecord?</h3>
<p>Of course it is, it's forging HAS_ONE relations on the fly, so if you happen to use the UserGroupsUser model you can find all the data of
your Profile Extensions, just using this syntax:<br/>
<b>$model-&gt;relMODEL_CLASS_NAME</b><br/>
So, in the UserHobbies example we used until now this would be the right syntax:
<b>$model-&gt;relUserHobbies</b> 

<h3>How can I store Profile Extension's data in session?</h3>
<p>You simply need to implement on more method inside your Profile Extension model:</p>

<div class="code"><code>
	/**
	 * returns an array that contains the names of the attributes that will
	 * be stored in session
	 * @return array
	 */
	public function profileSessionData()
	{
		return array(
			'attribute_name',
		);
	}

</code></div>

<p>To load that data you'll simply use this line of code:<br/>
<strong>Yii::app()-&gt;user-&gt;profile('MODEL_CLASS_NAME', 'ATTRIBUTE_NAME');</strong><br/>
ie:<br/>
<strong>Yii::app()-&gt;user-&gt;profile('UserHobbies', 'hobby');</strong><br/>
</p>