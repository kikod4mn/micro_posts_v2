<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\MicroComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MicroComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method MicroComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method MicroComment[]    findAll()
 * @method MicroComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MicroCommentRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, MicroComment::class);
	}
}
