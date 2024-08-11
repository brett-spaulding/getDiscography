<?php

namespace App\Models;

use App\Utils\ImageUrl;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class WebScraper
{
    /**
     * Fallback scrape option for the youtube music search page; in some cases there are no additional artists available
     *
     * @return RemoteWebDriver
     */
    public static function scrapeArtist($driver)
    {
        $response = [];
        $artistContainer = $driver->findElement(WebDriverBy::cssSelector('.main-card-content-container'));
        $artistThumbnail = $artistContainer->findElement(WebDriverBy::cssSelector('img'))->getAttribute('src');
        $artistLink = $artistContainer->findElements(WebDriverBy::cssSelector('a'));
        $artistHref = $artistLink[0]->getAttribute('href');
        $artistName = $artistLink[0]->getAttribute('title');

        // Resize image and save to file, provide path to data
        $imageUrl = ImageUrl::modifyGoogleImageUrl($artistThumbnail);
        $imageFileUrl = ImageUrl::save_img_url($imageUrl, 'artist');

        $data = [
            'name' => $artistName,
            'thumbnail' => $artistThumbnail,
            'url_remote' => $artistHref,
            'image' => $imageFileUrl,
        ];
        $artist_id = Artist::findOrCreateByName($artistName, $data);
        return $artist_id->read();
    }

    /**
     * The first scrape that is attempted; this will return the artists and similar artists per youtube so we can return
     * the users search with additional suggestions, or a list of suggestions if their exact search isn't found.
     *
     * @return RemoteWebDriver
     */
    public static function scrapeArtists($driver)
    {
        $response = [];
        // Click the artist button to force a "structure" of results
        $artistBtnXpath = '//a[@title="Show artist results"]';
        $driver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath($artistBtnXpath))
        );
        $driver->findElement(WebDriverBy::xpath($artistBtnXpath))->click();
        // Youtube has multiple elements with the same ID (Naughty!).  We will give a reasonable analog time to render.
        sleep(5);

        $contentDivs = $driver->findElements(WebDriverBy::cssSelector('#contents'));
        $divCount = 0;
        foreach ($contentDivs as $content) {
            $divCount += 1;
            $artists = $content->findElements(WebDriverBy::xpath('//ytmusic-responsive-list-item-renderer'));
            if ($artists) {
                $resultCap = 6;
                $resultIndex = 0;
                foreach ($artists as $artist) {
                    // There are a bunch of elements with no text in them; just a quick and dirty filter
                    $hasText = $artist->getText();
                    if ($hasText) {
                        $resultIndex += 1;
                        // Artist Details
                        $artistThumbnail = $artist->findElement(WebDriverBy::cssSelector('img'))->getAttribute('src');
                        $artistLink = $artist->findElements(WebDriverBy::cssSelector('a'));
                        $artistHref = $artistLink[0]->getAttribute('href');
                        $artistName = $artistLink[0]->getAttribute('aria-label');

                        // Resize image and save to file, provide path to data
                        $imageUrl = ImageUrl::modifyGoogleImageUrl($artistThumbnail);
                        $imageFileUrl = ImageUrl::save_img_url($imageUrl, 'artist');

                        // Create if we don't have it yet
                        $data = [
                            'name' => $artistName,
                            'thumbnail' => $artistThumbnail,
                            'url_remote' => $artistHref,
                            'image' => $imageFileUrl,
                        ];
                        $artist_id = Artist::findOrCreateByName($artistName, $data);
                        $response[] = $artist_id->read();
                        // Limit the results, there are alot of them
                        if ($resultCap <= $resultIndex) {
                            break;
                        }
                    }
                }
                // There are 4 div#contents returned, one empty and 3 with duplicated info
                break;
            }
        }

        return $response;
    }

    /**
     * Scrape the album data from given artist page, create new album records and queue those records for download
     *
     * @return RemoteWebDriver
     */
    public static function scrapeAlbums($driver, $artist_id): array
    {
        $url = 'https://music.youtube.com/' . $artist_id->url_remote;
        $driver->get($url);
        $response = [];
        $albumBtn = $driver->findElement(WebDriverBy::xpath('//a[text()="Albums"]'));
        if ($albumBtn) {
            $albumBtn->click();
            sleep(3);
            $itemsContainer = $driver->findElements(WebDriverBy::cssSelector('#items'));
            foreach ($itemsContainer as $item) {
                $albumContainers = $item->findElements(WebDriverBy::cssSelector('.ytmusic-grid-renderer'));
                if ($albumContainers) {
                    foreach ($albumContainers as $albumContainer) {
                        $albumLink = $albumContainer->findElement(WebDriverBy::cssSelector('a'));
                        $albumHref = $albumLink->getAttribute('href');
                        $albumTitle = $albumLink->getAttribute('title');
                        $albumThumbnail = $albumLink->findElement(WebDriverBy::cssSelector('img'))->getAttribute('src');

                        // Resize image and save to file, provide path to data
                        $imageUrl = ImageUrl::modifyGoogleImageUrl($albumThumbnail);
                        $imageFileUrl = ImageUrl::save_img_url($imageUrl, 'album');

                        $data = [
                            'name' => $albumTitle,
                            'artist_id' => $artist_id->id,
                            'thumbnail' => $albumThumbnail,
                            'url_remote' => $albumHref,
                            'image' => $imageFileUrl,
                        ];
                        $album_id = Album::findOrCreateByName($artist_id, $albumTitle, $data);

                        $album_queue = new AlbumQueue();
                        $album_queue->enqueue($album_id);
                    }
                }
            }
        }
        return $response;
    }
}
