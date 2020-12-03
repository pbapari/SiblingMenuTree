<?php

/**
 * Generates the requested menu with a specified parent link as root.
 *
 * @param string $menu_name
 *   The name of the menu.
 * @param string $parent_link_id
 *   The parent link to use as root.
 *
 * @return array
 *   Drupal renderable array of menu.
 */
function generate_menu_tree_from_parent($menu_name, $parent_link_id) {

  $menu_tree = \Drupal::menuTree();
  $parameters = new MenuTreeParameters();
  // $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);
  $mtarray = [];

  $about_page = $parent_link_id == 'menu_link_content:b793fd29-68d5-426b-a34e-ca04b30823d1';

  if (!empty($parent_link_id)) {
    // Having the parent now we set it as starting point to build our custom
    // tree.
    $parameters->setRoot($parent_link_id);
    $parameters->setMaxDepth(3);
    $parameters->excludeRoot();

    $tree = $menu_tree->load($menu_name, $parameters);

    // Optional: Native sort and access checks.
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkNodeAccess'],
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $menu_tree->transform($tree, $manipulators);

    $ignore_list = [];

    if ($about_page) {
      $ignore_list = [
        'menu_link_content:3bb8deae-9b55-46fc-8f2f-4733f84f683a',
        'menu_link_content:07da01b2-6068-48a0-8c35-253074107410',
      ];
    }

    // Finally, build a renderable array.
    foreach ($tree as $item) {
      if (!in_array($item->link->getPluginId(), $ignore_list)) {
        $title = $item->link->getTitle();
        $url_obj = $item->link->getUrlObject();
        $url_string = $url_obj->toString();

        $mtarray[] = [$title, $url_string];
      }
    }
  }
  return $mtarray;
}
