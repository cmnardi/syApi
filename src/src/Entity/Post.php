<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"list_posts", "detail"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Serializer\Groups({"list_posts", "detail"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Serializer\Groups({"detail"})
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"list_posts", "detail"})
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
