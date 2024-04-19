<?php

namespace Drupal\video_embed_dacast\Plugin\video_embed_field\Provider;

use Drupal\video_embed_field\ProviderPluginBase;

/**
 * A Dacast provider plugin for video embed field.
 *
 * @VideoEmbedProvider(
 *   id = "Dacast",
 *   title = @Translation("Dacast")
 * )
 */
class Dacast extends ProviderPluginBase {

  /**
   * {@inheritdoc}
   */
  public function renderEmbedCode($width, $height, $autoplay) {
    return [
      '#type' => 'video_embed_iframe',
      '#provider' => 'Dacast',
      '#url' => $this->getVideoUrl(),
      '#query' => [],
      '#attributes' => [
        'id' => sprintf('dacast-video-%s', $this->getVideoId()),
        'name' => sprintf('dacast-video-%s', $this->getVideoId()),
        'width' => $width,
        'height' => $height,
        'frameborder' => '0',
        'allowfullscreen' => 'allowfullscreen',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getRemoteThumbnailUrl() {
    // Get the thumbnail that has been set for this video.
    $request = $this->httpClient->get($this->getVideoUrl());
    if ($request && $request->getStatusCode() == 200) {
      $html = $request->getBody()->getContents();
      // Parsing html begins here.
      $doc = new \DOMDocument();
      @$doc->loadHTML($html);
      $metas = $doc->getElementsByTagName('meta');

      for ($i = 0; $i < $metas->length; $i++) {
        $meta = $metas->item($i);
        if ($meta->getAttribute('property') == 'og:image') {
          return $meta->getAttribute('content');
        }
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function getIdFromInput($input) {
    $domain = self::getDomain();
    if (strpos($input, $domain) !== FALSE) {
      // Prepare the user input for regex.
      $domain = preg_quote($domain);

      $regex = "/(.*)$domain\/vod\/(.+)/";
      preg_match($regex, $input, $matches);

      // Use the entire end part as the ID, including the account ID but make it
      // safe for css etc.
      if ($matches[2]) {
        return str_replace('/', '__', $matches[2]);
      }
    }

    return FALSE;
  }

  /**
   * Return the domain used.
   */
  public static function getDomain() {
    // Can have custom domains.
    return \Drupal::configFactory()->get('video_embed_dacast.settings')->get('domain') ?? 'iframe.dacast.com';
  }

  /**
   * Return the URL for the video.
   */
  public function getVideoUrl() {
    return sprintf('//%s/vod/%s', $this->getDomain(), str_replace('__', '/', $this->getVideoId()));
  }

}
