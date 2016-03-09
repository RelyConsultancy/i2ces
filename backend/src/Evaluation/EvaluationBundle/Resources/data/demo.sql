/*
-- Query: SELECT * FROM bap_standard.chapter
LIMIT 0, 1000

-- Date: 2016-03-04 16:37
*/
INSERT INTO `chapter` (`id`,`uid`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`) VALUES (1,'56ce5bd2-49ed-4e91-b4f7-43b44d225edb','Grow Awareness','hidden','/api/evaluations/0f754e5c-6f77-4d55-a770-9d75d60c82e5/chapters/56ce5bd2-49ed-4e91-b4f7-43b44d225edb','[{"type":"section","title":"Period on period sales performance","content":[{"type":"barchart","source":"\/dataset\/123.cs","comment":"... long string of text ...","info":"... long string of text ..."}]},{"type":"section","title":"Promotional activity during campaign","content":[{"type":"text","content":"... long string of text ..."},{"type":"gallery","images":["\/path\/to\/imag2222e_1.jpg","\/path\/to\/image_2.jpg","\/path\/to\/image_3.jpg"]}]}]','2016-03-04 16:00:00','2016-03-04 13:34:31');
INSERT INTO `chapter` (`id`,`uid`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`) VALUES (2,'56ce5bd2-49ed-4e91-b4f7-43b44d225cdb','Campaign Background','visible','/api/evaluations/0f754e5c-6f77-4d55-a770-9d75d60c82e5/chapters/56ce5bd2-49ed-4e91-b4f7-43b44d225cdb','[{"type":"section","title":"Period on period sales performance","content":[{"type":"barchart","source":"/dataset/123.cs","comment":"... long string of text ...","info":"... long string of text ..."}]},{"type":"section","title":"Promotional activity during campaign","content":[{"type":"text","content":"... long string of text ..."},{"type":"gallery","images":["/path/to/image_1.jpg","/path/to/image_2.jpg","/path/to/image_3.jpg"]}]}]','2016-03-04 16:00:00',NULL);
INSERT INTO `chapter` (`id`,`uid`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`) VALUES (3,'d2d1f3a9-3e5f-448f-b394-87aebc85092e','Campaign Background','visible','/api/evaluations/248bf0d7-7d47-4330-acac-ff763a61d73e/chapters/d2d1f3a9-3e5f-448f-b394-87aebc85092e','[{"type":"section","title":"Period on period sales performance","content":[{"type":"barchart","source":"/dataset/123.cs","comment":"... long string of text ...","info":"... long string of text ..."}]},{"type":"section","title":"Promotional activity during campaign","content":[{"type":"text","content":"... long string of text ..."},{"type":"gallery","images":["/path/to/image_1.jpg","/path/to/image_2.jpg","/path/to/image_3.jpg"]}]}]','2016-03-04 16:00:00',NULL);
INSERT INTO `chapter` (`id`,`uid`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`) VALUES (4,'da272b47-6966-4880-b8ad-9ddad624e223','Campaign Background','visible','/api/evaluations/da272b47-6966-4880-b8ad-9ddad624e223/chapters/da272b47-6966-4880-b8ad-9ddad624e223','[{"type":"section","title":"Period on period sales performance","content":[{"type":"barchart","source":"/dataset/123.cs","comment":"... long string of text ...","info":"... long string of text ..."}]},{"type":"section","title":"Promotional activity during campaign","content":[{"type":"text","content":"... long string of text ..."},{"type":"gallery","images":["/path/to/image_1.jpg","/path/to/image_2.jpg","/path/to/image_3.jpg"]}]}]','2016-03-04 16:00:00',NULL);
/*
-- Query: SELECT * FROM bap_standard.evaluation
LIMIT 0, 1000

-- Date: 2016-03-04 16:37
*/
INSERT INTO `evaluation` (`id`,`uid`,`title`,`start_date`,`end_date`,`generated_at`,`business_unit_id`,`category`,`brand`,`state`,`cid`) VALUES (1,'0f754e5c-6f77-4d55-a770-9d75d60c82e5','Lindt - Christmas 2015','2015-08-04 00:00:00','2016-01-21 00:00:00','2016-03-04 16:00:00',2,'IMPULSE FOOD','Lindor','draft','i2c1510047a');
INSERT INTO `evaluation` (`id`,`uid`,`title`,`start_date`,`end_date`,`generated_at`,`business_unit_id`,`category`,`brand`,`state`,`cid`) VALUES (2,'248bf0d7-7d47-4330-acac-ff763a61d73e','SCA Plenty Genius Award','2015-08-04 00:00:00','2016-01-12 00:00:00','2016-03-04 16:00:00',3,'HOUSEHOLD & PETCARE','Plenty','draft','i2c1509134a');
INSERT INTO `evaluation` (`id`,`uid`,`title`,`start_date`,`end_date`,`generated_at`,`business_unit_id`,`category`,`brand`,`state`,`cid`) VALUES (3,'da272b47-6966-4880-b8ad-9ddad624e223','Colgate Palmolive - CSPR','2015-07-13 00:00:00','2015-07-13 00:00:00','2016-03-04 16:00:00',3,'BABY & BEAUTY','Colgate','draft','i2c1507187a');

/*
-- Query: SELECT * FROM bap_standard.evaluation_chapters
LIMIT 0, 1000

-- Date: 2016-03-04 16:39
*/
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (1,1);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (1,2);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (2,3);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (3,4);
