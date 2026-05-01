CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'viewer') NOT NULL DEFAULT 'viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO users (username, email, password, role)
VALUES ('admin', 'admin@localhost', '$2y$10$HhSWUWnYWJCFv4SE4fU7Pey46hqPiQDj/WtXWYV34r8udE4EPOc9y', 'admin');

CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level ENUM('info', 'warning', 'error') NOT NULL DEFAULT 'info',
    event VARCHAR(100) NOT NULL,
    message TEXT,
    user_id INT DEFAULT NULL,
    username VARCHAR(50) DEFAULT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
