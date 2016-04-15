CREATE TABLE IF NOT EXISTS `i2c_evaluation` (
  id               INT(11)      NOT NULL AUTO_INCREMENT,
  cid              VARCHAR(255) NOT NULL,
  title            VARCHAR(255) NOT NULL,
  category         VARCHAR(255) NOT NULL,
  brand            VARCHAR(255) NOT NULL,
  state            VARCHAR(255) NOT NULL,
  start_date       DATETIME,
  end_date         DATETIME,
  version_number   VARCHAR(255),
  latest_pdf_path  VARCHAR(255),
  generated_at     DATETIME,
  business_unit_id INT(11),
  FOREIGN KEY (business_unit_id) REFERENCES oro_business_unit (id),
  PRIMARY KEY (id)
)
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `i2c_chapter` (
  id                 INT(11) NOT NULL AUTO_INCREMENT,
  title              VARCHAR(255),
  state              VARCHAR(255),
  location           VARCHAR(255),
  content            BLOB,
  created_at         DATETIME,
  last_modified_at   DATETIME,
  chapter_order      INT,
  is_additional_data TINYINT(1)       DEFAULT 0,
  serialized_name    VARCHAR(255),
  PRIMARY KEY (id)
)
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `i2c_evaluation_chapters` (
  evaluation_id INT(11) NOT NULL,
  chapter_id    INT(11) NOT NULL,
  FOREIGN KEY (evaluation_id) REFERENCES i2c_evaluation (id),
  FOREIGN KEY (chapter_id) REFERENCES i2c_chapter (id),
  PRIMARY KEY (evaluation_id, chapter_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `i2c_reimported_evaluation` (
  id               INT(11)      NOT NULL AUTO_INCREMENT,
  cid              VARCHAR(255) NOT NULL,
  title            VARCHAR(255) NOT NULL,
  category         VARCHAR(255) NOT NULL,
  brand            VARCHAR(255) NOT NULL,
  state            VARCHAR(255) NOT NULL,
  start_date       DATETIME,
  end_date         DATETIME,
  version_number   VARCHAR(255),
  latest_pdf_path  VARCHAR(255),
  generated_at     DATETIME,
  business_unit_id INT(11),
  FOREIGN KEY (business_unit_id) REFERENCES oro_business_unit (id),
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `i2c_reimported_chapter` (
  id                 INT(11) NOT NULL AUTO_INCREMENT,
  title              VARCHAR(255),
  state              VARCHAR(255),
  location           VARCHAR(255),
  content            BLOB,
  created_at         DATETIME,
  last_modified_at   DATETIME,
  chapter_order      INT,
  is_additional_data TINYINT(1)       DEFAULT 0,
  serialized_name    VARCHAR(255),
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `i2c_reimported_evaluation_chapters` (
  evaluation_id INT(11) NOT NULL,
  chapter_id    INT(11) NOT NULL,
  FOREIGN KEY (evaluation_id) REFERENCES i2c_reimported_evaluation (id),
  FOREIGN KEY (chapter_id) REFERENCES i2c_reimported_chapter (id),
  PRIMARY KEY (evaluation_id, chapter_id)
)
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `i2c_chart_data_set` (
  id      INT(11) NOT NULL AUTO_INCREMENT,
  cid     VARCHAR(255),
  content BLOB,
  PRIMARY KEY (id)
)
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `i2c_images_queue` (
  chapter_id    INT(11)      NOT NULL,
  evaluation_id VARCHAR(255) NOT NULL,
  PRIMARY KEY (chapter_id, evaluation_id)
);

CREATE TABLE IF NOT EXISTS `i2c_objective_units` (
  metric VARCHAR(255) NOT NULL,
  unit   VARCHAR(255) NOT NULL,
  PRIMARY KEY (metric)
)
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `i2c_channel_icons` (
  channel_name             VARCHAR(255)      NOT NULL,
  icon_name VARCHAR(255) NOT NULL,
  PRIMARY KEY (channel_name)
)
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `i2c_pages` (
  type VARCHAR(255) NOT NULL,
  title   VARCHAR(255) NOT NULL,
  content   BLOB,
  PRIMARY KEY (type)
)
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Direct Mail', 'dm');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Secondary Space', '');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Nectar Competition Barker', 'barkers');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Yahoo', 'programmatic');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Programmatic', 'programmatic');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('In Store Broadcasting', 'in_store_tanoy');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Recipe Card Barker', 'barkers');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Email', 'email');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('New Information Barker', 'barkers');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Mobile', 'mobile');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Coupon At Till', 'coupon_at_till');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Bollards', 'entrance_gate');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Instore Sampling', 'sampling');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('JS Magazine', 'magazine');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Six Sheets', '6_sheet');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Entrance Gates', 'entrance_gate');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Milk Media', 'milk_media');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Trolleys', 'trolleys');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('TV Wall', 'tv_wall');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Aisle Fins', 'aisle_fin');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Take Home Barker', 'barkers');

INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('Existing_custs', 'customer');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('New_custs', 'customer');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('New_trialists', 'customer');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('Units', 'units');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('SPEC', 'GBP');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('FOP', 'percentage');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('Existing_custs', 'customer');



CREATE TABLE IF NOT EXISTS `i2c_generate_pdf_queue` (
  id             INT(11)      NOT NULL AUTO_INCREMENT,
  evaluation_cid VARCHAR(255) NOT NULL,
  published_time DATETIME     NOT NULL,
  PRIMARY KEY (id)
)
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

INSERT INTO `i2c_pages` (`type`, `title`, `content`) VALUES ('faq', 'F.A.Q.', 'FAQ page content');