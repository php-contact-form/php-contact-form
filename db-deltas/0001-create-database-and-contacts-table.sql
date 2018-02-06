CREATE TABLE contacts (
    contact_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(40),
    message VARCHAR(511) NOT NULL,
    email_sent_to VARCHAR(255),
    email_sent_status VARCHAR(255),
    contact_submit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
