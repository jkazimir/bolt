<?php

namespace Bolt\Storage;

use Bolt\Mapping\ClassMetadata;
use Bolt\Storage\EntityManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;

/**
 * This class prepares an entity instance ready to be persisted to the 
 * database. It consults handlers first before falling back to native doctrine
 * types.
 */
class Persister
{
    
    protected $metadata;
    
    public function __construct(ClassMetadata $metadata)
    {
        $this->metadata = $metadata; 
    }
    
    /**
     *  @param array source data
     * 
     *  @return Object Entity
     */
    public function persist(QuerySet $queries, $entity, EntityManager $em)
    {
    
        foreach ($this->metadata->getFieldMappings() as $key=>$mapping) {
            
            // First step is to allow each Bolt field to transform the data.
            $field = new $mapping['fieldtype']($mapping);
            $field->persist($queries, $entity, $em);
            
            
        }
        
        return $entity;
                
    }
    
    

    
}