
  <div>

    <h2>Index</h2>

    <p>
    These controllers show some of rapyd functionalities.<br />
    You need to build a test database to run it .<br />
    
    <br />
    You must also edit <strong>application/config/database.php</strong> and edit your connection params (host,dbname,password etc..)    <br />
    
    <br />
    IMPORTANT NOTE:<br/>
    in this version there are new tables added to support rapyd authorization class.<br />
    However rapyd_auth is an "optional" class.<br/>
    If you already use "auth", "sentry", "freakauth" or other contributed auth libs for CI, you can simply "jump" rapyd_auth tables.
    </p>
    
    <div class="code">
    <pre>
    
    /* required by rapyd auth */
    
    CREATE TABLE `users` (
      `user_id` int(9) NOT NULL auto_increment,
      `role_id` varchar(50) NOT NULL default '0',
      `act_key` varchar(255) NOT NULL default '',
      `act_ip` varchar(255) NOT NULL default '',
      `user_name` varchar(50) NOT NULL default '',
      `password` varchar(50) NOT NULL default '',
      `email` varchar(150) NOT NULL default '',
      `name` varchar(50) default NULL,
      `lastname` varchar(100) default NULL,
      `active` enum('y','n') default 'n',
      `lastlogin` datetime default '0000-00-00 00:00:00', 
      PRIMARY KEY  (`user_id`),
      UNIQUE KEY `user_name` (`user_name`),
      UNIQUE KEY `email` (`email`),
      KEY `users_FI_1` (`role_id`)
    );
    INSERT INTO `users` (`user_id`,`role_id`,`act_key`,`act_ip`,`user_name`,`password`,
                        `email`,`name`,`lastname`,`active`,`lastlogin`) 
        VALUES (1,'3','','','test','a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',
                'email@email.com','name','lastname','y', '0000-00-00 00:00:00');
    

    CREATE TABLE `security_role` (
      `role_id` int(11) NOT NULL auto_increment,
      `name` varchar(45) NOT NULL default '',
      PRIMARY KEY  (`role_id`)
    );
    INSERT INTO `security_role` (`role_id`,`name`) VALUES (1,'root');
    INSERT INTO `security_role` (`role_id`,`name`) VALUES (2,'admin');
    INSERT INTO `security_role` (`role_id`,`name`) VALUES (3,'operator');
    INSERT INTO `security_role` (`role_id`,`name`) VALUES (4,'guest');


    CREATE TABLE `security_permission` (
      `permission_id` int(11) NOT NULL auto_increment,
      `name` varchar(45) NOT NULL default '',
      PRIMARY KEY  (`permission_id`)
    ) ;
    INSERT INTO `security_permission` (`permission_id`,`name`)
         VALUES (1,'*');

    CREATE TABLE `security_role_permission` (
      `entry_id` int(11) NOT NULL auto_increment,
      `role_id` int(11) NOT NULL default '0',
      `permission_id` int(11) NOT NULL default '0',
      `allow_deny` tinyint(1) default '1',
      PRIMARY KEY  (`entry_id`),
      KEY `security_role_permission_FI_1` (`role_id`),
      KEY `security_role_permission_FI_2` (`permission_id`)
    );
    INSERT INTO `security_role_permission` (`entry_id`,`role_id`,`permission_id`,`allow_deny`) 
        VALUES (1,1,1,1);

    CREATE TABLE `security_user_permission` (
      `entry_id` int(11) NOT NULL auto_increment,
      `user_id` int(11) NOT NULL default '0',
      `permission_id` int(11) NOT NULL default '0',
      `allow_deny` tinyint(1) NOT NULL default '1',
      PRIMARY KEY  (`entry_id`),
      KEY `security_user_permission_FI_1` (`user_id`),
      KEY `security_user_permission_FI_2` (`permission_id`)
    );

    
    
    
    /* needed only by samples */
    
  CREATE TABLE articles (
    article_id int(9) unsigned NOT NULL auto_increment,
    author_id int(11) default NULL,
    title varchar(100) NOT NULL default '',
    body text,
    datefield datetime default '0000-00-00 00:00:00',
    public enum('y','n') default NULL,
    PRIMARY KEY  (article_id)
  );
  INSERT INTO articles VALUES("1", "1", "Title 1", "Body 1", NULL, NULL);
  INSERT INTO articles VALUES("2", "2", "Title 2", "Body 2", NULL, NULL);
  INSERT INTO articles VALUES("3", "1", "Title 3", "Body 3", NULL, NULL);
  INSERT INTO articles VALUES("4", "2", "Title 4", "Body 4", NULL, NULL);
  INSERT INTO articles VALUES("5", "1", "Title 5", "Body 5", NULL, NULL);
  INSERT INTO articles VALUES("6", "2", "Title 6", "Body 6", NULL, NULL);
  INSERT INTO articles VALUES("7", "1", "Title 7", "Body 7", NULL, NULL);
  INSERT INTO articles VALUES("8", "2", "Title 8", "Body 8", NULL, NULL);
  INSERT INTO articles VALUES("9", "1", "Title 9", "Body 9", NULL, NULL);
  INSERT INTO articles VALUES("10", "2", "Title 10", "Body 10", NULL, NULL);


  CREATE TABLE authors (
    author_id int(11) NOT NULL auto_increment,
    firstname varchar(25) NOT NULL default '',
    lastname varchar(25) NOT NULL default '',
    PRIMARY KEY  (author_id)
  );
  INSERT INTO authors VALUES("1", "Jhon", "Doe");
  INSERT INTO authors VALUES("2", "Rocco", "Siffredi");


  CREATE TABLE comments (
    comment_id int(9) NOT NULL auto_increment,
    article_id int(9) NOT NULL default '0',
    comment text,
    PRIMARY KEY  (`comment_id`)
  );
  
  
  CREATE TABLE articles_related (
    `art_id` int(9) unsigned NOT NULL default '0',
    `rel_id` int(9) unsigned NOT NULL default '0',
    PRIMARY KEY  (`art_id`,`rel_id`)
  );
  INSERT INTO articles_related VALUES("1", "2");
  INSERT INTO articles_related VALUES("2", "1");
  
  
  
    </pre>
    </div>
  
    <h3>Author &amp; License</h3>
    <p>
    Author: <a href="http://www.feliceostuni.com">Felice Ostuni</a> aka Felix on CI forum/wiki<br />
    Rapyd is Open Source (LGPL) for more info: <a href="http://www.rapyd.com">www.rapyd.com</a><br />
    </p>

    
    


       
  </div>
