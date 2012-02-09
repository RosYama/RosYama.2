<p>userGroups already has some cron jobs installed and running.<br/>
Cron Jobs are operations that are performed on regular basis.<br/>
The two cron jobs already installed take care of reactivating users whose ban has expired and remove those that were not activated for seven days after the email was sent to
their email address.</p>

<h3>You can customize!</h3>

<?php echo CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/doc06.png", 'documentation images', array('class' => 'doc-images')); ?>

<p>
As you can see you can decide how many days have to pass before a specific cron runs again.<br/>
Right now cron jobs will be checked every time a user perform loggin actions.<br/>
If enough time has passed the cron job will be performed.
</p>

<h3>You can create!</h3>
<p>
Need some more cron jobs running?<br/>
You can easily create your own, because userGroups cron job system was designed using design patterns and modularity.<br/>
What do you have to do to create one?<br/>
Well, it's pretty easy, open the <b>UGCron.php</b> file located in the userGroups component directory and take a look at the two cron jobs
already installed.<br/>
The code is commented almost line by line, to make it easier for you to understand it.<br/>
</p>

<p>If you will use the same <b>cronTable</b> used by the two already existing cron jobs, your cron job will be automatically installed in userGroups.</p>

<h3>So, does userGroups find my cron job using magic?</h3>
<p>Of course it does! Just kidding.<br/>
To make userGroups aware of your cron job you'll need to put it in his own file, wherever you want to, and make sure that he is in a directory that
is already imported by Yii.<br/>
You can see what directories are imported in Yii on default and set your own in the application config file:
</p>

<div class="code"><code>
	'import'=&gt;array(
		'application.models.*',
		'application.components.*',
		<span class="highlight">'ext.myFolder.*',</span>
	),

</code></div>

<p>For more informations about how to use the dot notation when importing files, read the official Yii documentation.<br/>
Now there's just one more thing that you need to do: tell userGroups about your cron job.<br/>
In the config file set a crons parameter and assign an array to it. Each value of this array must be the class name of one of yours
cron jobs:</p>

<div class="code"><code>
	'userGroups'=&gt;array(
		'accessCode'=&gt;'ciao',
		<span class="highlight">'crons'=&gt;array('myCronClassName'),</span>
	),

</code></div>

<p>And that's it! Your cron job will be installed automatically inside userGroups.<br/>
If you get tired of your own cron you'll just need to delete it's name from the userGroups configuration settings inside the application
configuration file and then to delete it from the database push the button you can see in the Root Tools Cron Jobs section.</p>

<h3>What if i want to manage my crons not inside userGroups?</h3>
<p>You can setup your own db table to keep track of your crons and use the core functions of the UGCron class<br/>
This is the basic MySQL syntax to create the cron table:</p>

<div class="code"><code>
	CREATE TABLE your_table_name
	(
	id BIGINT(20) AUTO_INCREMENT,
	name VARCHAR(40),
	lapse INT(6),
	last_occurrence DATETIME,
	PRIMARY KEY (id)
	);

</code></div>

<p>If you want to trigger the cron jobs inside other pages you'll have to use the following lines of code:</p>

<div class="code"><code>
	UGCron::init();
	UGCron::add(new UGCJGarbageCollection);
	UGCron::run();

</code></div>

<p>Remember to add every single Cron you are going to use before using the UGCron::run() method</p>

<p>If you don't care about triggering the cronjobs somewhere else, the core script inside userGroups will take care of every single cron job
you'll add without you having to edit a single line of code.<br/>
So <b>don't ever edit UserGroupsIdentity</b> just to add your crons.</p>

<h3>Executing the cronjobs with crontab.</h3>
<p>Since version 1.8 you can use crontab to start the cronjobs. In this way you won't to rely anymore on random events</p>
<p>To use this feature just visit the following url with wget or whatever you feel like: /userGroups/admin/cron</p>
<p>This url can be accessed just from localhost and if you turn on the configuration setting.</p>