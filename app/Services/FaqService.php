<?php

declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use PDO;

final class FaqService
{
	public function __construct(
		private Database $db
	) {
	}

	public function all(): array
	{
		$stmt = $this->db->pdo()->query(
			'SELECT *
			 FROM mv_faqs
			 ORDER BY sort_order ASC, id ASC'
		);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function published(): array
	{
		$stmt = $this->db->pdo()->prepare(
			'SELECT *
			 FROM mv_faqs
			 WHERE status = :status
			 ORDER BY sort_order ASC, id ASC'
		);

		$stmt->execute([
			'status' => 'published',
		]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function find(int $id): ?array
	{
		$stmt = $this->db->pdo()->prepare(
			'SELECT *
			 FROM mv_faqs
			 WHERE id = :id
			 LIMIT 1'
		);

		$stmt->execute([
			'id' => $id,
		]);

		$faq = $stmt->fetch(PDO::FETCH_ASSOC);

		return $faq !== false ? $faq : null;
	}

	public function findByAnchor(string $anchor): ?array
	{
		$stmt = $this->db->pdo()->prepare(
			'SELECT *
			 FROM mv_faqs
			 WHERE anchor = :anchor
			 LIMIT 1'
		);

		$stmt->execute([
			'anchor' => $anchor,
		]);

		$faq = $stmt->fetch(PDO::FETCH_ASSOC);

		return $faq !== false ? $faq : null;
	}

	public function anchorExists(
		string $anchor,
		?int $excludeId = null
	): bool {
		$sql = 'SELECT COUNT(*)
				FROM mv_faqs
				WHERE anchor = :anchor';

		$params = [
			'anchor' => $anchor,
		];

		if ($excludeId !== null) {
			$sql .= ' AND id <> :exclude_id';

			$params['exclude_id'] = $excludeId;
		}

		$stmt = $this->db->pdo()->prepare($sql);
		$stmt->execute($params);

		return (int) $stmt->fetchColumn() > 0;
	}

	public function create(array $data): int
	{
		$stmt = $this->db->pdo()->prepare(
			'INSERT INTO mv_faqs (
				question,
				anchor,
				answer,
				section,
				status,
				sort_order,
				created_by,
				created_at
			) VALUES (
				:question,
				:anchor,
				:answer,
				:section,
				:status,
				:sort_order,
				:created_by,
				NOW()
			)'
		);

		$stmt->execute([
			'question' => $data['question'],
			'anchor' => $data['anchor'],
			'answer' => $data['answer'],
			'section' => $data['section'] !== ''
				? $data['section']
				: null,
			'status' => $data['status'] ?? 'draft',
			'sort_order' => $data['sort_order'] ?? 0,
			'created_by' => $data['created_by'] ?? null,
		]);

		return (int) $this->db->pdo()->lastInsertId();
	}

	public function update(
		int $id,
		array $data
	): bool {
		$stmt = $this->db->pdo()->prepare(
			'UPDATE mv_faqs
			 SET question = :question,
				 anchor = :anchor,
				 answer = :answer,
				 section = :section,
				 status = :status,
				 sort_order = :sort_order,
				 updated_by = :updated_by,
				 updated_at = NOW()
			 WHERE id = :id'
		);

		return $stmt->execute([
			'id' => $id,
			'question' => $data['question'],
			'anchor' => $data['anchor'],
			'answer' => $data['answer'],
			'section' => $data['section'] !== ''
				? $data['section']
				: null,
			'status' => $data['status'] ?? 'draft',
			'sort_order' => $data['sort_order'] ?? 0,
			'updated_by' => $data['updated_by'] ?? null,
		]);
	}

	public function delete(int $id): bool
	{
		$stmt = $this->db->pdo()->prepare(
			'DELETE FROM mv_faqs
			 WHERE id = :id'
		);

		return $stmt->execute([
			'id' => $id,
		]);
	}
}
