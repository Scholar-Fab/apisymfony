<?php

namespace App\State;

use App\Entity\Tag;
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
        $tagRepository = $this->entityManager->getRepository(Tag::class);
        foreach($data->getListeTags() as $tag){
            $t = $tagRepository->findOneByLabel($tag->getLabel());
            if($t !== null) {
                $data->removeListeTag($tag);
                $data->addListeTag($t);
            }
            else{
                $this->entityManager->persist($tag);
            }
        }
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
