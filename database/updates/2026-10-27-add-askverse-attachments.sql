-- AskVerse 1.1
-- Allegati immagine per domande e risposte.
-- Nessuna anteprima: viene conservato il file originale.

CREATE TABLE `community_askverse_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `question_id` bigint(20) unsigned NOT NULL,
  `answer_id` bigint(20) unsigned DEFAULT NULL,
  `author_sub` varchar(255) NOT NULL,
  `storage_path` varchar(500) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(150) NOT NULL,
  `file_size` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),

  PRIMARY KEY (`id`),

  UNIQUE KEY `uq_askverse_attachment_uuid` (`uuid`),

  KEY `idx_askverse_attachment_question` (`question_id`),
  KEY `idx_askverse_attachment_answer` (`question_id`, `answer_id`),
  KEY `idx_askverse_attachment_author` (`author_sub`),

  CONSTRAINT `fk_askverse_attachment_question`
    FOREIGN KEY (`question_id`)
    REFERENCES `community_questions` (`id`)
    ON DELETE CASCADE,

  CONSTRAINT `fk_askverse_attachment_answer`
    FOREIGN KEY (`question_id`, `answer_id`)
    REFERENCES `community_question_answers` (`question_id`, `id`)
    ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
