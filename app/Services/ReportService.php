<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Database;
use Monoverse\Helpers\Uuid;

class ReportService
{
	public function __construct(
		private Database $database
	) {
	}

	public function create(
		string $reporterSub,
		string $targetType,
		string $targetUuid,
		string $reason,
		?string $description = null
	): ?string {
	
		$uuid = Uuid::v4();
	
		$created = $this->database->execute(
			'
			INSERT INTO community_reports
			(
				uuid,
				reporter_sub,
				target_type,
				target_uuid,
				reason,
				description,
				created_at
			)
			VALUES
			(
				?, ?, ?, ?, ?, ?, ?
			)
			',
			[
				$uuid,
				$reporterSub,
				$targetType,
				$targetUuid,
				$reason,
				trim((string) $description) !== ''
					? trim((string) $description)
					: null,
				date('Y-m-d H:i:s'),
			]
		);
	
		return $created ? $uuid : null;
	
	}

	public function alreadyReported(
		string $reporterSub,
		string $targetType,
		string $targetUuid
	): bool {

		return $this->database->fetchOne(
			'
			SELECT id
			FROM community_reports
			WHERE reporter_sub = ?
			  AND target_type = ?
			  AND target_uuid = ?
			  AND status = ?
			LIMIT 1
			',
			[
				$reporterSub,
				$targetType,
				$targetUuid,
				'open',
			]
		) !== false;

	}
	
	public function findAll(): array
	{
		return $this->database->fetchAll(
			'
			SELECT
				r.*,
				p.username AS reporter_username
			FROM community_reports r
			LEFT JOIN profiles p
				ON p.oauth_sub = r.reporter_sub
			ORDER BY
				CASE r.status
					WHEN \'open\' THEN 1
					WHEN \'reviewed\' THEN 2
					WHEN \'closed\' THEN 3
				END,
				r.created_at DESC
			'
		);
	}
	
	public function countOpen(): int
	{
		$result = $this->database->fetchOne(
			'
			SELECT COUNT(*) AS total
			FROM community_reports
			WHERE status = ?
			',
			[
				'open',
			]
		);
	
		if ($result === false) {
			return 0;
		}
	
		return (int) ($result['total'] ?? 0);
	}
	
	public function findByUuid(string $uuid): ?array
	{
		$result = $this->database->fetchOne(
			'
			SELECT
				r.*,
				p.username AS reporter_username,
				reviewer.username AS reviewer_username
			FROM community_reports r
			LEFT JOIN profiles p
				ON p.oauth_sub = r.reporter_sub
			LEFT JOIN profiles reviewer
				ON reviewer.oauth_sub = r.reviewed_by
			WHERE r.uuid = :uuid
			LIMIT 1
			',
			[
				'uuid' => $uuid,
			]
		);
		
		return $result !== false ? $result : null;
	}
	
	public function markReviewed(
		string $uuid,
		string $moderatorSub
	): bool {
		return $this->database->execute(
			'
			UPDATE community_reports
			SET
				status = ?,
				reviewed_by = ?,
				reviewed_at = ?
			WHERE uuid = ?
			AND status = ?
			',
			[
				'reviewed',
				$moderatorSub,
				date('Y-m-d H:i:s'),
				$uuid,
				'open',
			]
		);
	}
	
	public function close(
		string $uuid,
		string $moderatorSub
	): bool {
		return $this->database->execute(
			'
			UPDATE community_reports
			SET
				status = ?,
				reviewed_by = ?,
				reviewed_at = ?
			WHERE uuid = ?
			AND status <> ?
			',
			[
				'closed',
				$moderatorSub,
				date('Y-m-d H:i:s'),
				$uuid,
				'closed',
			]
		);
	}

}
