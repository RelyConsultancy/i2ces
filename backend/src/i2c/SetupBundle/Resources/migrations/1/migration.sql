CREATE TABLE IF NOT EXISTS `evaluation` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  cid VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  category VARCHAR(255) NOT NULL,
  brand VARCHAR(255) NOT NULL,
  state VARCHAR(255) NOT NULL,
  start_date DATETIME,
  end_date DATETIME,
  generated_at DATETIME,
  business_unit_id INT(11),
  FOREIGN KEY (business_unit_id) REFERENCES oro_business_unit (id),
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS `chapter` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(255),
  state VARCHAR(255),
  location VARCHAR(255),
  content BLOB,
  created_at DATETIME,
  last_modified_at DATETIME,
  chapter_order INT,
  is_additional_data TINYINT(1) DEFAULT 0,
  serialized_name VARCHAR(255),
  PRIMARY KEY (id)
);


CREATE TABLE IF NOT EXISTS `evaluation_chapters` (
  evaluation_id INT(11) NOT NULL,
  chapter_id INT(11) NOT NULL,
  FOREIGN KEY (evaluation_id) REFERENCES evaluation(id),
  FOREIGN KEY (chapter_id) REFERENCES chapter(id),
  PRIMARY KEY (evaluation_id, chapter_id)
);
