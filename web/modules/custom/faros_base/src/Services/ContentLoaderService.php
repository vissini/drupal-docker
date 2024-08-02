<?php

declare(strict_types=1);

namespace Drupal\faros_base\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * @todo Add class description.
 */
final class ContentLoaderService {

  use StringTranslationTrait;

  /**
   * Constructs a ContentLoaderService object.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Retrieves the taxonomy term options for a given vocabulary.
   *
   * @param string $vocabulary The vocabulary machine name.
   * @param bool $include_empty_option (optional) Whether to include an empty option. Default is true.
   * @param string $empty_label (optional) The label for the empty option. Default is 'Select an option'.
   * @return array An array of taxonomy term options, where the keys are term IDs and the values are term names.
   */
  
   public function getTaxonomyTermOptions(string $vocabulary, bool $include_empty_option = true, string $empty_label = 'Select an option'): array {
    $storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $terms = $storage->loadTree($vocabulary);
    $options = [];
  
    if ($include_empty_option) {
      $options[''] = $this->t($empty_label);
    }
  
    foreach ($terms as $term) {
      $options[$term->tid] = $term->name;
    }
  
    return $options;
  }

  /**
   * Retrieves the options for nodes of a given content type.
   *
   * @param string $content_type The content type machine name.
   * @param string $label_field (optional) The field to use as the label. Default is 'title'.
   * @param bool $include_empty_option (optional) Whether to include an empty option. Default is true.
   * @param string $empty_label (optional) The label for the empty option. Default is 'Select an option'.
   * @return array An array of content type options, where the keys are entity IDs and the values are labels.
   */
  public function getContentTypeOptions(string $content_type, string $label_field = 'title', bool $include_empty_option = true, string $empty_label = 'Select an option'): array {
    $storage = $this->entityTypeManager->getStorage('node');
    $query = $storage->getQuery()
      ->condition('type', $content_type)
      ->condition('status', 1)
      ->accessCheck(TRUE); 

    $nids = $query->execute();
    $nodes = $storage->loadMultiple($nids);

    $options = [];
    if ($include_empty_option) {
      $options[''] = $this->t($empty_label);
    }

    foreach ($nodes as $node) {
      $options[$node->id()] = $node->get($label_field)->value;
    }

    return $options;
  }
}
