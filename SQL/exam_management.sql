
USE exam_management;

CREATE TABLE exams (
    idexam INT AUTO_INCREMENT PRIMARY KEY,
    exam_code VARCHAR(4) NOT NULL,
    exam_name VARCHAR(255) NOT NULL,
    num_questions INT NOT NULL,
    password VARCHAR(255) DEFAULT NULL,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME NOT NULL,
    username INT NOT NULL,
    mix_questions TINYINT(1) DEFAULT 0,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE questions (
    idquestion INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    question_text VARCHAR(255) NOT NULL,
    correct_answer VARCHAR(255) NOT NULL,
    score INT DEFAULT 1,
    mix_answers TINYINT(1) DEFAULT 0,
    FOREIGN KEY (exam_id) REFERENCES exams(idexam)
);

CREATE TABLE choices (
    idchoice INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    choice_text VARCHAR(255) NOT NULL,
    FOREIGN KEY (question_id) REFERENCES questions(idquestion)
);
