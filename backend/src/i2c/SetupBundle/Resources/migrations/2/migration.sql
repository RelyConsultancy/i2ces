ALTER TABLE `i2c_evaluation` ADD COLUMN temporary_pdf_path VARCHAR(255);
ALTER TABLE `i2c_evaluation` ADD COLUMN pdf_markers VARCHAR(255);
ALTER TABLE `i2c_reimported_evaluation` ADD COLUMN temporary_pdf_path VARCHAR(255);
ALTER TABLE `i2c_reimported_evaluation` ADD COLUMN pdf_markers VARCHAR(255);
DROP TABLE i2c_generate_pdf_queue;