-- AskVerse 1.0
-- Domande, risposte, commenti, voti e tag.

CREATE TABLE `community_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `author_sub` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `accepted_answer_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'published',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_community_questions_uuid` (`uuid`),
  KEY `idx_questions_author` (`author_sub`),
  KEY `idx_questions_status_created` (`status`, `created_at`),
  KEY `idx_questions_accepted` (`accepted_answer_id`),
  FULLTEXT KEY `idx_questions_search` (`title`, `content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `community_question_answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `question_id` bigint(20) unsigned NOT NULL,
  `author_sub` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'published',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_community_answers_uuid` (`uuid`),
  KEY `idx_answers_question` (`question_id`, `status`, `created_at`),
  KEY `idx_answers_author` (`author_sub`),
  CONSTRAINT `fk_community_answers_question`
    FOREIGN KEY (`question_id`)
    REFERENCES `community_questions` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `community_answer_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `answer_id` bigint(20) unsigned NOT NULL,
  `author_sub` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'published',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_community_answer_comments_uuid` (`uuid`),
  KEY `idx_answer_comments_answer` (`answer_id`, `status`, `created_at`),
  KEY `idx_answer_comments_author` (`author_sub`),
  CONSTRAINT `fk_community_answer_comments_answer`
    FOREIGN KEY (`answer_id`)
    REFERENCES `community_question_answers` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `community_question_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) unsigned NOT NULL,
  `author_sub` varchar(255) NOT NULL,
  `vote` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_question_vote_user` (`question_id`, `author_sub`),
  KEY `idx_question_votes_author` (`author_sub`),
  CONSTRAINT `fk_question_votes_question`
    FOREIGN KEY (`question_id`)
    REFERENCES `community_questions` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `chk_question_vote`
    CHECK (`vote` IN (-1, 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `community_answer_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `answer_id` bigint(20) unsigned NOT NULL,
  `author_sub` varchar(255) NOT NULL,
  `vote` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_answer_vote_user` (`answer_id`, `author_sub`),
  KEY `idx_answer_votes_author` (`author_sub`),
  CONSTRAINT `fk_answer_votes_answer`
    FOREIGN KEY (`answer_id`)
    REFERENCES `community_question_answers` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `chk_answer_vote`
    CHECK (`vote` IN (-1, 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `community_question_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) unsigned NOT NULL,
  `tag` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_question_tag` (`question_id`, `tag`),
  KEY `idx_question_tags_tag` (`tag`),
  CONSTRAINT `fk_question_tags_question`
    FOREIGN KEY (`question_id`)
    REFERENCES `community_questions` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Una risposta accettata deve appartenere alla domanda.
-- Il vincolo viene aggiunto dopo la creazione di entrambe le tabelle.

ALTER TABLE `community_question_answers`
  ADD UNIQUE KEY `uq_answer_question_id` (`question_id`, `id`);

ALTER TABLE `community_questions`
  ADD CONSTRAINT `fk_question_accepted_answer`
  FOREIGN KEY (`id`, `accepted_answer_id`)
  REFERENCES `community_question_answers` (`question_id`, `id`)
  ON DELETE RESTRICT;
