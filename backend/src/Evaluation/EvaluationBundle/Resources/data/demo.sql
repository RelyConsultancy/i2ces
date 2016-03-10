# clear previous data imported at server up
delete from evaluation_chapters where evaluation_id < 10;
delete from evaluation where id < 10;
delete from chapter where id < 10;

/*
-- Query: SELECT * FROM new_i2c.chapter
LIMIT 0, 1000

-- Date: 2016-03-10 20:25
*/
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (414,'campaign_objective','campaign_objective',NULL,'[{"label":"Total Sales Uplift","value":"38315.15","unit":"GBP"},{"label":"Uplift in New Customers","value":"11637.557"},label":Share of Category Uplift","value":"1.8","unit":"ppts"}]',NULL,NULL,1,'campaign_objectives');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (415,'channels','channels','','["Instore Sampling","JS Magazine","Six Sheets"]',NULL,NULL,1,'channels');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (416,'Campaign Background','visible','/api/evaluations/i2c1510047a/chapters/416','[{"type":"section","title":"Background","access":"editable","content":[{"type":"html","value":"<p><\/p>"}]},{"type":"section","title":"Campaign Objectives","access":"editable","content":[{"type":"blocks","items":[{"label":"Acquire new customers","content":"..."},{"label":"Grow total units","content":"..."},{"label":"Overview","content":"..."}]}]},{"type":"section","title":"Timings","access":"readonly","content":[{"type":"list","items":["2015-08-04 - 2015-10-26","2015-10-27 - 2015-12-24","2015-12-25 - 2016-01-21"]}]},{"type":"section","title":"Evaluated Channels","access":"readonly","content":[{"type":"list","items":["Instore Sampling","JS Magazine","Six Sheets"]}]},{"type":"section","title":"Evaluated Cost","access":"readonly","content":[{"type":"list","items":["37600"]}]},{"type":"section","title":"Offer SKUs","access":"readonly","content":[{"type":"text","value":"Please see Product Definitions"}]},{"type":"section","title":"Media Laydown","access":"readonly","content":[{"type":"chart_time_range","items":[{"label":"Instore Sampling","start":"2015-12-06","end":"2015-12-06","marker":"#ccc"},{"label":"JS Magazine","start":"2015-10-27","end":"2015-12-01","marker":"#555"},{"label":"Six Sheets","start":"2015-11-30","end":"2015-12-13","marker":"#ccc"},{"label":"Instore Sampling","start":"2015-12-04","end":"2015-12-04","marker":"#ccc"},{"label":"JS Magazine","start":"2015-12-01","end":"2015-12-24","marker":"#555"},{"label":"Instore Sampling","start":"2015-12-05","end":"2015-12-05","marker":"#ccc"}],"legend":[{"label":"Evaluated media","color":"#ccc"},{"label":"In-Store Promotions","color":"#555"}]}]}]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (417,'Appendix','visible','/api/evaluations/i2c1510047a/chapters/417','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (418,'Category Context','visible','/api/evaluations/i2c1510047a/chapters/418','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (419,'Summary','visible','/api/evaluations/i2c1510047a/chapters/419','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (420,'Objective Review','visible','/api/evaluations/i2c1510047a/chapters/420','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (421,'Sampling Performance','visible','/api/evaluations/i2c1510047a/chapters/421','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (422,'campaign_objective','campaign_objective',NULL,'[{"label":"Total Sales Uplift","value":"38315.15","unit":"GBP"},{"label":"Uplift in New Customers","value":"11637.557"},label":Share of Category Uplift","value":"1.8","unit":"ppts"}]',NULL,NULL,1,'campaign_objectives');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (423,'channels','channels','','["TV Wall","Six Sheets","JS Magazine"]',NULL,NULL,1,'channels');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (424,'Campaign Background','visible','/api/evaluations/i2c1509134a/chapters/424','[{"type":"section","title":"Background","access":"editable","content":[{"type":"html","value":"<p><\/p>"}]},{"type":"section","title":"Campaign Objectives","access":"editable","content":[{"type":"blocks","items":[{"label":"Grow spend per existing customer","content":"..."},{"label":"Overview","content":"..."},{"label":"Retain existing customers","content":"..."},{"label":"Grow total category","content":"..."}]}]},{"type":"section","title":"Timings","access":"readonly","content":[{"type":"list","items":["2015-08-04 - 2015-10-26","2015-10-27 - 2015-12-01","2015-12-02 - 2016-01-12"]}]},{"type":"section","title":"Evaluated Channels","access":"readonly","content":[{"type":"list","items":["TV Wall","Six Sheets","JS Magazine"]}]},{"type":"section","title":"Evaluated Cost","access":"readonly","content":[{"type":"list","items":["42050"]}]},{"type":"section","title":"Offer SKUs","access":"readonly","content":[{"type":"text","value":"Please see Product Definitions"}]},{"type":"section","title":"Media Laydown","access":"readonly","content":[{"type":"chart_time_range","items":[{"label":"TV Wall","start":"2015-10-28","end":"2015-11-17","marker":"#555"},{"label":"Six Sheets","start":"2015-11-02","end":"2015-11-15","marker":"#ccc"},{"label":"JS Magazine","start":"2015-10-27","end":"2015-12-01","marker":"#555"}],"legend":[{"label":"In-Store Promotions","color":"#555"},{"label":"Evaluated media","color":"#ccc"}]}]}]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (425,'Appendix','visible','/api/evaluations/i2c1509134a/chapters/425','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (426,'Category Context','visible','/api/evaluations/i2c1509134a/chapters/426','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (427,'Summary','visible','/api/evaluations/i2c1509134a/chapters/427','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (428,'Objective Review','visible','/api/evaluations/i2c1509134a/chapters/428','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (429,'Sampling Performance','visible','/api/evaluations/i2c1509134a/chapters/429','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (430,'campaign_objective','campaign_objective',NULL,'[{"label":"Total Sales Uplift","value":"38315.15","unit":"GBP"},{"label":"Uplift in New Customers","value":"11637.557"},label":Share of Category Uplift","value":"1.8","unit":"ppts"}]',NULL,NULL,1,'campaign_objectives');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (431,'channels','channels','','["Entrance Gates","Six Sheets","TV Wall"]',NULL,NULL,1,'channels');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (432,'Campaign Background','visible','/api/evaluations/i2c1507187a/chapters/432','[{"type":"section","title":"Background","access":"editable","content":[{"type":"html","value":"<p><\/p>"}]},{"type":"section","title":"Campaign Objectives","access":"editable","content":[{"type":"blocks","items":[{"label":"Grow frequency of shop per customer","content":"..."},{"label":"Acquire new customers","content":"..."},{"label":"Retain new customers (trialists)","content":"..."},{"label":"Overview","content":"..."}]}]},{"type":"section","title":"Timings","access":"readonly","content":[{"type":"list","items":["2015-07-13 - 2015-10-04","2015-10-05 - 2015-10-27","2015-10-28 - 2015-12-08"]}]},{"type":"section","title":"Evaluated Channels","access":"readonly","content":[{"type":"list","items":["Entrance Gates","Six Sheets","TV Wall"]}]},{"type":"section","title":"Evaluated Cost","access":"readonly","content":[{"type":"list","items":["73250"]}]},{"type":"section","title":"Offer SKUs","access":"readonly","content":[{"type":"text","value":"Please see Product Definitions"}]},{"type":"section","title":"Media Laydown","access":"readonly","content":[{"type":"chart_time_range","items":[{"label":"Entrance Gates","start":"2015-10-07","end":"2015-10-27","marker":"#ccc"},{"label":"Six Sheets","start":"2015-10-05","end":"2015-10-18","marker":"#ccc"},{"label":"TV Wall","start":"2015-10-07","end":"2015-10-27","marker":"#555"}],"legend":[{"label":"Evaluated media","color":"#ccc"},{"label":"In-Store Promotions","color":"#555"}]}]}]',NULL,NULL,0,NULL);
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (433,'Appendix','visible','/api/evaluations/i2c1507187a/chapters/433','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (434,'Category Context','visible','/api/evaluations/i2c1507187a/chapters/434','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (435,'Summary','visible','/api/evaluations/i2c1507187a/chapters/435','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (436,'Objective Review','visible','/api/evaluations/i2c1507187a/chapters/436','[]',NULL,NULL,0,'');
INSERT INTO `chapter` (`id`,`title`,`state`,`location`,`content`,`created_at`,`last_modified_at`,`is_additional_data`,`serialized_name`) VALUES (437,'Sampling Performance','visible','/api/evaluations/i2c1507187a/chapters/437','[]',NULL,NULL,0,'');
/*
-- Query: SELECT * FROM new_i2c.evaluation
LIMIT 0, 1000

-- Date: 2016-03-10 20:24
*/
INSERT INTO `evaluation` (`id`,`cid`,`title`,`category`,`brand`,`state`,`start_date`,`end_date`,`generated_at`,`business_unit_id`,`uid`) VALUES (103,'i2c1510047a','Lindt - Christmas 2015','IMPULSE FOOD','Lindor','draft','2015-08-04 00:00:00','2016-01-21 00:00:00','2016-03-10 18:22:35',2,'');
INSERT INTO `evaluation` (`id`,`cid`,`title`,`category`,`brand`,`state`,`start_date`,`end_date`,`generated_at`,`business_unit_id`,`uid`) VALUES (104,'i2c1509134a','SCA Plenty Genius Award','HOUSEHOLD & PETCARE','Plenty','draft','2015-08-04 00:00:00','2016-01-12 00:00:00','2016-03-10 18:22:35',2,'');
INSERT INTO `evaluation` (`id`,`cid`,`title`,`category`,`brand`,`state`,`start_date`,`end_date`,`generated_at`,`business_unit_id`,`uid`) VALUES (105,'i2c1507187a','Colgate Palmolive - CSPR','BABY & BEAUTY','Colgate','draft','2015-07-13 00:00:00','2015-12-08 00:00:00','2016-03-10 18:22:35',3,'');
/*
-- Query: SELECT * FROM new_i2c.evaluation_chapters
LIMIT 0, 1000

-- Date: 2016-03-10 20:25
*/
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (103,414);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (103,415);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (103,416);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (103,418);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (103,419);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (103,420);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (103,421);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (104,422);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (104,423);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (104,424);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (104,426);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (104,427);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (104,428);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (104,429);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (105,430);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (105,431);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (105,432);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (105,434);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (105,435);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (105,436);
INSERT INTO `evaluation_chapters` (`evaluation_id`,`chapter_id`) VALUES (105,437);

