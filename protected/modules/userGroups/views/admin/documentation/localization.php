<h3>Can I localize this module?</h3>

<p>Of course you can!<br/>
userGroups is already localized in Italian, but if you want to localize it in any other language we setted up everything to make it as easy as
possible.</p>

<p>If yiic is working inside your shell/terminal you can just go from it inside your application root directory and type this command:<br/>
<b>yiic message protected/modules/userGroups/messages/local.php</b></p>

<p>Before running that command remember to change the languages value inside the <b>userGroups/messages/local.php</b> file.</p>
<p>This command will take care of almost everything, but two things:</p>

<dl>
<dt>install views</dt>
	<dd>located inside <b>/userGroups/views/install</b></dd>
<dt>documentation</dt>
	<dd>located inside <b>/userGroups/views/admin/documentation/</b></dd>
</dl>

<p>You'll need to translate those file and put them inside a subdirectory named after the language code.<br/>
You can easily see an example of what we did with italian.</p>

<h3>One More Thing! Configurations descriptions and Controllers descriptions.</h3>
<p>Configurations descriptions are located inside the database, while controller descriptions used in Root Tools are class constants.<br/>
That's why we couldn't actually execute a Yii::t() method on them, but we had to do it on a variable.<br/>
To localize that content just copy the <b>conf_description.php</b> and <b>cont_description.php</b> files located inside the
<b>/userGroups/messages/it/</b> directory and paste them into the directory created by yiic, overwriting those already in that folder, then translate them.</p>