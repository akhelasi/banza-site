ALTER TABLE pages
  ADD COLUMN source_status VARCHAR(40) NOT NULL DEFAULT 'demo' AFTER status,
  ADD COLUMN source_note TEXT NULL AFTER source_status;

ALTER TABLE posts
  ADD COLUMN source_status VARCHAR(40) NOT NULL DEFAULT 'demo' AFTER status,
  ADD COLUMN source_note TEXT NULL AFTER source_status;
