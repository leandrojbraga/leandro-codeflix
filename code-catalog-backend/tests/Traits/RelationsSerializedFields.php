<?php

namespace Tests\Traits;

trait RelationsSerializedFields
{   
    protected function getRelationsSerializedFields($relations) {
        $relationsFields = [];
        
        foreach ($relations as $relation) {
            $related = $this->factoryModel->{$relation}()->getRelated();

            $relationsFields[$relation] = [
                '*' => array_merge(
                        ['id'],
                        array_values($related->getFillable()),
                        array_values($related->getDates())
                    )
            ];
        }
        
        return $relationsFields;        
    }
}