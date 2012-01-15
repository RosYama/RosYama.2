<p>userGroups provides you an extension of the core AccessControlFilter to check users permissions when they try to access to a specific action inside a controller.</p>
<p>Using <b>userGroupsAccessControl</b> you won't lose any of the core features of the core filter, but you'll gain many more.</p>

<h3>What do I have to change in my controllers?</h3>
<p>If you don't really care about using the <b>UserGroupsAccessControl</b> you won't have to change anything, our new filter is not overriding or disabling anything,
but what's the point of installing this module if you are not going to use the new filter?</p>

<p>Now let's take a look of what usually looks like one of your controllers. You used to have something like this:</p>

<div class="code"><code>
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
	
</code></div>

<p>Now to use your brand new filter you have to change your code to make it look like this:</p>

<div class="code"><code>
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			<span class="highlight">'userGroupsAccessControl',</span> // perform access control for CRUD operations
		);
	}
	
</code></div>

<h3>And my old accessRules? What's going to happen to my old accessRules?</h3>
<p>The answer is simple: absolutely nothing!<br/>
<b>userGroupsAcessControl</b> look for his rules in the same method where the core accessControlFilter does, so your old rules will still be working</p>
<p>Now go to the next chapter and check out the new kind of accessRules that userGroups supports</p>