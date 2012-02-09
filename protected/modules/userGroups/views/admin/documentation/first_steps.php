<h3>Welcome aboard!</h3>
<p>You probably just installed this module, and you are asking yourself "<b><i>so now what?</i></b>".</p>
<p>userGroups does most of the installation all by itself, but there are a couple of things that you still have to do to make sure everything is working just fine:<br/>
change the login and logout links in your menus.</p>

<p>If this is a fresh Yii application those links are pointing towards these two addresses: "<b>/site/login</b>" and "<b>/site/logout</b>".<br/>
The login path in userGroups is extremely easy:"<b>/userGroups</b>".<br/>
The logout path is instead:"<b>/userGroups/user/logout</b>".<br/>
You could actually leave the default logout path as it is, because the default logout action just calls the logout method inside the WebUser class,
but someday you could forget that the logout link in your menus were pointining to a perhaps old and now useless controller and you could delete it by mistake.<br/>
So just change it now and you won't ever have to worry about it.</p>
<p>And that's it, now you are setted and good to go, so look at the next chapter to learn how to use the new advaced accessRules.</p>

<h3>NB: your old Controller is gone!</h3>
<p>He is gone, but not really far indeed. During the installation process the userGroups basic controller was copied inside your application components directory
and your old controller was renamed as <b>_old_Controller.php</b>.<br/>
If you had some special settings made in your old controller remember that you have to copy them in the brend new controller provided by userGroups, otherwise
somethings may not work in your application.</p>