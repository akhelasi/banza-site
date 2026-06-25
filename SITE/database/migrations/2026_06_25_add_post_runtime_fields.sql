ALTER TABLE posts
  ADD COLUMN category VARCHAR(120) NULL AFTER body,
  ADD COLUMN display_status VARCHAR(120) NULL AFTER category,
  ADD COLUMN date_label VARCHAR(80) NULL AFTER display_status;
