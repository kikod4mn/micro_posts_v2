<?php

namespace App\Repository;

use App\Entity\ContentCount;
use App\Support\Str;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * @method ContentCount|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContentCount|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContentCount[]    findAll()
 * @method ContentCount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentCountsRepository extends ServiceEntityRepository
{
	/**
	 * Valid sql table column names.
	 */
	private const COLUMNS = [
		'public_micro_post_count', 'public_micro_comment_count', 'public_blog_post_count', 'public_blog_comment_count',
		'public_picture_count', 'public_gallery_count',
	];
	
	/**
	 * ContentCountsRepository constructor.
	 * @param  ManagerRegistry  $registry
	 */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, ContentCount::class);
	}
	
	/**
	 * Get the latest counter from the db and extract the desired column.
	 * @param  string  $table  The  table or entity name. Can be camel, snake or normal writing with spaces between words. Can be literal column name as well.
	 * @return mixed
	 * @throws DBALException
	 */
	public function getLatestPublic(string $table)
	{
		// Format the table name to expected column name. Also convert to snake case if not provided.
		$table = $this->prepareName($table);
		
		// Validate that such a column exists, this is necessary to use dynamic column names.
		// PDO does not accept prepared parameters for table and column names, as such we must manually validate the column name.
		if (! $this->validateName($table)) {
			
			throw new InvalidArgumentException(
				sprintf(
					'No column by the name of "%s" exists in the content counting maintenance table counters. Check ur c0d3!',
					$table
				)
			);
		}
		
		$conn = $this->getEntityManager()->getConnection();
		
		$sql = "SELECT {$table} FROM content_count ORDER BY id DESC LIMIT 1";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->execute();
		
		return $stmt->fetchColumn();
	}
	
	/**
	 * @param  string  $table  The table or entity name. Can be camel, snake or normal writing with spaces between words. Can be literal column name as well.
	 * @return string
	 */
	private function prepareName(string $table): string
	{
		return 'public_' . strtolower(Str::snake($table)) . '_count';
	}
	
	private function validateName(string $table): bool
	{
		return in_array($table, self::COLUMNS);
	}
	
	/**
	 * Get all counters in an associated array.
	 * @return int[]
	 */
	public function getCounts()
	{
		return [
			'microPost'    => (int) $this->countPublicMicroPosts()['count'],
			'microComment' => (int) $this->countPublicMicroComments()['count'],
			'blogPost'     => (int) $this->countPublicBlogPosts()['count'],
			'blogComment'  => (int) $this->countPublicBlogComments()['count'],
			'picture'      => (int) $this->countPublicPictures()['count'],
			'gallery'      => (int) $this->countPublicGalleries()['count'],
		];
	}
	
	private function countPublicMicroPosts()
	{
		$conn = $this->getEntityManager()->getConnection();
		
		$sql = 'SELECT COUNT(mp.id) as count FROM micro_post as mp WHERE mp.trashed_at IS NULL AND mp.published_at IS NOT NULL';
		
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	private function countPublicMicroComments()
	{
		$conn = $this->getEntityManager()->getConnection();
		
		$sql = 'SELECT COUNT(mc.id) as count FROM micro_comment as mc WHERE mc.trashed_at IS NULL AND mc.published_at IS NOT NULL';
		
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	private function countPublicBlogPosts()
	{
		$conn = $this->getEntityManager()->getConnection();
		
		$sql = 'SELECT COUNT(b.id) as count FROM blog_post as b WHERE b.trashed_at IS NULL AND b.published_at IS NOT NULL';
		
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	private function countPublicBlogComments()
	{
		$conn = $this->getEntityManager()->getConnection();
		
		$sql = 'SELECT COUNT(bc.id) as count FROM blog_comment as bc WHERE bc.trashed_at IS NULL AND bc.published_at IS NOT NULL';
		
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	private function countPublicPictures()
	{
		$conn = $this->getEntityManager()->getConnection();
		
		$sql = 'SELECT COUNT(p.id) as count FROM picture as p WHERE p.trashed_at IS NULL AND p.published_at IS NOT NULL';
		
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	private function countPublicGalleries()
	{
		$conn = $this->getEntityManager()->getConnection();
		
		$sql = 'SELECT COUNT(g.id) as count FROM gallery as g WHERE g.trashed_at IS NULL AND g.published_at IS NOT NULL';
		
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		
		return $stmt->fetch();
	}
}
