CREATE TABLE IF NOT EXISTS `evaluation` (
  id               INT(11)      NOT NULL AUTO_INCREMENT,
  cid              VARCHAR(255) NOT NULL,
  title            VARCHAR(255) NOT NULL,
  category         VARCHAR(255) NOT NULL,
  brand            VARCHAR(255) NOT NULL,
  state            VARCHAR(255) NOT NULL,
  start_date       DATETIME,
  end_date         DATETIME,
  generated_at     DATETIME,
  business_unit_id INT(11),
  version_number   VARCHAR(255),
  FOREIGN KEY (business_unit_id) REFERENCES oro_business_unit (id),
  PRIMARY KEY (id)
)
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `chapter` (
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


CREATE TABLE IF NOT EXISTS `evaluation_chapters` (
  evaluation_id INT(11) NOT NULL,
  chapter_id    INT(11) NOT NULL,
  FOREIGN KEY (evaluation_id) REFERENCES evaluation (id),
  FOREIGN KEY (chapter_id) REFERENCES chapter (id),
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


CREATE TABLE IF NOT EXISTS `i2c_pages` (
  type VARCHAR(255) NOT NULL,
  title   VARCHAR(255) NOT NULL,
  content   BLOB,
  PRIMARY KEY (type)
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

INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Instore Sampling', 'sampling');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('JS Magazine', 'magazine');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Six Sheets', '6_sheet');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Entrance Gates', 'entrance_gate');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Milk Media', 'milk_media');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Trolleys', 'trolleys');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('TV Wall', 'tv_wall');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Aisle Fins', 'aisle_fin');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Take Home Barker', 'barkers');
INSERT INTO `i2c_channel_icons` (`channel_name`, `icon_name`) VALUES ('Car Park Posters', '6_sheet');


INSERT INTO `i2c_pages` (`type`, `title`, `content`) VALUES ('faq', 'F.A.Q.', 'FAQ page content');


INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('New_custs', 'customer');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('New_trialists', 'customer');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('Units', 'units');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('SPEC', 'GBP');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('FOP', 'percentage');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('Existing_custs', 'customer');