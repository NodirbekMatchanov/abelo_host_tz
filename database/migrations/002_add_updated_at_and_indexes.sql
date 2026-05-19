-- Add updated_at to categories
ALTER TABLE categories
    ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        AFTER created_at,
    ADD INDEX idx_name       (name),
    ADD INDEX idx_created_at (created_at);

-- Add updated_at and indexes to posts
ALTER TABLE posts
    ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        AFTER created_at,
    ADD INDEX idx_created_at  (created_at),
    ADD INDEX idx_views_count (views_count),
    ADD FULLTEXT INDEX ft_title_desc (title, description);
