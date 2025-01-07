<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
#[Vich\Uploadable]
class Link
{
    public const TYPE_DEEZER = 'Deezer';
    public const TYPE_FACEBOOK = 'Facebook';
    public const TYPE_INSTAGRAM = 'Instagram';
    public const TYPE_WEB = 'Web';
    public const TYPE_X = 'X';
    public const TYPE_YOUTUBE = 'Youtube';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url(
        message: 'L\'url {{ value }} n\'est pas valide'
    )]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: [self::TYPE_DEEZER, self::TYPE_FACEBOOK, self::TYPE_INSTAGRAM, self::TYPE_WEB, self::TYPE_X, self::TYPE_YOUTUBE], message: "Le type de contenu sélectionné est invalide.")]
    private ?string $type = self::TYPE_DEEZER;

    #[Vich\UploadableField(mapping: 'link_svg', fileNameProperty: 'svgFilename')]
    private ?File $svgFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $svgFilename = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'links', cascade: ['persist', 'remove'])]
    private ?Artist $artist = null;

    public function __toString(): string
    {
        return $this->getType();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

                /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setSvgFile(?File $svgFile = null): void
    {
        $this->svgFile = $svgFile;

        if (null !== $svgFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getSvgFile(): ?File
    {
        return $this->svgFile;
    }

    public function setSvgFilename(?string $svgFilename): void
    {
        $this->svgFilename = $svgFilename;
    }

    public function getSvgFilename(): ?string
    {
        return $this->svgFilename;
    }
}
