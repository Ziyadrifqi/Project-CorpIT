<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'icon', 'url', 'parent_id', 'order_pos', 'is_active'];

    public function getMenusByRole($roleId)
    {
        // Get menu permissions from role_menus table
        $roleMenus = $this->getRoleMenus($roleId);

        // Get all top level menus (both headers and standalone menus)
        $topLevelMenus = $this->where([
            'parent_id' => null,
            'is_active' => 1
        ])
            ->orderBy('order_pos', 'ASC')
            ->findAll();

        $structuredMenus = [];

        foreach ($topLevelMenus as $topMenu) {
            // Get first level children
            $firstLevelMenus = $this->where([
                'parent_id' => $topMenu['id'],
                'is_active' => 1
            ])
                ->orderBy('order_pos', 'ASC')
                ->findAll();

            $hasAccessibleChildren = false;

            // If this is a standalone menu (has URL)
            if (!empty($topMenu['url'])) {
                $topMenu['has_access'] = in_array($topMenu['id'], $roleMenus);
                if ($topMenu['has_access']) {
                    $topMenu['is_standalone'] = true;
                    $structuredMenus[] = $topMenu;
                }
                continue;
            }

            // Process child menus
            foreach ($firstLevelMenus as &$menu) {
                $menu['has_access'] = in_array($menu['id'], $roleMenus);

                // Get submenus
                $subMenus = $this->where([
                    'parent_id' => $menu['id'],
                    'is_active' => 1
                ])
                    ->orderBy('order_pos', 'ASC')
                    ->findAll();

                // Process submenus and check permissions
                $accessibleSubmenus = [];
                foreach ($subMenus as $submenu) {
                    $submenu['has_access'] = in_array($submenu['id'], $roleMenus);
                    if ($submenu['has_access']) {
                        $accessibleSubmenus[] = $submenu;
                        $hasAccessibleChildren = true;
                    }
                }

                if (!empty($accessibleSubmenus)) {
                    $menu['is_dropdown'] = true;
                    $menu['children'] = $accessibleSubmenus;
                    $hasAccessibleChildren = true;
                } else {
                    $menu['is_dropdown'] = false;
                    $menu['children'] = [];
                }

                if ($menu['has_access'] || !empty($accessibleSubmenus)) {
                    $hasAccessibleChildren = true;
                }
            }

            // Only add header if it has accessible children
            if ($hasAccessibleChildren) {
                $topMenu['menus'] = array_filter($firstLevelMenus, function ($menu) {
                    return $menu['has_access'] || !empty($menu['children']);
                });
                $structuredMenus[] = $topMenu;
            }
        }

        return $structuredMenus;
    }

    private function getRoleMenus($roleId)
    {
        // Get menu IDs from role_menus table
        $db = db_connect();
        $builder = $db->table('role_menus');
        $permissions = $builder->where('role_id', $roleId)
            ->get()
            ->getResultArray();

        return array_column($permissions, 'menu_id');
    }

    public function getCurrentUserRole()
    {
        $user = user();
        if ($user) {
            $groupModel = db_connect()->table('auth_groups_users');
            $userGroup = $groupModel->where('user_id', $user->id)->get()->getRowArray();

            return $userGroup ? $userGroup['group_id'] : null;
        }
        return null;
    }
}
