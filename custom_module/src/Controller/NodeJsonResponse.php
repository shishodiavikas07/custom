<?php
/**
 * @file
 * Contains \Drupal\custom_module\Controller\NodeJsonResponse.
 */
namespace Drupal\custom_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResult;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * A controller that will return Node object in JSON format.
 */
class NodeJsonResponse extends ControllerBase {

  /**
   * Returns node object as json.
   *
   * @param string $site_api_key
   *   Site information api key.
   *
   * @param string $node_id
   *   Node id.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Node object in JSON format.
   */
  public function content($site_api_key, $node_id) {
    $node = Node::load($node_id);
    $json_array = ['data' => []];
    $json_array['data'][] = [
      'type' => $node->get('type')->target_id,
      'id' => $node->get('nid')->value,
      'attributes' => [
        'title' => $node->get('title')->value,
        'content' => $node->get('body')->value,
      ],
    ];
    return new JsonResponse($json_array);
  }

  /**
   * Checks access for a specific request.
   *
   * @param string $site_api_key
   *   Site information api key.
   *
   * @param string $node_id
   *   Node id.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function access($site_api_key, $node_id) {
    // Check if api key in url argument 2 matches with site information key.
    $site_information_api_key = \Drupal::config('system.site')->get('siteapikey');
    if ($site_information_api_key != $site_api_key) {
      return AccessResult::forbidden();
    }

    $node = Node::load($node_id);
    // Check if node id passed as argument 3 is valid node id.
    if (empty($node)) {
      return AccessResult::forbidden();
    }

    $type = $node->bundle();
    // Check if node id passed as argument 3 is of type page.
    if ($type != 'page') {
      return AccessResult::forbidden();
    }
    return AccessResult::allowed();
  }
}
