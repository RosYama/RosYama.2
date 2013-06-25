<?php

class m130620_205102_blank extends CDbMigration
{
    private $stack;

    public function safeUp()
    {
        $this->stack = new SplStack();
        $this->createTable(
            '{{comments}}',
            array(
                'owner_name' => 'varchar(50)NOT NULL',
                'owner_id' => 'int(11) NOT NULL',
                'comment_id' => 'pk',
                'parent_comment_id' => 'integer',
                'creator_id' => 'int(11)',
                'user_name' => 'varchar(128)',
                'user_email' => 'varchar(128)',
                'comment_text' => 'text',
                'create_time' => 'integer',
                'update_time' => 'integer',
                'status' => 'int(1) NOT NULL DEFAULT 0',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createIndex('owner_name', '{{comments}}', 'owner_name,owner_id');
        $this->createTable(
            '{{gibdd_areas}}',
            array(
                'id' => 'pk',
                'gibdd_id' => 'integer NOT NULL'
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{gibdd_area_points}}',
            array(
                'area_id' => 'integer NOT null',
                'point_num' => 'integer NOT null',
                'lat' => 'double(14,11) NOT null',
                'lng' => 'double(14,11) NOT null',
                'PRIMARY KEY (area_id,point_num)'
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{gibdd_heads}}',
            array(
                'id' => 'pk',
                'name' => 'text NOT null',
                'subject_id' => 'integer NOT null',
                'is_regional' => 'integer NOT null DEFAULT 0',
                'level' => 'tinyint(4) NOT null DEFAULT 2',
                'moderated' => 'integer NOT null DEFAULT 0',
                'post' => 'string NOT null',
                'post_dative' => 'string NOT null',
                'fio' => 'string NOT null',
                'fio_dative' => 'string NOT null',
                'gibdd_name' => 'string NOT null',
                'contacts' => 'text NOT null',
                'address' => 'text NOT null',
                'tel_degurn' => 'string NOT null',
                'tel_dover' => 'string NOT null',
                'tel_chancery' => 'string NOT null',
                'url' => 'string NOT null',
                'url_priemnaya' => 'string NOT null',
                'lat' => 'double(14,11) NOT null DEFAULT 0',
                'lng' => 'double(14,11) NOT null DEFAULT 0',
                'created' => 'int(10) NOT null',
                'modified' => 'int(10) NOT null',
                'author_id' => 'integer NOT null DEFAULT 1',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{gibdd_heads_buffer}}',
            array(
                'id' => 'pk',
                'name' => 'text NOT null',
                'subject_id' => 'integer NOT null',
                'is_regional' => 'integer NOT null DEFAULT 0',
                'level' => 'tinyint(4) NOT null DEFAULT 2',
                'moderated' => 'integer NOT null DEFAULT 0',
                'post' => 'string NOT null',
                'post_dative' => 'string NOT null',
                'fio' => 'string NOT null',
                'fio_dative' => 'string NOT null',
                'gibdd_name' => 'string NOT null',
                'contacts' => 'text NOT null',
                'address' => 'text NOT null',
                'tel_degurn' => 'string NOT null',
                'tel_dover' => 'string NOT null',
                'tel_chancery' => 'string NOT null',
                'url' => 'string NOT null',
                'url_priemnaya' => 'string NOT null',
                'lat' => 'double(14, 11) NOT null DEFAULT 0',
                'lng' => 'double(14, 11) NOT null DEFAULT 0',
                'created' => 'int(10) NOT null',
                'modified' => 'int(10) NOT null',
                'author_id' => 'integer NOT null DEFAULT 1',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{globals}}',
            array(
                'id' => 'pk',
                'var' => 'string NOT null',
                'name' => 'string NOT null',
                'value' => 'string NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{holes}}',
            array(
                'ID' => 'pk',
                'USER_ID' => 'integer unsigned NOT null',
                'LATITUDE' => 'double(13, 11) NOT null',
                'LONGITUDE' => 'double(14, 11) NOT null',
                'ADDRESS' => 'text NOT null',
                'STATE' => 'enum("fresh", "inprogress", "fixed", "achtung", "prosecutor", "gibddre") NOT null DEFAULT "fresh"',
                'DATE_CREATED' => 'integer unsigned NOT null',
                'DATE_SENT' => 'integer unsigned DEFAULT null',
                'DATE_STATUS' => 'integer unsigned DEFAULT null',
                'COMMENT1' => 'text',
                'description_size' => 'text NOT null',
                'description_locality' => 'text NOT null',
                'COMMENT2' => 'text',
                'TYPE_ID' => 'integer NOT null',
                'ADR_SUBJECTRF' => 'integer unsigned DEFAULT null',
                'gibdd_id' => 'integer NOT null',
                'ADR_CITY' => 'varchar(50) DEFAULT null',
                'COMMENT_GIBDD_REPLY' => 'text',
                'GIBDD_REPLY_RECEIVED' => 'tinyint(1) DEFAULT 0',
                'PREMODERATED' => 'tinyint(1) DEFAULT 0',
                'premoderator_id' => 'integer NOT null DEFAULT 0',
                'archive' => 'tinyint(1) NOT null DEFAULT 0',
                'DATE_SENT_PROSECUTOR' => 'integer unsigned DEFAULT null',
                'deleted' => 'tinyint(1) NOT null DEFAULT 0',
                'deletor_id' => 'integer NOT null DEFAULT 0',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{hole_answers}}',
            array(
                'id' => 'pk',
                'request_id' => 'integer NOT null',
                'date' => 'integer NOT null',
                'comment' => 'text NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{hole_answer_files}}',
            array(
                'id' => 'pk',
                'answer_id' => 'integer NOT null',
                'file_name' => 'varchar(511) NOT null',
                'file_type' => 'varchar(63) NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{hole_answer_results}}',
            array(
                'id' => 'pk',
                'name' => 'string NOT null',
                'published' => 'tinyint(1) NOT null DEFAULT 1',
                'ordering' => 'integer NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{hole_answer_results_xref}}',
            array(
                'answer_id' => 'integer NOT null',
                'result_id' => 'integer NOT null',
                'PRIMARY KEY(answer_id, result_id)'
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{hole_archive_filters}}',
            array(
                'id' => 'pk',
                'name' => 'string NOT null',
                'type_id' => 'integer NOT null DEFAULT 0',
                'status' => 'varchar(30) NOT null',
                'time_to' => 'integer NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{hole_cron_log}}',
            array(
                'id' => 'pk',
                'type' => 'string NOT null',
                'time_finish' => 'integer NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{hole_fixeds}}',
            array(
                'hole_id' => 'integer NOT null',
                'user_id' => 'integer NOT null',
                'date_fix' => 'integer NOT null',
                'comment' => 'text NOT null',
                'PRIMARY KEY(hole_id, user_id)'
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{hole_pictures}}',
            array(
                'id' => 'pk',
                'hole_id' => 'integer NOT null',
                'type' => 'varchar(63) NOT null',
                'user_id' => 'integer NOT null DEFAULT 0',
                'filename' => 'string NOT null',
                'ordering' => 'integer NOT null',
                'premoderated' => 'tinyint(1) NOT null DEFAULT 1',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{hole_requests}}',
            array(
                'id' => 'pk',
                'hole_id' => 'integer NOT null',
                'user_id' => 'integer NOT null',
                'gibdd_id' => 'integer NOT null',
                'date_sent' => 'integer NOT null',
                'response_requestid' => 'integer NOT null DEFAULT 0',
                'type' => 'varchar(30) NOT null',
                'notification_sended' => 'integer NOT null DEFAULT 0',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{hole_types}}',
            array(
                'id' => 'pk',
                'alias' => 'string NOT null',
                'name' => 'string NOT null',
                'pdf_body' => 'text NOT null',
                'pdf_footer' => 'text NOT null',
                'dorogimos_id' => 'string NOT null',
                'published' => 'tinyint(1) NOT null',
                'ordering' => 'integer NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{hole_type_pdf_list_commands}}',
            array(
                'id' => 'pk',
                'hole_type_id' => 'integer NOT null',
                'text' => 'text NOT null',
                'ordering' => 'integer NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{mainmenu}}',
            array(
                'id' => 'pk',
                'lft' => 'integer NOT null',
                'rgt' => 'integer NOT null',
                'level' => 'integer NOT null',
                'name' => 'string NOT null DEFAULT ""',
                'type' => 'integer NOT null DEFAULT 0',
                'link' => 'string NOT null',
                'controller' => 'string DEFAULT null',
                'action' => 'string DEFAULT null',
                'element' => 'string DEFAULT null',
                'elementmodel' => 'string NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createIndex('lft', '{{mainmenu}}', 'lft');
        $this->createIndex('rgt', '{{mainmenu}}', 'rgt');
        $this->createIndex('level', '{{mainmenu}}', 'level');
        $this->createIndex('name', '{{mainmenu}}', 'name');
        $this->createTable(
            '{{news}}',
            array(
                'id' => 'pk',
                'date' => 'integer NOT null',
                'picture' => 'string NOT null',
                'title' => 'string NOT null',
                'introtext' => 'text NOT null',
                'fulltext' => 'mediumtext NOT null',
                'published' => 'tinyint(1) NOT null DEFAULT 1',
                'archive' => 'tinyint(1) NOT null DEFAULT 0',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{prosecutors}}',
            array(
                'id' => 'pk',
                'name' => 'string NOT null',
                'subject_id' => 'integer NOT null',
                'preview_text' => 'text NOT null',
                'tel_chancery' => 'string NOT null',
                'gibdd_name' => 'string NOT null',
                'url_priemnaya' => 'string NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{prosecutors_buffer}}',
            array(
                'id' => 'pk',
                'name' => 'string NOT null',
                'subject_id' => 'integer NOT null',
                'preview_text' => 'text NOT null',
                'tel_chancery' => 'string NOT null',
                'gibdd_name' => 'string NOT null',
                'url_priemnaya' => 'string NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{rf_subjects}}',
            array(
                'id' => 'pk',
                'name' => 'string NOT null',
                'name_full' => 'string NOT null',
                'name_full_genitive' => 'string NOT null',
                'region_num' => 'integer NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{usergroups_access}}',
            array(
                'id' => 'bigint NOT null AUTO_INCREMENT PRIMARY KEY',
                'element' => 'int(3) NOT null',
                'element_id' => 'bigint NOT null',
                'module' => 'varchar(140) NOT null',
                'controller' => 'varchar(140) NOT null',
                'permission' => 'varchar(7) NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{usergroups_configuration}}',
            array(
                'id' => 'bigint NOT null AUTO_INCREMENT PRIMARY KEY',
                'rule' => 'varchar(40) DEFAULT null',
                'value' => 'varchar(20) DEFAULT null',
                'options' => 'text',
                'description' => 'text',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{usergroups_cron}}',
            array(
                'id' => 'bigint NOT null AUTO_INCREMENT PRIMARY KEY',
                'name' => 'varchar(40) DEFAULT null',
                'lapse' => 'int(6) DEFAULT null',
                'last_occurrence' => 'datetime DEFAULT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{usergroups_group}}',
            array(
                'id' => 'bigint NOT null AUTO_INCREMENT PRIMARY KEY',
                'groupname' => 'varchar(120) NOT null unique',
                'level' => 'int(6) DEFAULT null',
                'home' => 'varchar(120) DEFAULT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{usergroups_lookup}}',
            array(
                'id' => 'bigint NOT null AUTO_INCREMENT PRIMARY KEY',
                'element' => 'varchar(20) DEFAULT null',
                'value' => 'int(5) DEFAULT null',
                'text' => 'varchar(40) DEFAULT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{usergroups_social_services}}',
            array(
                'id' => 'pk',
                'name' => 'string NOT null',
                'service_name' => 'string NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{usergroups_user}}',
            array(
                'id' => 'bigint NOT null AUTO_INCREMENT PRIMARY KEY',
                'group_id' => 'bigint DEFAULT null',
                'username' => 'varchar(120) NOT null UNIQUE',
                'password' => 'varchar(120) DEFAULT null',
                'is_bitrix_pass' => 'tinyint(1) NOT null',
                'email' => 'varchar(120) NOT null',
                'name' => 'string NOT null',
                'second_name' => 'string NOT null',
                'last_name' => 'string NOT null',
                'home' => 'varchar(120) DEFAULT null',
                'status' => 'tinyint(1) NOT null DEFAULT 1',
                'question' => 'text',
                'answer' => 'text',
                'creation_date' => 'datetime DEFAULT null',
                'activation_code' => 'varchar(30) DEFAULT null',
                'activation_time' => 'datetime DEFAULT null',
                'last_login' => 'datetime DEFAULT null',
                'ban' => 'datetime DEFAULT null',
                'ban_reason' => 'text',
                'params' => 'text NOT null',
                'xml_id' => 'string NOT null',
                'external_auth_id' => 'string NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createIndex('group_id_idxfk', '{{usergroups_user}}', 'group_id');
        $this->createTable(
            '{{usergroups_user_profile}}',
            array(
                'id' => 'pk',
                'ug_id' => 'bigint DEFAULT null',
                'avatar' => 'varchar(120) DEFAULT null',
                'birthday' => 'date NOT null DEFAULT "0000-00-00"',
                'site' => 'string NOT null',
                'aboutme' => 'text NOT null',
                'request_from' => 'string NOT null',
                'request_signature' => 'varchar(127) NOT null',
                'request_address' => 'string NOT null',
                'request_phone' => 'varchar(63) NOT null',
                'show_archive_holes' => 'tinyint(1) NOT null DEFAULT 1',
                'send_achtung_notifications' => 'tinyint(1) NOT null DEFAULT 1',
                'use_multi_upload' => 'tinyint(1) NOT null DEFAULT 0',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{usergroups_user_social_accounts}}',
            array(
                'ug_id' => 'integer NOT null',
                'service_id' => 'integer NOT null',
                'xml_id' => 'string NOT null',
                'external_auth_id' => 'string NOT null',
                'PRIMARY KEY(ug_id, service_id)'
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{user_area_shapes}}',
            array(
                'id' => 'pk',
                'ug_id' => 'integer NOT null',
                'ordering' => 'integer NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{user_area_shape_points}}',
            array(
                'id' => 'pk',
                'shape_id' => 'integer NOT null',
                'point_num' => 'integer NOT null',
                'lat' => 'double(14, 11) NOT null',
                'lng' => 'double(14, 11) NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{user_selected_lists}}',
            array(
                'id' => 'pk',
                'user_id' => 'integer NOT null',
                'gibdd_id' => 'integer NOT null',
                'date_created' => 'integer NOT null',
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        $this->createTable(
            '{{user_selected_lists_holes_xref}}',
            array(
                'list_id' => 'integer NOT null',
                'hole_id' => 'integer NOT null',
                'PRIMARY KEY(list_id, hole_id)'
            ),
            'ENGINE=InnoDB CHARSET=utf8'
        );
        foreach (require(__DIR__ . '/m130620_205102_blank/gibdd_heads.php') as $item) {
            $this->insert('{{gibdd_heads}}', $item);
        }
        foreach (require(__DIR__ . '/m130620_205102_blank/hole_types.php') as $item) {
            $this->insert('{{hole_types}}', $item);
        }
        foreach (require(__DIR__ . '/m130620_205102_blank/hole_type_pdf_list_commands.php') as $item) {
            $this->insert('{{hole_type_pdf_list_commands}}', $item);
        }
        foreach (require(__DIR__ . '/m130620_205102_blank/mainmenu.php') as $item) {
            $this->insert('{{mainmenu}}', $item);
        }
        foreach (require(__DIR__ . '/m130620_205102_blank/prosecutors.php') as $item) {
            $this->insert('{{prosecutors}}', $item);
        }
        foreach (require(__DIR__ . '/m130620_205102_blank/rf_subjects.php') as $item) {
            $this->insert('{{rf_subjects}}', $item);
        }
        foreach (require(__DIR__ . '/m130620_205102_blank/usergroups_configuration.php') as $item) {
            $this->insert('{{usergroups_configuration}}', $item);
        }
        foreach (require(__DIR__ . '/m130620_205102_blank/usergroups_cron.php') as $item) {
            $this->insert('{{usergroups_cron}}', $item);
        }
        foreach (require(__DIR__ . '/m130620_205102_blank/usergroups_group.php') as $item) {
            $this->insert('{{usergroups_group}}', $item);
        }
        foreach (require(__DIR__ . '/m130620_205102_blank/usergroups_lookup.php') as $item) {
            $this->insert('{{usergroups_lookup}}', $item);
        }
        foreach (require(__DIR__ . '/m130620_205102_blank/usergroups_social_services.php') as $item) {
            $this->insert('{{usergroups_social_services}}', $item);
        }
    }

    public function safeDown()
    {
        $this->dropTable('{{user_selected_lists_holes_xref}}');
        $this->dropTable('{{user_selected_lists}}');
        $this->dropTable('{{user_area_shape_points}}');
        $this->dropTable('{{user_area_shapes}}');
        $this->dropTable('{{usergroups_user_social_accounts}}');
        $this->dropTable('{{usergroups_user_profile}}');
        $this->dropIndex('group_id_idxfk', '{{usergroups_user}}');
        $this->dropTable('{{usergroups_user}}');
        $this->dropTable('{{usergroups_social_services}}');
        $this->dropTable('{{usergroups_lookup}}');
        $this->dropTable('{{usergroups_group}}');
        $this->dropTable('{{usergroups_cron}}');
        $this->dropTable('{{usergroups_configuration}}');
        $this->dropTable('{{usergroups_access}}');
        $this->dropTable('{{rf_subjects}}');
        $this->dropTable('{{prosecutors_buffer}}');
        $this->dropTable('{{prosecutors}}');
        $this->dropTable('{{news}}');
        $this->dropIndex('name', '{{mainmenu}}');
        $this->dropIndex('level', '{{mainmenu}}');
        $this->dropIndex('rgt', '{{mainmenu}}');
        $this->dropIndex('lft', '{{mainmenu}}');
        $this->dropTable('{{mainmenu}}');
        $this->dropTable('{{hole_type_pdf_list_commands}}');
        $this->dropTable('{{hole_types}}');
        $this->dropTable('{{hole_requests}}');
        $this->dropTable('{{hole_pictures}}');
        $this->dropTable('{{hole_fixeds}}');
        $this->dropTable('{{hole_cron_log}}');
        $this->dropTable('{{hole_archive_filters}}');
        $this->dropTable('{{hole_answer_results_xref}}');
        $this->dropTable('{{hole_answer_results}}');
        $this->dropTable('{{hole_answer_files}}');
        $this->dropTable('{{hole_answers}}');
        $this->dropTable('{{holes}}');
        $this->dropTable('{{globals}}');
        $this->dropTable('{{gibdd_heads_buffer}}');
        $this->dropTable('{{gibdd_heads}}');
        $this->dropTable('{{gibdd_area_points}}');
        $this->dropTable('{{gibdd_areas}}');
        $this->dropIndex('owner_name', '{{comments}}');
        $this->dropTable('{{comments}}');
    }
}

?>
