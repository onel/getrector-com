<?php

declare(strict_types=1);

namespace App\Controller\Socials;

use App\Entity\Post;
use App\Enum\FontFile;
use App\Repository\PostRepository;
use App\Thumbnail\ThumbnailGenerator;
use Illuminate\Routing\Controller;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class PostThumbnailController extends Controller
{
    /**
     * Initializes the controller with required dependencies for image generation and post retrieval.
     */
    public function __construct(
        private readonly Imagine $imagine,
        private readonly PostRepository $postRepository,
        private readonly ThumbnailGenerator $thumbnailGenerator,
    ) {
    }

    /**
     * Resolves the thumbnail image for a post by its lowercased title.
     * Generates the image on-the-fly if it doesn't exist in the filesystem.
     *
     * @param string $lowercasedTitle The lowercased version of the post title used to locate the thumbnail
     * @return BinaryFileResponse The thumbnail image file response
     */
    public function __invoke(string $lowercasedTitle): BinaryFileResponse
    {
        $imageFilePath = $this->thumbnailGenerator->resolveImageFilePath($lowercasedTitle);

        // on the fly
        if (! file_exists($imageFilePath)) {
            $this->createImage($lowercasedTitle, $imageFilePath);
        }

        return response()->file($imageFilePath);
    }

    /**
     * Generates a thumbnail image with post title, author information, and branding.
     * Creates a 2040x1117 canvas, adds the post title in black font, author details in green font,
     * pastes the author's profile picture, and applies the Rector logo before saving to the specified path.
     *
     * @param string $title The lowercased post title to retrieve post data
     * @param string $imageFilePath The filesystem path where the generated image will be saved
     * @return void
     */
    private function createImage(string $title, string $imageFilePath): void
    {
        $box = new Box(2040, 1117);
        $image = $this->imagine->create($box);
        $drawer = $image->draw();

        $blackFont = $this->thumbnailGenerator->createFont(FontFile::SOURCE_SANS_BOLD, '24292e', 100);

        $greenFont = $this->thumbnailGenerator->createFont(FontFile::INTER, '59a35e', 40);

        $post = $this->postRepository->findByLowercasedTitle($title);
        if (! $post instanceof Post) {
            return;
        }

        $drawer->text($post->getTitle(), $blackFont, new Point(130, 340), 0, 1800);

        if ($post->getAuthor() === 'samsonasik') {
            $authorName = 'Abdul Malik Ikhsan';
            $authorPicture = __DIR__ . '/../../../public/assets/images/samsonasik_circle.jpg';
        } elseif ($post->getAuthor() === 'carlos_granados') {
            $authorName = 'Carlos Granados';
            $authorPicture = __DIR__ . '/../../../public/assets/images/carlos_granados_circle.png';
        } else {
            $authorName = 'Tomas Votruba';
            $authorPicture = __DIR__ . '/../../../public/assets/images/tomas_votruba_circle.jpg';
        }

        $drawer->text("Written by \n" . $authorName, $greenFont, new Point(130, 870), 0, 550);

        // add author face
        $faceImage = $this->imagine->open($authorPicture);
        $faceImage->resize(new Box(200, 200));

        $image->paste($faceImage, new Point(1700, 800));

        $this->thumbnailGenerator->addRectorLogo($image);

        $image->save($imageFilePath);
    }
}
