<h3>Is the accessRules structure going to change?</h3>
<p>In you old controllers you had an accessRules method to provide access restrictions to your actions.</p>

<div class="code"><code>
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=&gt;array('index','view'),
				'users'=&gt;array('*'),
			),
			array('allow', // allow authenticated user to perform
				       // 'create' and 'update' actions
				'actions'=&gt;array('create','update'),
				'users'=&gt;array('@'),
			),
			array('allow', // allow admin user to perform 'admin'
				       // and 'delete' actions
				'actions'=&gt;array('admin','delete'),
				'users'=&gt;array('admin'),
			),
			array('deny',  // deny all users
				'users'=&gt;array('*'),
			),
		);
	}

</code></div>

<p>With your new filter you will have new rule sets that you can use by themeselves or combined togheter with both the new and old rules, without having to learn
a new data structure for writing them.</p>
<p>Now we'll be analyzing every single new type of rule that you can apply and will show you an example for each one of them.</p>
<p>Keep in mind that all of the results achieved by this new kind of rules could have been obtained using the good old expression rule.<br/>
The rules we provide however make it easier for you to write them, and furthermore will be much more easy to read.</p>

<h3>Ajax!</h3>
<p>With this rule you can force the user to load a specific action with or without ajax.</p>

<div class="code"><code>
	<b>// allow all users to perform 'index' and 'view' actions
	// just when loading the page via ajax</b>
	array('allow',
		'actions'=&gt;array('index','view'),
		'users'=&gt;array('*'),
		<span class="highlight">'ajax'=&gt;true,</span>
	),

</code></div>
<p>If you want to force the user to load a page without ajax you'll just have to assign to it a <b>boolean false</b></p>
<h3>Groups, of course!</h3>
<p>This rule allow you to choose which groups of users have access to a specific action.<br/>
This rule behaves just like the well known users rule:</p>

<div class="code"><code>
	<b>// allow all users who belong to the admin or core group
	// or to the group with id 5 to perform 'update' and 'admin' actions</b>
	array('allow',
		'actions'=&gt;array('update','admin'),
		<span class="highlight">'groups'=&gt;array('admin', 'core', 5),</span>
	),

</code></div>
<p>You can also use the <b>*</b> to indicate that you grant access to every group.<br/>
If this rule is setted guests won't have of course access to the page.</p>

<h3>Levels</h3>
<p>Each group has a level assigned, and so each user inherits the level of the group he belongs to.<br/>
You will be able to decide the level of each group, but will talk more about it in the <b>Root Tools section  of the documentation.</b></p>

<p>This is one of the most complex rules you can apply. You can tell the system that just users with a level higher then 4 can
execute a specific action, or you can decide that users with a level equal to 5 or higher then 50 can view the page, or
if you really want to you can decide that just those who match all the above defined rules can access that content on your
application.<br/>
Let's see some examples to make it more clear:</p>

<div class="code"><code>
	<b>// allow all users with a level lower then 10, or equal to 20
	// or higher or equal to 35 to perform an update action</b>
	array('allow',
		'actions'=&gt;array('update'),
		<span class="highlight">'level'=&gt;array('&lt;10', '20' ,'&gt;=35'),</span>
	),

</code></div>
<div class="code"><code>
	<b>// allow all users with a level lower then 40 AND
	// higher then 35 to perform an update action</b>
	array('allow',
		'actions'=&gt;array('update'),
		<span class="highlight">'level'=&gt;array('&lt;40', '&gt;35', 'strict' =&gt; true),</span>
	),

</code></div>
<p>I think both example are self explenatory. You can use any comparison operator, but remember to <b>never use =</b> because it will always return true.</p>
<p>If you are using this rule guests won't have access to the page, because they don't have a level.</p>

<h3>P-what? PBAC! Permission Based Access Control.</h3>
<p>userGroups lets you manage groups and users, but also their permission.<br/>
For each controller you will be able to assign to every user and/or group reading, writing, and admin permissions.<br/>
A user authomatically inherits his group permissions, but if you want to you can furthermore customize adding special rules to him.<br/>
We'll talk about assigning permissions in the <b>Root Tools</b> chapter of the documentation, so if you want more infos about it take a look overthere, but
right now what we already told you is enough to understand how to use this kind of rule.</p>
<p>With PBAC you'll be able to grant access to a page just to users with writing permissions on that controller or admin permission on another.</p>
<p>Let's make it simpler. You have in your applications two controllers, one that is managing companies, the other one employees.<br/>
Just users with admin permission on companies can delete them, so you write this rule:</p>

<div class="code"><code>
	<b>// allow users with admin permissions to delete companies</b>
	array('allow',
		'actions'=&gt;array('delete'),
		<span class="highlight">'pbac'=&gt;array('admin'),</span>
	),

</code></div>

<p>Easy, wasn't it? In the employee controller you want to grand access to the update action both to users with admin rights on the employee controller and
to users with admin rights on the companies controller</p>

<div class="code"><code>
	<b>// allow users with admin permissions on this controller or
	// the companies controller to access the update action.</b>
	array('allow',
		'actions'=&gt;array('update'),
		<span class="highlight">'pbac'=&gt;array('admin', 'companies.admin'),</span>
	),

</code></div>

<p>In our application we have a installed a module that manage employees certifications named <b>certEmployee</b>.<br/>
We want those users with admin permissions on that module user controller to have access to our admin
action on the employee controller, but we don't want them to access that page using ajax.<br/>
So we write the following rule inside our employee controller:</p>

<div class="code"><code>
	<b>// allow users with admin permissions on the user controller that
	// belongs to the certEmployee module to perform update actions, but
	// just if they access that page without using ajax</b>
	array('allow',
		'actions'=&gt;array('update'),
		<span class="highlight">'pbac'=&gt;array('certEmployee.user.admin'),</span>
		<span class="highlight">'ajax'=&gt;false,</span>
	),

</code></div>

<p>As you can see PBAC uses a dot notation. If you don't use any dot PBAC will check the user permissions on that controller, if you use one dot, the
word before it will be the controller name, if you use two dots the first word will be the module name.<br/>
Controllers in the basic applications are considered inside a module named Basic. So if you want to check the user admin permission on the site
controller that comes by default with a fresh new Yii application you'll have to write "<b>Basic.site.admin</b>"</p>
<p>Remember that PBAC is <b>case sensitive</b>. If you have doubts about how to write the name of your controller check its web path, print the
value of <b>Yii::app()-&gt;getController()-&gt;id</b>, or look at it's name on the <b>Root Tools</b> page.</p>

<p>If you use PBAC no guest user will have access to that page, because of course guest don't have any permission.</p>

<h3>How can i check this new user's infos inside my pages?</h3>
<p>Sometimes you will want to grant access to a page to every user, but just some of them should be able to view a specific link.<br/>
Here is the syntax you'll have to use to check each one of the new infos that the user is storing in session:</p>
<dl>
<dt>User ID</dt>
	<dd>Yii::app()-&gt;user-&gt;id</dd>
<dt>User name</dt>
	<dd>Yii::app()-&gt;user-&gt;name</dd>
<dt>User Group ID</dt>
	<dd>Yii::app()-&gt;user-&gt;group</dd>
<dt>User Group Name</dt>
	<dd>Yii::app()-&gt;user-&gt;groupName</dd>
<dt>User Group Level</dt>
	<dd>Yii::app()-&gt;user-&gt;level</dd>
<dt>User Email</dt>
	<dd>Yii::app()-&gt;user-&gt;email</dd>
<dt>User Home</dt>
	<dd>Yii::app()-&gt;user-&gt;home</dd>
	<dd>Home? Don't worry, we'll discuss about home in the Root Tools Chapter</dd>
</dl>

<h3>And what abou PBAC? Can I check permissions?</h3>
<p>Of course you can, but checking permissions could have been a too cumbersome task to perform, so we provided you a special method:</p>
<p><b>Yii::app()-&gt;user-&gt;pbac();</b></p>
<p>the user::pbac() method can accept up to 3 arguments, and just the first one is required.</p>
<dl>
<dt>1st argument: permission (mixed)</dt>
	<dd>
		This is the permission you need to check. You can send both a string or an array. You'll be able to use the same dot notation that
		you used in your rules.<br/>
		If you just need to check one permission you can just send a string, but if you need to check more then one
		you'll need to send an array, where each value is a permission to check (just like in the rule).<br/>
		Remember that when this method is used if you don't specifically tell the controller or module name the method will use those of the
		page that the user is browsing.
	</dd>
<dt>2nd argument: configuration (string)</dt>
	<dd>
		userGroups has several configurations, if you want to check at the same time both a configuration value and a permission to decide
		whenever a user can view a portion of your page you can input the configuration name inside this argument.
	</dd>
<dt>3rd argument: operator ('AND' or 'OR')</dt>
	<dd>
		Sometimes you may want to grant access to a link if the user has a specific permission OR if that configuration is active.<br/>
		By default the user::pbac() method assumes that you are using an AND operator, so if you need to you can pass the string OR to this
		argument to change the comparison.
	</dd>
</dl>

<h3>There's a new User in Town!</h3>
<p>You well know that in Yii you can target every user using a <b>*</b>, guests using a <b>?</b> and registered users using a <b>@</b>.</p>
<p>We added a new user tipology with this module: <b>#</b><br/>
<b>#</b> identifies users in recovery mode. Users are in recovery mode when they are activating their accounts or resetting their password.
Users in recovery mode can access just pages where guests can go and those that are specifically aimed to them.<br/>
No other user, not even guest can access pages reserved for the recovery mode users.</p>

<h3>Who's your Daddy? ROOT!</h3>
<p>When you installed this module you created on the fly a new user, the one you are now logged in as. That user is known as Root.<br/>
Root cannot be banned, his permissions cannot be changed, no one but himself can change his profile, and has automatically access granted to
every single page in the application, but those reserved to recovery mode users.</p>
<p>You'll never have to specifically tell any rule to grant access to ROOT, because he will always match any condition you write.</p>