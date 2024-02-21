<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class LivreProcessor implements ProcessorInterface
{
    private $entityManager;
    private $slugger;  

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger){
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $data->setSlug($this->slugger->slug(strtolower($data->getTitre())));
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
