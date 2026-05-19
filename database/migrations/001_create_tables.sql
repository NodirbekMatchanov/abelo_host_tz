CREATE TABLE IF NOT EXISTS categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS posts (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    image       VARCHAR(255),
    title       VARCHAR(255) NOT NULL,
    description VARCHAR(500) NOT NULL,
    content     TEXT         NOT NULL,
    views_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS post_categories (
    post_id     INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (post_id, category_id),
    INDEX idx_category (category_id),
    CONSTRAINT fk_pc_post     FOREIGN KEY (post_id)     REFERENCES posts(id)      ON DELETE CASCADE,
    CONSTRAINT fk_pc_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
