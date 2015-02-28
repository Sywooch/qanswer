<?php

use yii\db\Schema;
use yii\db\Migration;

class m150213_071635_init extends Migration {

    public function up() 
    {
        $this->createTable('{{%user}}', [
            'id' => 'pk',
            'username' => 'VARCHAR(30) NOT NULL',
            'email' => 'VARCHAR(75) NOT NULL',
            'password1' => 'VARCHAR(128) NOT NULL',
            'password' => 'CHAR(32) NOT NULL',
            'active' => 'TINYINT(1) NOT NULL',
            'activekey' => 'VARCHAR(128) NOT NULL',
            'salt' => 'CHAR(6) NOT NULL',
            'groupid' => 'SMALLINT(6) NOT NULL DEFAULT \'1\'',
            'lastlogin' => 'INT(10) NOT NULL',
            'registertime' => 'INT(10) NOT NULL',
            'gold' => 'SMALLINT(6) UNSIGNED NOT NULL',
            'silver' => 'SMALLINT(6) UNSIGNED NOT NULL',
            'bronze' => 'SMALLINT(6) UNSIGNED NOT NULL',
            'reputation' => 'INT(10) UNSIGNED NULL DEFAULT \'1\'',
            'votedcount' => 'MEDIUMINT(8) NOT NULL',
            'editedcount' => 'MEDIUMINT(8) NOT NULL',
            'messagecount' => 'SMALLINT(6) UNSIGNED NOT NULL',
            'lastseen' => 'INT(10) NULL',
            'status' => 'TINYINT(1) NOT NULL',
            'setting' => 'TINYINT(8) NOT NULL DEFAULT \'3\'',
        ]);
        $this->createTable('{{%user_profile}}', [
            'id' => 'pk',
            'realname' => 'VARCHAR(20) NOT NULL',
            'birthday' => 'DATE NOT NULL',
            'location' => 'VARCHAR(250) NOT NULL',
            'website' => 'VARCHAR(200) NULL',
            'aboutme' => 'TEXT NOT NULL',
            'complete' => 'TINYINT(1) NOT NULL',
            'preference' => 'TEXT NOT NULL',
            'unpreference' => 'TEXT NOT NULL',
        ]);

        $this->createTable('{{%user_tags}}', [
            'uid' => 'INT(11) NOT NULL',
            'tag' => 'VARCHAR(20) NOT NULL',
            'totalcount' => 'SMALLINT(6) NOT NULL',
            'unwikicount' => 'SMALLINT(6) NOT NULL',
        ]);
        $this->createIndex('idx_uid_tag', '{{%user_tags}}', ['uid','tag']);

        $this->createTable('{{%user_stat}}', [
            'id' => 'INT(11) NOT NULL',
            'qcount' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'acount' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'viewcount' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'upvotecount' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'downvotecount' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'commentcount' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'editcount' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'weekvotes' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'monthvotes' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'quartervotes' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'yearvotes' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'weekedits' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'monthedits' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'quarteredits' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'yearedits' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'weekreps' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'monthreps' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'quarterreps' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'yearreps' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
        ]);
        $this->createIndex('idx_id', '{{%user_stat}}', 'id',true);


        $this->createTable('{{%user_limits}}', [
            'uid' => 'INT(10) UNSIGNED NOT NULL',
            'action' => 'CHAR(1) NOT NULL',
            'period' => 'INT(10) UNSIGNED NOT NULL',
            'count' => 'SMALLINT(5) UNSIGNED NOT NULL',
        ]);
        $this->createIndex('idx_uid_action', '{{%user_limits}}', ['uid','action']);

        //post,post_mod,post_state,questiontag,revision,vote        

        $this->createTable('{{%post}}', [
            'id' => 'pk',
            'idv' => 'INT(11) NOT NULL',
            'idtype' => 'ENUM(\'question\',\'answer\',\'tag\') NOT NULL DEFAULT \'question\'',
            'revisionid' => 'INT(11) NOT NULL',
            'uid' => 'INT(11) NOT NULL',
            'createtime' => 'INT(10) NOT NULL',
            'activity' => 'INT(10) NOT NULL',
            'lastedit' => 'INT(10) NOT NULL',
            'status' => 'TINYINT(1) NOT NULL',
            'accepted' => 'TINYINT(1) NOT NULL',
            'score' => 'MEDIUMINT(8) NOT NULL',
            'answercount' => 'INT(10) UNSIGNED NOT NULL',
            'commentcount' => 'INT(10) UNSIGNED NOT NULL',
            'viewcount' => 'INT(11) NOT NULL',
            'favcount' => 'SMALLINT(6) UNSIGNED NOT NULL',
            'flagcount' => 'SMALLINT(6) NOT NULL',
            'title' => 'VARCHAR(128) NULL',
            'content' => 'TEXT NOT NULL',
            'excerpt' => 'VARCHAR(255) NOT NULL',
            'tags' => 'VARCHAR(150) NOT NULL',
            'useful' => 'INT(10) NOT NULL',
            'useless' => 'INT(10) NOT NULL',
            'aupvotes' => 'MEDIUMINT(8) NOT NULL',
            'wiki' => 'TINYINT(1) NOT NULL',
        ]);

        $this->createTable('{{%post_mod}}', [
            'id' => 'pk',
            'uid' => 'INT(11) NOT NULL',
            'postid' => 'INT(11) NOT NULL',
            'type' => 'SMALLINT(6) NOT NULL',
            'typeid' => 'SMALLINT(6) NOT NULL',
            'oid' => 'INT(11) NOT NULL',
            'time' => 'INT(10) UNSIGNED NOT NULL',
        ]);

        $this->createTable('{{%post_state}}', [
            'id' => 'pk',
            'lockuid' => 'INT(11) NOT NULL',
            'locktime' => 'INT(10) NOT NULL',
            'protect' => 'TINYINT(1) NOT NULL',
            'protectuid' => 'INT(11) NOT NULL',
            'protecttime' => 'INT(10) NOT NULL',
            'close' => 'TINYINT(1) NOT NULL',
            'closecount' => 'SMALLINT(6) NOT NULL',
            'closereason' => 'SMALLINT(6) NOT NULL',
            'closetime' => 'INT(10) NOT NULL',
            'delete' => 'TINYINT(1) NOT NULL',
            'deletecount' => 'SMALLINT(6) NOT NULL',
            'deletetime' => 'INT(10) NOT NULL',
        ]);

        $this->createTable('{{%questiontag}}', [
            'postid' => 'INT(11) NOT NULL',
            'tag' => 'VARCHAR(128) NOT NULL',
            'time' => 'INT(10) NOT NULL',
        ]);
        $this->createIndex('idx_postid_tag', '{{%questiontag}}', ['postid','tag']);

        $this->createTable('{{%revision}}', [
            'id' => 'pk',
            'title' => 'VARCHAR(125) NOT NULL',
            'uid' => 'INT(11) NOT NULL',
            'revtime' => 'INT(10) NOT NULL',
            'tags' => 'VARCHAR(125) NOT NULL',
            'comment' => 'TINYTEXT NOT NULL',
            'text' => 'TEXT NOT NULL',
            'status' => 'TINYINT(1) NOT NULL',
        ]);

        $this->createTable('{{%vote}}', [
            'id' => 'pk',
            'postid' => 'INT(11) NOT NULL',
            'uid' => 'INT(11) NOT NULL',
            'useful' => 'TINYINT(2) NOT NULL',
            'time' => 'INT(10) UNSIGNED NOT NULL',
            'fav' => 'TINYINT(2) NOT NULL',
            'favtime' => 'INT(10) NOT NULL',
        ]);


//activity,adv,award,badge,bounty,cache,comment,comment_vote,draft,flag,inbox,mail_queue,notify,oauth_login,options,report,repute,tag


        $this->createTable('{{%activity}}', [
            'id' => 'pk',
            'uid' => 'INT(11) NOT NULL',
            'type' => 'ENUM(\'comment\',\'ask\',\'answer\',\'posts\',\'voteup\',\'votedown\',\'revise\',\'fav\',\'award\',\'accept\') NOT NULL',
            'typeid' => 'INT(11) NOT NULL',
            'data' => 'TEXT NULL',
            'time' => 'INT(10) NOT NULL',
        ]);

        $this->createTable('{{%adv}}', [
            'id' => 'pk',
            'available' => 'TINYINT(1) NOT NULL',
            'position' => 'VARCHAR(50) NOT NULL',
            'displayorder' => 'TINYINT(3) NOT NULL',
            'title' => 'VARCHAR(255) NOT NULL',
            'code' => 'TEXT NOT NULL',
            'starttime' => 'INT(10) UNSIGNED NOT NULL',
            'endtime' => 'INT(10) UNSIGNED NOT NULL',
        ]);

        $this->createTable('{{%award}}', [
            'id' => 'pk',
            'badgeid' => 'INT(11) NOT NULL',
            'time' => 'INT(10) NOT NULL',
            'idtype' => 'VARCHAR(20) NOT NULL DEFAULT \'question\'',
            'idv' => 'INT(11) NOT NULL',
        ]);

        $this->createTable('{{%badge}}', [
            'id' => 'pk',
            'name' => 'VARCHAR(50) NOT NULL',
            'marker' => 'VARCHAR(20) NOT NULL',
            'type' => 'SMALLINT(6) NOT NULL',
            'description' => 'TEXT NOT NULL',
            'multiple' => 'TINYINT(4) NOT NULL',
            'awardcount' => 'INT(10) UNSIGNED NOT NULL',
        ]);

        $this->createTable('{{%bounty}}', [
            'id' => 'pk',
            'uid' => 'INT(11) NOT NULL',
            'questionid' => 'INT(11) NOT NULL',
            'amount' => 'SMALLINT(6) UNSIGNED NOT NULL DEFAULT \'50\'',
            'status' => 'TINYINT(1) NOT NULL',
            'time' => 'INT(10) NOT NULL',
            'endtime' => 'INT(10) UNSIGNED NOT NULL',
            'touid' => 'INT(11) NOT NULL',
            'totime' => 'INT(10) NOT NULL',
            'answerid' => 'INT(11) NOT NULL',
            'bonus' => 'SMALLINT(6) UNSIGNED NOT NULL',
        ]);

//        $this->createTable('{{%cache}}', [
//            'id' => 'CHAR(128) NOT NULL',
//            0 => 'PRIMARY KEY (`id`)',
//            'expire' => 'INT(11) NULL',
//            'value' => 'LONGBLOB NULL',
//        ]);

        $this->createTable('{{%comment}}', [
            'id' => 'pk',
            'idtype' => 'VARCHAR(8) NOT NULL DEFAULT \'question\'',
            'idv' => 'INT(11) UNSIGNED NOT NULL',
            'uid' => 'INT(11) NOT NULL',
            'message' => 'TEXT NOT NULL',
            'time' => 'INT(10) NOT NULL',
            'upvotes' => 'MEDIUMINT(8) UNSIGNED NOT NULL',
            'status' => 'TINYINT(1) NOT NULL DEFAULT \'1\'',
        ]);

        $this->createTable('{{%comment_vote}}', [
            'id' => 'pk',
            'commentid' => 'INT(11) NOT NULL',
            'voteTypeId' => 'TINYINT(3) NOT NULL',
            'time' => 'INT(10) NOT NULL',
            'uid' => 'INT(11) NOT NULL',
            'message' => 'TEXT NOT NULL',
        ]);

        $this->createTable('{{%draft}}', [
            'id' => 'pk',
            'uid' => 'INT(11) NOT NULL',
            'type' => 'CHAR(1) NOT NULL',
            'postid' => 'INT(11) UNSIGNED NOT NULL',
            'title' => 'VARCHAR(80) NOT NULL',
            'content' => 'TEXT NOT NULL',
            'tagnames' => 'VARCHAR(256) NOT NULL',
            'dateline' => 'INT(10) NOT NULL',
        ]);

        $this->createTable('{{%flag}}', [
            'id' => 'pk',
            'idtype' => 'ENUM(\'Q\',\'A\',\'C\') NOT NULL',
            'idval' => 'INT(10) UNSIGNED NOT NULL',
            'uid' => 'INT(11) NOT NULL',
            'status' => 'TINYINT(1) NOT NULL',
            'time' => 'INT(10) NOT NULL',
        ]);

        $this->createTable('{{%inbox}}', [
            'id' => 'pk',
            'title' => 'VARCHAR(100) NOT NULL',
            'summary' => 'TEXT NOT NULL',
            'url' => 'VARCHAR(200) NOT NULL',
            'type' => 'VARCHAR(10) NOT NULL',
            'isnew' => 'TINYINT(1) NOT NULL DEFAULT \'1\'',
            'uid' => 'INT(11) NOT NULL',
            'time' => 'INT(10) UNSIGNED NOT NULL',
        ]);

        $this->createTable('{{%mail_queue}}', [
            'id' => 'pk',
            'mailto' => 'VARCHAR(150) NOT NULL',
            'subject' => 'VARCHAR(255) NOT NULL',
            'body' => 'TEXT NOT NULL',
            'errnum' => 'TINYINT(1) UNSIGNED NOT NULL',
            'time' => 'INT(11) NOT NULL',
            'lockexpiry' => 'INT(11) NOT NULL',
        ]);

        $this->createTable('{{%notify}}', [
            'id' => 'pk',
            'uid' => 'INT(11) NOT NULL',
            'typeid' => 'SMALLINT(6) NOT NULL',
            'message' => 'TEXT NOT NULL',
            'new' => 'TINYINT(1) NOT NULL DEFAULT \'1\'',
            'time' => 'INT(10) NOT NULL',
        ]);

        $this->createTable('{{%oauth_login}}', [
            'id' => 'pk',
            'uid' => 'INT(11) NOT NULL',
            'type_uid' => 'VARCHAR(255) NOT NULL',
            'type' => 'CHAR(80) NOT NULL',
            'oauth_token' => 'VARCHAR(150) NULL',
            'oauth_token_secret' => 'VARCHAR(150) NULL',
        ]);

        $this->createTable('{{%options}}', [
            'name' => 'VARCHAR(30) NOT NULL',
            0 => 'PRIMARY KEY (`name`)',
            'value' => 'TEXT NOT NULL',
            'type' => 'TINYINT(1) NOT NULL',
        ]);
        $this->createIndex('idx_name', '{{%options}}', 'name', true);

        $this->createTable('{{%report}}', [
            'id' => 'pk',
            'uid' => 'INT(11) NOT NULL',
            'postid' => 'INT(11) NOT NULL',
            'qid' => 'INT(11) UNSIGNED NOT NULL',
            'message' => 'TEXT NOT NULL',
            'time' => 'INT(10) NOT NULL',
            'opuid' => 'INT(11) NOT NULL',
            'optime' => 'INT(10) UNSIGNED NOT NULL',
            'opresult' => 'VARCHAR(255) NOT NULL',
        ]);

        $this->createTable('{{%repute}}', [
            'id' => 'pk',
            'uid' => 'INT(11) NOT NULL',
            'postid' => 'INT(11) NOT NULL',
            'apostid' => 'INT(11) NOT NULL',
            'time' => 'INT(10) NOT NULL',
            'type' => 'SMALLINT(6) NOT NULL',
            'reputation' => 'INT(11) NOT NULL',
        ]);

        $this->createTable('{{%tag}}', [
            'id' => 'pk',
            'uid' => 'INT(11) NOT NULL',
            'name' => 'VARCHAR(255) NOT NULL',
            'postid' => 'INT(11) NOT NULL',
            'frequency' => 'INT(10) UNSIGNED NOT NULL',
        ]);
    }

    public function down() 
    {
        $this->dropTable('{{%tag}}');
        $this->dropTable('{{%repute}}');
        $this->dropTable('{{%report}}');
        $this->dropTable('{{%options}}');
        $this->dropTable('{{%oauth_login}}');
        $this->dropTable('{{%notify}}');
        $this->dropTable('{{%mail_queue}}');
        $this->dropTable('{{%inbox}}');
        $this->dropTable('{{%flag}}');
        $this->dropTable('{{%draft}}');
        $this->dropTable('{{%comment_vote}}');
        $this->dropTable('{{%comment}}');
        $this->dropTable('{{%bounty}}');
        $this->dropTable('{{%badge}}');
        $this->dropTable('{{%award}}');
        $this->dropTable('{{%adv}}');
        $this->dropTable('{{%activity}}');
        $this->dropTable('{{%revision}}');
        $this->dropTable('{{%questiontag}}');
        $this->dropTable('{{%post_state}}');
        $this->dropTable('{{%post_mod}}');
        $this->dropTable('{{%post}}');
        $this->dropTable('{{%user_limits}}');
        $this->dropTable('{{%user_stat}}');
        $this->dropTable('{{%user_tags}}');
        $this->dropTable('{{%user_profile}}');
        $this->dropTable('{{%user}}');
        
        return true;
    }

}
