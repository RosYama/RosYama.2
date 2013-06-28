<?php

return array (
  0 => 
  array (
    'id' => 1,
    'rule' => 'version',
    'value' => '1.8',
    'options' => 'CONST',
    'description' => 'userGroups version',
  ),
  1 => 
  array (
    'id' => 2,
    'rule' => 'password_strength',
    'value' => '0',
    'options' => 'a:3:{i:0;s:4:"weak";i:1;s:6:"medium";i:2;s:6:"strong";}',
    'description' => 'password strength:<br/>weak: password of at least 5 characters, any character allowed.<br/>
			medium: password of at least 5 characters, must contain at least 2 digits and 2 letters.<br/>
			strong: password of at least 5 characters, must contain at least 2 digits, 2 letters and a special character.',
  ),
  2 => 
  array (
    'id' => 3,
    'rule' => 'registration',
    'value' => 'TRUE',
    'options' => 'BOOL',
    'description' => 'allow user registration',
  ),
  3 => 
  array (
    'id' => 4,
    'rule' => 'public_user_list',
    'value' => 'FALSE',
    'options' => 'BOOL',
    'description' => 'logged users can see the complete user list',
  ),
  4 => 
  array (
    'id' => 5,
    'rule' => 'public_profiles',
    'value' => 'FALSE',
    'options' => 'BOOL',
    'description' => 'allow everyone, even guests, to see user profiles',
  ),
  5 => 
  array (
    'id' => 6,
    'rule' => 'profile_privacy',
    'value' => 'TRUE',
    'options' => 'BOOL',
    'description' => 'logged user can see other users profiles',
  ),
  6 => 
  array (
    'id' => 7,
    'rule' => 'personal_home',
    'value' => 'FALSE',
    'options' => 'BOOL',
    'description' => 'users can set their own home',
  ),
  7 => 
  array (
    'id' => 8,
    'rule' => 'simple_password_reset',
    'value' => 'TRUE',
    'options' => 'BOOL',
    'description' => 'if true users just have to provide user and email to reset their password.<br/>Otherwise they will have to answer their custom question',
  ),
  8 => 
  array (
    'id' => 9,
    'rule' => 'user_need_activation',
    'value' => 'TRUE',
    'options' => 'BOOL',
    'description' => 'if true when a user creates an account a mail with an activation code will be sent to his email address',
  ),
  9 => 
  array (
    'id' => 10,
    'rule' => 'user_need_approval',
    'value' => 'FALSE',
    'options' => 'BOOL',
    'description' => 'if true when a user creates an account a user with user admin rights will have to approve the registration.<br/>If both this setting and user_need_activation are true the user will need to activate is account first and then will need the approval',
  ),
  10 => 
  array (
    'id' => 11,
    'rule' => 'user_registration_group',
    'value' => '2',
    'options' => 'GROUP_LIST',
    'description' => 'the group new users automatically belong to',
  ),
  11 => 
  array (
    'id' => 12,
    'rule' => 'dumb_admin',
    'value' => 'TRUE',
    'options' => 'BOOL',
    'description' => 'users with just admin write permissions won\'t see the Main Configuration and Cron Jobs panels',
  ),
  12 => 
  array (
    'id' => 13,
    'rule' => 'super_admin',
    'value' => 'FALSE',
    'options' => 'BOOL',
    'description' => 'users with userGroups admin admin permission will have access to everything, just like root',
  ),
  13 => 
  array (
    'id' => 14,
    'rule' => 'permission_cascade',
    'value' => 'TRUE',
    'options' => 'BOOL',
    'description' => 'if a user has on a controller admin permissions will have access to write and read pages. If he has write permissions will also have access to read pages',
  ),
  14 => 
  array (
    'id' => 15,
    'rule' => 'server_executed_crons',
    'value' => 'FALSE',
    'options' => 'BOOL',
    'description' => 'if true crons must be executed from the server using a crontab',
  ),
);