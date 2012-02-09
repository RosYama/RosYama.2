<h3>This is your Palantir!</h3>
<p>In Root Tools you have access to everything you need:<br/>
Here you can create users and groups, change some of their data and update configurations and Cron Jobs.
</p>
<h3>Users and Groups</h3>
<p>
These are the two panels you'll use the most.<br/>
Here everyone that has access to this page can see every user or group whose level is below their own.<br/>
Root of course will be able to see every user and group, but himself.<br/>
You can easilly create user and groups and change most of their datas.</p>

<p>This is the only page where someone can manage groups. You can set and change their levels and delete them.<br/>
This is also the only page where you can actually delete users from the system.<br/>
Always be careful when you delete a group, because every user who belongs to it will be deleted as well.<br/>
But now let's take a look at the permissions configurations.</p>

<h3>Permission Configurations</h3>
<p>If you click on a user/group or try to create one you'll see a list of the controllers inside your application, and a checkbox for read, write and admin.<br/>
Next to that checkbox you'll see a small icon. If you hold your mouse still on that icon you will see a brief description of what rights that permission will grant.</p>
<?php echo CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/doc01.png", 'documentation images', array('class' => 'doc-images')); ?>
<p>Now that you installed the module you'll see that some of those icons are red, while others are blu. Red icons state that no description was provided yet, and that
is something we want to solve right now!</p>
<p>Go inside one of your controller's file, and add the following lines of code:</p>

<div class="code"><code> 
	<span class="highlight">static $_permissionControl = array(</span>
		<span class="highlight">'write' =&gt; 'with this permission you can create new whatever',</span>
		<span class="highlight">'label' =&gt; 'Better Label');</span>
		
</code></div>

<p>As you can easilly guess each key of this array refers to the permission, and the value it's his description.<br/>
If you omit a permission, no checkbox will be displayed for it in the root tools, something you are going to do really often:
you won't alwasy need three kind of permissions for each controller.<br/>
Label states the name you want to be displayed in Root Tools for that controller.<br/>
You'll use this a lot to make permission management even more user friendly.</p>

<?php echo CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/doc02.png", 'documentation images', array('class' => 'doc-images')); ?>

<p>If you don't want this controller to show at all in Root Tools change the value of <b>$_permissionControl</b> to boolean false.</p>

<div class="code"><code> 
	<span class="highlight">static $_permissionControl = false;</span>
	
</code></div>

<p>As we already told you in the previous chapters, users inherit their group permissions.<br/>
If you open a user details you'll see some green checkmarks next to the permissions that his group already has.</p>

<?php echo CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/doc03.png", 'documentation images', array('class' => 'doc-images')); ?>

<p>You won't need to give those permissions again to that user, because he will always have them as long as his group does, but you can
add other permissions to him that are going to be combined with those of his group.</p>

<h3>Homes?</h3>
<p>Each group can have a home. The home is the page where that user is redirected after login.<br/>
Users can have a home too. If a user has a declared home he will be redirected to this one instead theat the one of his group.<br/>
As you can see in the drop down list the controller will still displayed with the label you assigned to it in <b>$_permissionControl</b></p>

<p>If you want to assign a label to a home, but don't need to set permissions for that controller, you can just put a label value in <b>$_permissionControl</b></p>

<div class="code"><code> 
	<span class="highlight">static $_permissionControl = array('label' =&gt; 'Better Label');</span>
	
</code></div>

<?php echo CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/doc04.png", 'documentation images', array('class' => 'doc-images')); ?>

<p>As you can see in this way the drop down list will display the label you chosed, but no permission will show up in the above table.</p>

<h3>Many ways to create a user!</h3>
<p>Here you can create a new user deciding both username and password, or just the username, or just the password, or none of them.<br/>
If you won't create a complete user that user will be considered in a <b>waiting activation status</b> and an email will be sent to him, using
the mail address you wrote inside the application configuration.</p>

<?php echo CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/doc05.png", 'documentation images', array('class' => 'doc-images')); ?>

<p>In this email the user will find the parameters to activate his own account, using the already mentioned recovery mode.<br/>
When activating his account the user will be prompted to enter the missing data, and change his temporary username if no one was
choosen for him.</p>

<h3>Configurations</h3>
<p>userGroups has several configurations that you can use at will. The system istalled himself with the most common settings.<br/>
We are not going to explain to you here what every single configuration does, because there are already exaustive descriptions for each of them
in the root tools.<br/>
Feel free to speriment and chose whatever configuration you like the best for your application.</p>