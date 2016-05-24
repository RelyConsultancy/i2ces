ALTER TABLE `i2c_evaluation` ADD COLUMN temporary_pdf_path VARCHAR(255);
ALTER TABLE `i2c_evaluation` ADD COLUMN pdf_markers VARCHAR(255);
ALTER TABLE `i2c_reimported_evaluation` ADD COLUMN temporary_pdf_path VARCHAR(255);
ALTER TABLE `i2c_reimported_evaluation` ADD COLUMN pdf_markers VARCHAR(255);
DROP TABLE i2c_generate_pdf_queue;


INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('UPEC', 'units');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('GCS_custs', 'customer');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('Known_spend', 'GBP');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('Lapsed_custs', 'customer');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('OTS', 'units');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('SOW', 'percentage');
INSERT INTO `i2c_objective_units` (`metric`, `unit`) VALUES ('Existing_custs', 'customer');